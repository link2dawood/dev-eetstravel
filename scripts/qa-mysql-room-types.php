<?php
// Local MySQL room-type workflows. Every database fixture change is rolled back.
require __DIR__ . '/../vendor/autoload.php';
$app = require __DIR__ . '/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\RoomTypes;
use App\Http\Controllers\RoomTypesController;
if (DB::getDriverName() !== 'mysql') throw new RuntimeException('MySQL required');
$results = [];
$check = function ($name, $fn) use (&$results) {
    try { if ($fn() === false) throw new RuntimeException('Assertion failed'); $results[] = ['check' => $name, 'status' => 'passed']; }
    catch (Throwable $e) { $results[] = ['check' => $name, 'status' => 'failed', 'error' => $e->getMessage()]; }
};
$references = ['hotel_room_types', 'hotel_agreements_room_type_hotels', 'seasons_prices_room_type_hotels', 'prices_room_type_hotels', 'tour_room_type_hotels', 'offer_room_prices'];
$tables = array_merge(['room_types', 'comments'], $references);
foreach ($tables as $table) {
    $row = DB::selectOne('SELECT ENGINE AS engine FROM information_schema.TABLES WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = ?', [$table]);
    if (!$row || $row->engine !== 'InnoDB') throw new RuntimeException('Transactional table required: ' . $table);
}
$snapshot = fn() => collect($tables)->mapWithKeys(fn($t) => [$t => hash('sha256', serialize(DB::table($t)->orderBy('id')->get()->toArray()))])->all();
$before = $snapshot();
DB::beginTransaction();
try {
    $controller = new RoomTypesController;
    $tag = 'QR' . strtoupper(bin2hex(random_bytes(4)));
    $data = ['name' => 'QA MySQL room ' . $tag, 'code' => $tag, 'sort_order' => 99];
    $check('Create custom room type redirects', fn() => $controller->store(Request::create('/room_types', 'POST', $data))->getStatusCode() === 302);
    $room = RoomTypes::where('code', $tag)->firstOrFail();
    $check('Edit custom room type preserves image', function () use ($controller, $room, $data) {
        $beforeImage = $room->img;
        return $controller->update(Request::create('/', 'PUT', array_replace($data, ['name' => $data['name'] . ' edited'])), $room->id)->getStatusCode() === 302 && $room->fresh()->img === $beforeImage;
    });
    $reject = function ($input, $field, $record = null, $files = []) use ($controller) {
        try { $controller->validateRoomTypes(Request::create('/', 'POST', $input, [], $files), $record); }
        catch (Illuminate\Validation\ValidationException $e) { return isset($e->errors()[$field]); }
        return false;
    };
    $data['name'] .= ' edited';
    $check('RT5 duplicate code rejected', fn() => $reject(array_replace($data, ['name' => 'Other ' . $tag]), 'code'));
    $check('RT5 duplicate name rejected', fn() => $reject(array_replace($data, ['code' => 'OTHER']), 'name'));
    $check('RT6 long name rejected', fn() => $reject(array_replace($data, ['name' => str_repeat('x', 192)]), 'name', $room));
    $check('RT9 decimal sort rejected', fn() => $reject(array_replace($data, ['sort_order' => -5.5]), 'sort_order', $room));
    $svg = Illuminate\Http\UploadedFile::fake()->createWithContent('evil.svg', '<svg xmlns="http://www.w3.org/2000/svg"><script>alert(1)</script></svg>');
    $check('RT2 script SVG rejected before storage', fn() => $reject($data, 'image', $room, ['image' => $svg]));
    app()->instance('request', Request::create('/', 'POST', [], [], [], ['HTTP_ACCEPT' => 'application/json']));
    foreach (array_keys(App\TourPackage::$roomsPeopleCount) as $code) {
        $system = RoomTypes::where('code', $code)->firstOrFail();
        $check("RT3 $code code changes rejected", fn() => $reject(['name' => $system->name, 'code' => $tag, 'sort_order' => $system->sort_order], 'code', $system));
        $check("RT3 $code deletion blocked", fn() => $controller->destroy($system->id)->getStatusCode() === 422 && RoomTypes::find($system->id) !== null);
    }
    foreach ($references as $table) {
        $source = DB::table($table)->first();
        if (!$source) {
            $results[] = ['check' => $table . ' live assignment fixture', 'status' => 'not exercised', 'reason' => 'No template row; covered in isolated SQLite tests'];
            continue;
        }
        $link = (array)$source; unset($link['id']); $link['room_type_id'] = $room->id;
        $linkId = DB::table($table)->insertGetId($link);
        $check("Usage guard: $table", fn() => $controller->destroy($room->id)->getStatusCode() === 422 && RoomTypes::find($room->id) !== null);
        DB::table($table)->where('id', $linkId)->delete();
    }
    $check('RT7 unused custom type deletes with JSON success', fn() => $controller->destroy($room->id)->getStatusCode() === 200 && RoomTypes::find($room->id) === null);
    $package = App\TourPackage::firstOrFail();
    $check('RT3 existing package pricing runs', fn() => is_numeric($package->getDoublePrice()) && is_numeric($package->getSinglePrice()));
    $check('RT2 known script asset is outside public folder', fn() => !file_exists(public_path('assets/img/svg/1793195440_6ae1fdb0740c8.svg')) && file_exists(storage_path('app/quarantine/room-types/1793195440_6ae1fdb0740c8.svg')));
} catch (Throwable $e) { $results[] = ['check' => 'Workflow setup', 'status' => 'failed', 'error' => $e->getMessage()]; }
finally { while (DB::transactionLevel()) DB::rollBack(); }
$check('All room/reference/comment values preserved after rollback', fn() => $before === $snapshot());
file_put_contents(storage_path('app/qa-mysql-room-types-results.json'), json_encode(['date' => date(DATE_ATOM), 'results' => $results], JSON_PRETTY_PRINT));
foreach ($results as $r) echo strtoupper($r['status']) . ' ' . $r['check'] . (isset($r['error']) ? ': ' . $r['error'] : '') . PHP_EOL;
exit(count(array_filter($results, fn($r) => $r['status'] === 'failed')) ? 1 : 0);
