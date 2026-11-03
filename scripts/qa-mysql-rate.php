<?php
// Local MySQL rate workflows; all fixture changes are rolled back.
require __DIR__ . '/../vendor/autoload.php';
$app = require __DIR__ . '/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Rate;
use App\Http\Controllers\RateController;
use App\Library\Services\DeleteModel;
if (DB::getDriverName() !== 'mysql') throw new RuntimeException('MySQL required');
$results = [];
$check = function ($name, $fn) use (&$results) {
    try { if ($fn() === false) throw new RuntimeException('Assertion failed'); $results[] = ['check' => $name, 'status' => 'passed']; }
    catch (Throwable $e) { $results[] = ['check' => $name, 'status' => 'failed', 'error' => $e->getMessage()]; }
};
$suppliers = ['hotels', 'events', 'restaurants', 'guides', 'transfers'];
$tables = array_merge(['rates', 'comments'], $suppliers);
foreach ($tables as $table) {
    $engine = DB::selectOne('SELECT ENGINE AS engine FROM information_schema.TABLES WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = ?', [$table]);
    if (!$engine || $engine->engine !== 'InnoDB') throw new RuntimeException('Transactional table required: ' . $table);
}
$snapshot = fn() => collect($tables)->mapWithKeys(fn($t) => [$t => hash('sha256', serialize(DB::table($t)->orderBy('id')->get()->toArray()))])->all();
$before = $snapshot();
DB::beginTransaction();
try {
    $controller = new RateController;
    $tag = 'QA MySQL Rate ' . bin2hex(random_bytes(5));
    $type = DB::table('rate_types')->first();
    if (!$type) throw new RuntimeException('Rate type fixture required');
    $data = ['name' => $tag, 'mark' => '4****+', 'rate_type' => $type->id, 'sort_order' => 99];
    $check('Create redirects to rate list', fn() => $controller->store(Request::create('/rate', 'POST', $data))->getStatusCode() === 302);
    $rate = Rate::where('name', $tag)->firstOrFail();
    $data['name'] = "Müller's " . $tag;
    $check('Update persists Unicode name and own unique name', fn() => $controller->update(Request::create('/', 'PUT', $data), $rate->id)->getStatusCode() === 302 && $rate->fresh()->name === $data['name']);
    $check('RA6 duplicate name rejected', function () use ($controller, $data) {
        try { $controller->validateRate(Request::create('/', 'POST', $data)); }
        catch (Illuminate\Validation\ValidationException $e) { return isset($e->errors()['name']); }
        return false;
    });
    foreach (['name' => str_repeat('x', 192), 'mark' => '<script>x</script>', 'rate_type' => 999999, 'sort_order' => -5.5] as $field => $value) {
        $check('Invalid ' . $field . ' rejected', function () use ($controller, $data, $rate, $field, $value) {
            try { $controller->update(Request::create('/', 'PUT', array_replace($data, [$field => $value])), $rate->id); }
            catch (Illuminate\Validation\ValidationException $e) { return isset($e->errors()[$field]); }
            return false;
        });
    }
    app()->instance('request', Request::create('/', 'POST', [], [], [], ['HTTP_ACCEPT' => 'application/json']));
    foreach ($suppliers as $table) {
        $source = DB::table($table)->first();
        if (!$source) throw new RuntimeException('Supplier fixture required for ' . $table);
        DB::table($table)->where('id', $source->id)->update(['rate' => $rate->id]);
        $check('RA1/RA2 deletion blocked by ' . $table, fn() => $controller->destroy($rate->id, new DeleteModel)->getStatusCode() === 422 && Rate::find($rate->id) !== null);
        DB::table($table)->where('id', $source->id)->update(['rate' => $source->rate]);
    }
    $check('Unused rate deletes successfully', fn() => $controller->destroy($rate->id, new DeleteModel)->getStatusCode() === 200 && Rate::find($rate->id) === null);
    $check('Rate list joins and pagination execute', fn() => $controller->index(Request::create('/rate'))->getData()['rates']->total() >= 1);
    $check('RA5 remaining non-null types are valid', function () {
        $valid = DB::table('rate_types')->pluck('id')->map(fn($id) => (string)$id)->all();
        foreach (DB::table('rates')->whereNotNull('rate_type')->pluck('rate_type') as $value) if (!in_array((string)$value, $valid, true)) return false;
        return true;
    });
} catch (Throwable $e) { $results[] = ['check' => 'Workflow setup', 'status' => 'failed', 'error' => $e->getMessage()]; }
finally { while (DB::transactionLevel()) DB::rollBack(); }
$check('All rate/supplier/comment values preserved after rollback', fn() => $before === $snapshot());
file_put_contents(storage_path('app/qa-mysql-rate-results.json'), json_encode(['date' => date(DATE_ATOM), 'results' => $results], JSON_PRETTY_PRINT));
foreach ($results as $r) echo strtoupper($r['status']) . ' ' . $r['check'] . (isset($r['error']) ? ': ' . $r['error'] : '') . PHP_EOL;
exit(count(array_filter($results, fn($r) => $r['status'] === 'failed')) ? 1 : 0);
