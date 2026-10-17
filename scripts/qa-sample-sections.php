<?php

// Local, additive sample-data setup and read-only checks. Never runs migrations.
require __DIR__ . '/../vendor/autoload.php';
$app = require __DIR__ . '/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$connection = config('database.default');
if (!app()->environment('local')
    || !in_array(config('database.connections.' . $connection . '.host'), ['localhost', '127.0.0.1'], true)) {
    throw new RuntimeException('Sample setup is restricted to a local development database.');
}

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

$section = $argv[1] ?? '';
$directory = storage_path('app/sample-data/section-checks');
if (!is_dir($directory)) mkdir($directory, 0775, true);

function requireCheck($condition, $message) {
    if (!$condition) throw new RuntimeException($message);
    echo "PASS: {$message}\n";
}

function rowHashes($table) {
    $hashes = [];
    foreach (DB::table($table)->get() as $row) {
        $hash = hash('sha256', json_encode($row));
        $hashes[$hash] = ($hashes[$hash] ?? 0) + 1;
    }
    return $hashes;
}

function archiveStaleManifest($name) {
    $path = storage_path('app/sample-data/' . $name . '.json');
    if (!is_file($path)) return;
    $ids = json_decode(file_get_contents($path), true, 512, JSON_THROW_ON_ERROR);
    $present = 0;
    foreach ($ids as $table => $values) $present += DB::table($table)->whereIn('id', $values)->count();
    if ($present === 0) {
        $archive = $path . '.stale-' . date('Ymd-His');
        requireCheck(rename($path, $archive), "Archived stale {$name} tracking file; no database records removed");
    }
}

function seedCommand($name, $command, $options = []) {
    archiveStaleManifest($name);
    if (is_file(storage_path('app/sample-data/' . $name . '.json'))) {
        echo "Existing {$name} samples retained.\n";
        return;
    }
    $code = Artisan::call($command, $options);
    echo Artisan::output();
    requireCheck($code === 0, "{$command} completed");
}

function sampleTour() {
    $tour = App\Tour::where('name', 'QA Sample Section Test Tour')->first();
    if ($tour) return $tour;
    return App\Tour::create([
        'name' => 'QA Sample Section Test Tour', 'external_name' => 'QA Sample Section Test Tour',
        'departure_date' => Carbon::today()->addDays(14)->toDateString(),
        'retirement_date' => Carbon::today()->addDays(17)->toDateString(),
        'pax' => 20, 'pax_free' => 0, 'author' => DB::table('users')->min('id'),
        'status' => DB::table('status')->where('type', 'tour')->min('id'),
        'is_quotation' => 0,
    ]);
}

function checkPages($paths) {
    $app = app();
    $kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
    Auth::guard()->setUser(App\User::findOrFail(1));
    $results = [];
    foreach ($paths as $path) {
        $request = Illuminate\Http\Request::create('http://127.0.0.1:8010' . $path);
        $response = $kernel->handle($request);
        $results[$path] = $response->getStatusCode();
        if ($response->getStatusCode() === 403) {
            echo "ACCESS RESTRICTION: existing user cannot open {$path}; permissions unchanged.\n";
            $route = Illuminate\Support\Facades\Route::getRoutes()->match($request);
            $request->setRouteResolver(function () use ($route) { return $route; });
            $rendered = Illuminate\Routing\Router::toResponse($request, $route->run());
            requireCheck($rendered->getStatusCode() === 200, "{$path} controller renders sample data (separate from access check)");
        } else {
            requireCheck($response->getStatusCode() === 200, "GET {$path} returns HTTP 200");
        }
    }
    return $results;
}

if ($section === 'backup') {
    requireCheck(!is_file($directory . '/before.json'), 'New backup baseline');
    $db = DB::connection();
    $config = $db->getConfig();
    $process = new Symfony\Component\Process\Process([
        'F:/xamp/mysql/bin/mysqldump.exe', '--host=' . $config['host'], '--port=' . ($config['port'] ?? 3306),
        '--user=' . $config['username'], '--single-transaction', '--skip-lock-tables', $db->getDatabaseName(),
    ], base_path(), ['MYSQL_PWD' => $config['password']]);
    $process->setTimeout(180);
    $process->mustRun();
    file_put_contents($directory . '/before.sql', $process->getOutput());
    $baseline = [];
    foreach (DB::select('SHOW TABLES') as $row) {
        $table = array_values((array) $row)[0];
        $baseline[$table] = rowHashes($table);
    }
    file_put_contents($directory . '/before.json', json_encode($baseline));
    echo 'Backup saved; baseline covers ' . count($baseline) . " tables.\n";
    exit;
}

requireCheck(is_file($directory . '/before.json'), 'Pre-sample backup exists');
$paths = [];
switch ($section) {
    case 'bus':
        if (!App\Transfer::where('name', 'like', 'QA Sample %')->exists()) {
            $code = Artisan::call('bus:sample-data', ['--standalone' => true]);
            echo Artisan::output();
            requireCheck($code === 0, 'Bus samples created without changing existing tours');
        }
        requireCheck(App\Transfer::where('name', 'like', 'QA Sample %')->count() === 3, '3 sample bus companies');
        requireCheck(App\Bus::where('name', 'like', 'QA Sample %')->count() === 6, '6 sample buses');
        requireCheck(App\Driver::where('name', 'like', 'QA Sample %')->count() === 6, '6 sample drivers');
        $tripIds = App\Trip::where('name', 'like', 'QA Sample %')->pluck('id');
        requireCheck($tripIds->count() === 8, '8 sample calendar trips');
        requireCheck(App\BusDay::whereIn('trip_id', $tripIds)->count() === 21, '21 linked calendar days');
        $paths = ['/transfer', '/bus', '/driver', '/bus/calendar'];
        $paths[] = '/transfer/' . App\Transfer::where('name', 'like', 'QA Sample %')->value('id');
        break;
    case 'base':
        seedCommand('base-input', 'base:sample-data', ['--safe' => true]);
        foreach (['status' => 5, 'room_types' => 3, 'rates' => 3, 'criterias' => 5] as $table => $count) {
            requireCheck(DB::table($table)->where('name', 'like', 'QA Sample %')->count() === $count, "{$count} sample {$table} records");
        }
        $paths = ['/status', '/room_types', '/rate', '/currencies', '/currency_rate', '/criteria'];
        break;
    case 'communication':
        seedCommand('communication', 'communication:sample-data');
        $path = storage_path('app/sample-data/communication.json');
        $ids = json_decode(file_get_contents($path), true);
        if (empty($ids['comments'])) {
            $tour = sampleTour();
            $author = DB::table('users')->min('id');
            $parent = App\Comment::create(['content' => '<p>QA Sample note: please confirm the group itinerary.</p>',
                'author_id' => $author, 'reference_type' => App\Comment::$services['tour'], 'reference_id' => $tour->id, 'status' => 0]);
            $reply = App\Comment::create(['content' => '<p>QA Sample reply: itinerary confirmed.</p>',
                'author_id' => $author, 'reference_type' => App\Comment::$services['tour'], 'reference_id' => $tour->id, 'status' => 0, 'parent' => $parent->id]);
            $ids['comments'] = [$parent->id, $reply->id];
            file_put_contents($path, json_encode($ids, JSON_PRETTY_PRINT));
        }
        requireCheck(count($ids['templates']) === 6, '6 sample email templates');
        requireCheck(DB::table('comments')->whereIn('id', $ids['comments'])->count() >= 2, 'Sample comment thread and reply');
        $paths = ['/templates', '/comment'];
        break;
    case 'clients':
        seedCommand('clients', 'clients:sample-data');
        $ids = json_decode(file_get_contents(storage_path('app/sample-data/clients.json')), true);
        requireCheck(DB::table('clients')->whereIn('id', $ids['clients'])->count() === 4, '4 sample clients');
        requireCheck(DB::table('client_contacts')->whereIn('id', $ids['client_contacts'])->count() === 6, '6 sample contact people');
        $paths = ['/clients?search=QA%20Sample', '/clients/create', '/clients/' . $ids['clients'][0], '/clients/' . $ids['clients'][0] . '/edit'];
        break;
    case 'accounting':
        $tour = sampleTour();
        $clientId = DB::table('clients')->where('name', 'like', 'QA Sample %')->min('id');
        requireCheck((bool) $clientId, 'Accounting samples use a sample client');
        $invoiceIds = DB::transaction(function () use ($tour, $clientId) {
            $ids = [];
            foreach (['UNPAID' => 0, 'PARTIAL' => 100, 'PAID' => 240] as $label => $paid) {
                $note = 'QA Sample accounting ' . $label;
                $existing = App\ClientInvoices::where('note', $note)->first();
                if ($existing) { $ids[$label] = $existing->id; continue; }
                $data = ['currency' => 'EUR', 'office_id' => DB::table('office_fees')->min('id'),
                    'tour_id' => $tour->id, 'client_id' => $clientId, 'note' => $note,
                    'items' => [['item_name' => 'QA Sample group service', 'quantity' => 2, 'amount' => 100, 'vat' => .2]]];
                if ($paid) $data += ['paid_amount' => [$paid], 'paymentdate' => [Carbon::today()->toDateString()], 'payment_method' => ['Bank']];
                (new App\Http\Controllers\ClientInvoiceController())->store(Illuminate\Http\Request::create('/accounting', 'POST', $data));
                $invoice = App\ClientInvoices::where('note', $note)->firstOrFail();
                $invoice->invoice_no = 'QA-SAMPLE-CLIENT-' . $label . '-' . $invoice->id;
                $invoice->save();
                $ids[$label] = $invoice->id;
            }
            return $ids;
        });
        file_put_contents(storage_path('app/sample-data/accounting.json'), json_encode(['client_invoices' => $invoiceIds, 'tour_id' => $tour->id], JSON_PRETTY_PRINT));
        foreach ($invoiceIds as $label => $id) {
            $invoice = App\ClientInvoices::findOrFail($id);
            requireCheck((float) $invoice->amount_receiveable === 240.0, "{$label} invoice total includes 20% VAT");
            $paid = (float) DB::table('transactions')->where('invoice_id', $id)->where('pay_to', 'Client')->sum('amount');
            requireCheck($paid === ['UNPAID' => 0.0, 'PARTIAL' => 100.0, 'PAID' => 240.0][$label], "{$label} payment balance is correct");
            $paths[] = '/accounting/' . $id;
            $paths[] = '/accounting/' . $id . '/edit';
        }
        array_unshift($paths, '/accounting', '/accounting/create');
        break;
    case 'preservation':
        $before = json_decode(file_get_contents($directory . '/before.json'), true);
        foreach ($before as $table => $hashes) {
            $after = rowHashes($table);
            foreach ($hashes as $hash => $count) {
                if (($after[$hash] ?? 0) < $count) throw new RuntimeException("Existing row changed or removed in {$table}");
            }
        }
        echo 'PASS: All original rows across ' . count($before) . " tables are unchanged.\n";
        exit;
    default:
        throw new RuntimeException('Use backup, bus, base, communication, clients, accounting, or preservation.');
}

$results = checkPages($paths);
file_put_contents($directory . '/' . $section . '.json', json_encode($results, JSON_PRETTY_PRINT));
echo "{$section}: sample records and page checks completed.\n";
