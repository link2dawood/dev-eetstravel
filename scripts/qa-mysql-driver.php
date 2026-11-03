<?php
// Real local MySQL controller workflows; fixture changes are rolled back.
require __DIR__ . '/../vendor/autoload.php';
$app = require __DIR__ . '/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Driver;

if (DB::getDriverName() !== 'mysql') throw new RuntimeException('MySQL required');
$results = [];
$check = function ($name, $fn) use (&$results) {
    try {
        if ($fn() === false) throw new RuntimeException('Assertion failed');
        $results[] = ['check' => $name, 'status' => 'passed'];
    } catch (Throwable $e) { $results[] = ['check' => $name, 'status' => 'failed', 'error' => $e->getMessage()]; }
};
$collations = DB::select("SELECT TABLE_NAME AS name, TABLE_COLLATION AS collation FROM information_schema.TABLES WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME IN ('client_invoices', 'invoices_tours', 'office_fees', 'supplier_invoices')");
$check('DR11 finance tables already use utf8mb4', function () use ($collations) {
    if (count($collations) !== 4) return false;
    foreach ($collations as $row) if (strpos($row->collation, 'utf8mb4') !== 0) return false;
    return true;
});
$tables = ['drivers', 'files', 'trip_to_drivers', 'transfer_to_drivers', 'comments', 'activity_log'];
$counts = fn() => collect($tables)->mapWithKeys(fn($t) => [$t => DB::table($t)->count()])->all();
$before = $counts();
$check('Fixture tables support rollback', function () use ($tables) {
    foreach ($tables as $table) {
        $row = DB::selectOne('SELECT ENGINE AS engine FROM information_schema.TABLES WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = ?', [$table]);
        if (!$row || $row->engine !== 'InnoDB') return false;
    }
    return true;
});
if (end($results)['status'] !== 'passed') throw new RuntimeException('Unsafe fixture engine');
DB::beginTransaction();
try {
    $controller = app(App\Http\Controllers\DriverController::class);
    $company = App\Transfer::firstOrFail();
    $tag = 'qa-driver-' . bin2hex(random_bytes(5));
    $data = ['name' => "José d'Orsay", 'phone' => '+49 (123) 456-78', 'email' => "$tag@example.test", 'transfer_id' => $company->id];
    $request = Request::create('/driver', 'POST', $data);
    $check('DR5 create redirects normal form', fn() => $controller->store($request)->getStatusCode() === 302);
    $driver = Driver::where('email', $data['email'])->firstOrFail();
    $check('Unicode name persists unchanged', fn() => $driver->name === $data['name']);
    $check('DR7 duplicate email rejected', fn() => Illuminate\Support\Facades\Validator::make($data, $controller->validationRules())->fails());
    $request = Request::create('/driver/' . $driver->id, 'PUT', array_replace($data, ['phone' => '+44 12345']));
    $check('DR5 update redirects and preserves own email', fn() => $controller->update($request, $driver->id)->getStatusCode() === 302 && $driver->fresh()->phone === '+44 12345');
    foreach ([['transfer_id' => 2147483647], ['phone' => 'abc'], ['name' => str_repeat('x', 192)], ['name' => '<img src=x onerror=alert(1)>']] as $bad) {
        $check('DR6/DR7 invalid ' . key($bad) . ' rejected by controller', function () use ($controller, $data, $bad) {
            try { $controller->store(Request::create('/driver', 'POST', array_replace($data, $bad))); }
            catch (Illuminate\Validation\ValidationException $e) { return isset($e->errors()[key($bad)]); }
            return false;
        });
    }
    $deletedCompany = App\Transfer::onlyTrashed()->first();
    $check('DR8 deleted company rejected', fn() => $deletedCompany && Illuminate\Support\Facades\Validator::make(array_replace($data, ['transfer_id' => $deletedCompany->id]), $controller->validationRules($driver->id))->fails());
    foreach (['trip_to_drivers', 'transfer_to_drivers'] as $table) {
        $source = DB::table($table)->first();
        if (!$source) throw new RuntimeException('Missing existing link fixture template: ' . $table);
        $link = (array) $source; unset($link['id']); $link['driver_id'] = $driver->id;
        $id = DB::table($table)->insertGetId($link);
        app()->instance('request', Request::create('/driver/' . $driver->id . '/delete', 'POST', [], [], [], ['HTTP_ACCEPT' => 'application/json']));
        $check('DR1 ' . $table . ' assignment blocks deletion', fn() => $controller->destroy($driver->id, new App\Library\Services\DeleteModel)->getStatusCode() === 422 && Driver::find($driver->id) !== null);
        DB::table($table)->where('id', $id)->delete();
    }
    $check('DR2 unused driver deletes without 500', fn() => $controller->destroy($driver->id, new App\Library\Services\DeleteModel)->getStatusCode() === 200 && Driver::find($driver->id) === null);
    $busController = app(App\Http\Controllers\BusController::class);
    foreach (['getDriverAndBusTransfer', 'getDriverAndBusTransferForTable'] as $method) {
        $check('DR4 missing company gives 404: ' . $method, function () use ($busController, $method) {
            try { $busController->$method(2147483647, Request::create('/')); }
            catch (Illuminate\Database\Eloquent\ModelNotFoundException $e) { return true; }
            return false;
        });
    }
} catch (Throwable $e) { $results[] = ['check' => 'Workflow setup', 'status' => 'failed', 'error' => $e->getMessage()]; }
finally { while (DB::transactionLevel()) DB::rollBack(); }
$check('Fixture row counts unchanged after rollback', fn() => $before === $counts());
file_put_contents(__DIR__ . '/../storage/app/qa-mysql-driver-results.json', json_encode(['date' => date(DATE_ATOM), 'collations' => $collations, 'results' => $results], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
foreach ($results as $r) echo strtoupper($r['status']) . ' ' . $r['check'] . (isset($r['error']) ? ': ' . $r['error'] : '') . PHP_EOL;
exit(count(array_filter($results, fn($r) => $r['status'] === 'failed')) ? 1 : 0);
