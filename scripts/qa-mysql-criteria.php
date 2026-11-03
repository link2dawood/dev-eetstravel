<?php
// Local MySQL criteria workflows; fixture writes are rolled back.
require __DIR__ . '/../vendor/autoload.php';
$app = require __DIR__ . '/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Criteria;
use App\Http\Controllers\CriteriaController;
use App\Library\Services\DeleteModel;
if (DB::getDriverName() !== 'mysql') throw new RuntimeException('MySQL required');
$results = [];
$check = function ($name, $fn) use (&$results) {
    try { if ($fn() === false) throw new RuntimeException('Assertion failed'); $results[] = ['check' => $name, 'status' => 'passed']; }
    catch (Throwable $e) { $results[] = ['check' => $name, 'status' => 'failed', 'error' => $e->getMessage()]; }
};
$tables = ['criterias', 'services_has_criterias', 'comments'];
foreach ($tables as $table) {
    $engine = DB::selectOne('SELECT ENGINE AS engine FROM information_schema.TABLES WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = ?', [$table]);
    if (!$engine || $engine->engine !== 'InnoDB') throw new RuntimeException('Transactional table required: ' . $table);
}
$snapshot = fn() => collect($tables)->mapWithKeys(fn($t) => [$t => hash('sha256', serialize(DB::table($t)->orderBy('id')->get()->toArray()))])->all();
$before = $snapshot();
DB::beginTransaction();
try {
    $controller = new CriteriaController;
    $types = DB::table('criteria_types')->orderBy('id')->limit(2)->pluck('id')->all();
    if (count($types) < 2) throw new RuntimeException('Two criteria types required');
    $tag = 'QA MySQL Criteria ' . bin2hex(random_bytes(5));
    $data = ['name' => $tag, 'short_name' => 'QA Wi-Fi', 'icon' => 'wifi', 'criteria_type' => $types[0]];
    $check('Create redirects normally', fn() => $controller->store(Request::create('/', 'POST', $data))->getStatusCode() === 302);
    $criterion = Criteria::where('name', $tag)->firstOrFail();
    $check('Edit excludes own unique name/type', fn() => $controller->update(Request::create('/', 'PUT', $data), $criterion->id)->getStatusCode() === 302);
    $reject = function ($input, $field, $record = null) use ($controller) {
        try { $controller->validateCriteria(Request::create('/', 'POST', $input), $record); }
        catch (Illuminate\Validation\ValidationException $e) { return isset($e->errors()[$field]); }
        return false;
    };
    $check('CT7 duplicate name/type rejected', fn() => $reject($data, 'name'));
    foreach (['criteria_type' => 999999, 'name' => '<script>x</script>', 'short_name' => str_repeat('x', 192), 'icon' => 'wifi" onclick="x'] as $field => $value) $check('Invalid ' . $field . ' rejected', fn() => $reject(array_replace($data, [$field => $value]), $field, $criterion));
    $source = DB::table('services_has_criterias')->first();
    if (!$source) throw new RuntimeException('Existing supplier-link fixture required');
    app()->instance('request', Request::create('/', 'POST', [], [], [], ['HTTP_ACCEPT' => 'application/json']));
    foreach (App\ServicesHasCriteria::$serviceTypes as $service => $type) {
        DB::table('services_has_criterias')->where('id', $source->id)->update(['criteria_id' => $criterion->id, 'service_type' => $type]);
        $check('CT2 deletion blocked for ' . $service . ' link', fn() => $controller->destroy($criterion->id, new DeleteModel)->getStatusCode() === 422 && Criteria::find($criterion->id) !== null);
        $check('CT6 used type change blocked for ' . $service . ' link', fn() => $reject(array_replace($data, ['criteria_type' => $types[1]]), 'criteria_type', $criterion));
    }
    DB::table('services_has_criterias')->where('id', $source->id)->update(['criteria_id' => $source->criteria_id, 'service_type' => $source->service_type]);
    $check('CT8 server pagination and search execute', function () use ($controller, $tag) {
        $rows = $controller->index(Request::create('/criteria', 'GET', ['search' => $tag]))->getData()['criteriaData'];
        return $rows->total() === 1 && $rows->perPage() === 25;
    });
    $check('Unused custom criterion deletes with JSON success', fn() => $controller->destroy($criterion->id, new DeleteModel)->getStatusCode() === 200 && Criteria::find($criterion->id) === null);
} catch (Throwable $e) { $results[] = ['check' => 'Workflow setup', 'status' => 'failed', 'error' => $e->getMessage()]; }
finally { while (DB::transactionLevel()) DB::rollBack(); }
$check('All criteria/link/comment values preserved after rollback', fn() => $before === $snapshot());
file_put_contents(storage_path('app/qa-mysql-criteria-results.json'), json_encode(['date' => date(DATE_ATOM), 'results' => $results], JSON_PRETTY_PRINT));
foreach ($results as $r) echo strtoupper($r['status']) . ' ' . $r['check'] . (isset($r['error']) ? ': ' . $r['error'] : '') . PHP_EOL;
exit(count(array_filter($results, fn($r) => $r['status'] === 'failed')) ? 1 : 0);
