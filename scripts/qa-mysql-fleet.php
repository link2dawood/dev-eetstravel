<?php
// Local MySQL integration checks. All fixture writes are rolled back.
require __DIR__ . '/../vendor/autoload.php';
$app = require __DIR__ . '/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;

$results = [];
$check = function ($name, $callback) use (&$results) {
    try {
        if ($callback() === false) throw new RuntimeException('Assertion failed');
        $results[] = ['check' => $name, 'status' => 'passed'];
    } catch (Throwable $e) {
        $results[] = ['check' => $name, 'status' => 'failed', 'error' => $e->getMessage()];
    }
};
if (DB::connection()->getDriverName() !== 'mysql') throw new RuntimeException('MySQL required');
$tables = ['buses', 'transfers', 'bus_days', 'drivers', 'activity_log'];
$counts = fn() => collect($tables)->mapWithKeys(fn($t) => [$t => DB::table($t)->count()])->all();
$before = $counts();
$mode = DB::selectOne('SELECT @@SESSION.sql_mode AS mode')->mode;
DB::statement("SET SESSION sql_mode = 'STRICT_TRANS_TABLES,ONLY_FULL_GROUP_BY,NO_ENGINE_SUBSTITUTION'");
DB::beginTransaction();
try {
    $check('All fixture tables support transactions', function () use ($tables) {
        foreach ($tables as $table) {
            $row = DB::selectOne('SELECT ENGINE AS engine FROM information_schema.TABLES WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = ?', [$table]);
            if (!$row || $row->engine !== 'InnoDB') throw new RuntimeException($table . ' is not InnoDB');
        }
    });
    if (end($results)['status'] !== 'passed') throw new RuntimeException('Cannot safely write fixtures');
    $clone = function ($class, $attributes) {
        $source = $class::query()->firstOrFail();
        return $class::withoutEvents(function () use ($source, $attributes) {
            $copy = $source->replicate();
            $copy->forceFill($attributes)->save();
            return $copy;
        });
    };
    $tag = 'MYSQL-QA-' . bin2hex(random_bytes(4));
    $company = $clone(App\Transfer::class, ['name' => "Müller's $tag", 'work_email' => null]);
    $bus = $clone(App\Bus::class, ['name' => $tag, 'bus_number' => $tag, 'transfer_id' => $company->id, 'capacity' => 49, 'comments' => 'MySQL integration comments']);
    $check('Bus capacity/comments persist in MySQL', fn() => $bus->fresh()->capacity == 49 && $bus->fresh()->comments === 'MySQL integration comments');
    $rules = (new App\Http\Requests\StoreBusRequest)->rules();
    $valid = ['name' => "Müller's Reisen", 'bus_number' => $tag . '-NEW', 'transfer_id' => $company->id, 'capacity' => 49];
    $check('Unicode bus name and active company validate', fn() => Validator::make($valid, $rules)->passes());
    $check('Duplicate active bus number rejected', fn() => Validator::make(array_replace($valid, ['bus_number' => $tag]), $rules)->fails());
    $check('Capacity over 500 rejected', fn() => Validator::make(array_replace($valid, ['capacity' => 501]), $rules)->fails());
    $check('Missing company rejected', fn() => Validator::make(array_replace($valid, ['transfer_id' => 2147483647]), $rules)->fails());
    $guard = new App\Library\Services\DeleteModel;
    $check('Company with active bus cannot be deleted', fn() => strpos($guard->check($company, 'Transfer'), 'cannot be deleted') !== false && $company->fresh() !== null);
    App\Transfer::withoutEvents(fn() => $company->delete());
    $check('Deleted company rejected by bus validation', fn() => Validator::make($valid, $rules)->fails());
    $controller = app(App\Http\Controllers\BusController::class);
    foreach ([['2026-10-01', '2026-12-31'], ['2020-01-01', '2020-01-31'], ['2030-01-01', '2030-02-01']] as [$from, $to]) {
        $check("Calendar query $from to $to with ONLY_FULL_GROUP_BY", function () use ($controller, $from, $to) {
            $response = $controller->getApiBusDays(Request::create('/api/bus-days', 'GET', compact('from', 'to')));
            return $response->getStatusCode() === 200 && json_decode($response->getContent(), true) !== null;
        });
    }
    foreach (['bus', 'driver'] as $kind) {
        $check("Calendar $kind options query", function () use ($controller, $kind) {
            return $controller->calendarOptions(Request::create('/bus/calendar/options', 'GET', ['kind' => $kind, 'page' => 1]))->getStatusCode() === 200;
        });
    }
    $check('Bus list MySQL joins and pagination', fn() => $controller->index(Request::create('/bus'))->getData()['buses']->total() >= 1);
    $check('Transfer list MySQL joins and pagination', fn() => app(App\Http\Controllers\TransferController::class)->index(Request::create('/transfer'))->getData()['transfers']->total() >= 1);
} catch (Throwable $e) {
    $results[] = ['check' => 'Integration setup', 'status' => 'failed', 'error' => $e->getMessage()];
} finally {
    while (DB::transactionLevel() > 0) DB::rollBack();
    DB::statement('SET SESSION sql_mode = ?', [$mode]);
}
$check('Fixture table row counts unchanged after rollback', fn() => $before === $counts());
$report = ['date' => date(DATE_ATOM), 'driver' => 'mysql', 'fixture_writes' => 'rolled back', 'results' => $results];
file_put_contents(__DIR__ . '/../storage/app/qa-mysql-fleet-results.json', json_encode($report, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
foreach ($results as $result) echo strtoupper($result['status']) . ' ' . $result['check'] . (isset($result['error']) ? ': ' . $result['error'] : '') . PHP_EOL;
exit(count(array_filter($results, fn($r) => $r['status'] === 'failed')) ? 1 : 0);
