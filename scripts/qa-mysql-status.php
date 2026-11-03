<?php
// Local status controller integration checks; no fixture writes are committed.
require __DIR__ . '/../vendor/autoload.php';
$app = require __DIR__ . '/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Status;
use App\Http\Controllers\StatusController;
if (DB::getDriverName() !== 'mysql') throw new RuntimeException('MySQL required');
$results = [];
$check = function ($name, $fn) use (&$results) {
    try { if ($fn() === false) throw new RuntimeException('Assertion failed'); $results[] = ['check' => $name, 'status' => 'passed']; }
    catch (Throwable $e) { $results[] = ['check' => $name, 'status' => 'failed', 'error' => $e->getMessage()]; }
};
$tables = ['status', 'tasks', 'hotel_offers', 'comments'];
$snapshot = fn() => collect($tables)->mapWithKeys(fn($t) => [$t => hash('sha256', serialize(DB::table($t)->orderBy('id')->get()->toArray()))])->all();
foreach ($tables as $table) {
    $engine = DB::selectOne('SELECT ENGINE AS engine FROM information_schema.TABLES WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = ?', [$table]);
    if (!$engine || $engine->engine !== 'InnoDB') throw new RuntimeException('Transactional table required: ' . $table);
}
$before = $snapshot();
DB::beginTransaction();
try {
    $controller = new StatusController;
    $tag = 'QA Status MySQL ' . bin2hex(random_bytes(5));
    $data = ['name' => $tag, 'type' => 'task', 'sort_order' => 99, 'color' => '#336699', 'is_completed' => 1, 'is_aborted' => 0];
    $check('ST4 task completion persists on create', fn() => $controller->store(Request::create('/status', 'POST', $data))->getStatusCode() === 302 && Status::where('name', $tag)->value('is_completed') == 1);
    $status = Status::where('name', $tag)->firstOrFail();
    $data['is_completed'] = 0; $data['is_aborted'] = 1;
    $check('ST4 task abortion persists on edit', fn() => $controller->update(Request::create('/status', 'PUT', $data), $status->id)->getStatusCode() === 302 && $status->fresh()->is_aborted == 1 && $status->fresh()->is_completed == 0);
    $reject = function ($input, $field, $record = null) use ($controller) {
        try { $controller->validateStatus(Request::create('/status', 'POST', $input), $record); }
        catch (Illuminate\Validation\ValidationException $e) { return isset($e->errors()[$field]); }
        return false;
    };
    foreach (['type' => 'unknown', 'name' => str_repeat('x', 192), 'sort_order' => -5.5, 'color' => 'red;background-image:url(x)'] as $field => $value) {
        $check('ST5/ST9/ST10 invalid ' . $field . ' rejected', fn() => $reject(array_replace($data, [$field => $value]), $field, $status));
    }
    $check('ST8 duplicate name/type rejected', fn() => $reject($data, 'name'));
    $check('ST4 conflicting task flags rejected', fn() => $reject(array_replace($data, ['is_completed' => 1]), 'is_aborted', $status));
    $task = (array) DB::table('tasks')->first(); unset($task['id']); $task['status'] = $status->id;
    DB::table('tasks')->insert($task);
    $check('ST3 used task status cannot change type', fn() => $reject(array_replace($data, ['type' => 'tour', 'is_aborted' => 0]), 'type', $status));
    app()->instance('request', Request::create('/status', 'POST', [], [], [], ['HTTP_ACCEPT' => 'application/json']));
    $check('Used task status deletion blocked', fn() => $controller->destroy($status->id)->getStatusCode() === 422 && Status::find($status->id) !== null);
    $system = Status::findOrFail(2);
    $sys = ['name' => $system->name, 'type' => $system->type, 'sort_order' => $system->sort_order, 'color' => '#336699', 'is_completed' => (int)$system->is_completed, 'is_aborted' => (int)$system->is_aborted];
    $check('ST6 system status rename blocked', fn() => $reject(array_replace($sys, ['name' => $tag . ' rename']), 'name', $system));
    $check('ST6 system status deletion blocked', fn() => $controller->destroy($system->id)->getStatusCode() === 422);
    $offer = Status::create(['name' => $tag . ' offer', 'type' => 'offer', 'sort_order' => 99]);
    $offerRow = (array) DB::table('hotel_offers')->first(); unset($offerRow['id']); $offerRow['status'] = $offer->name;
    DB::table('hotel_offers')->insert($offerRow);
    $check('ST7 offer stored by name blocks deletion', fn() => $controller->destroy($offer->id)->getStatusCode() === 422);
    $check('ST7 used offer cannot be renamed', fn() => $reject(['name' => $tag . ' rename offer', 'type' => 'offer', 'sort_order' => 99], 'name', $offer));
    $ajax = Request::create('/status', 'GET', [], [], [], ['HTTP_X_REQUESTED_WITH' => 'XMLHttpRequest']);
    $check('ST12 AJAX show resolves status URL', fn() => $controller->show($status->id, $ajax) === route('status.show', $status->id));
    $unused = Status::create(['name' => $tag . ' unused', 'type' => 'task', 'sort_order' => 99]);
    $check('Unused custom status deletes successfully', fn() => $controller->destroy($unused->id)->getStatusCode() === 200 && Status::find($unused->id) === null);
} catch (Throwable $e) { $results[] = ['check' => 'Workflow setup', 'status' => 'failed', 'error' => $e->getMessage()]; }
finally { while (DB::transactionLevel()) DB::rollBack(); }
$check('All status/task/offer/comment row values preserved after rollback', fn() => $before === $snapshot());
file_put_contents(storage_path('app/qa-mysql-status-results.json'), json_encode(['date' => date(DATE_ATOM), 'results' => $results], JSON_PRETTY_PRINT));
foreach ($results as $r) echo strtoupper($r['status']) . ' ' . $r['check'] . (isset($r['error']) ? ': ' . $r['error'] : '') . PHP_EOL;
exit(count(array_filter($results, fn($r) => $r['status'] === 'failed')) ? 1 : 0);
