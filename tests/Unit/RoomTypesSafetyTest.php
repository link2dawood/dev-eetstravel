<?php
namespace Tests\Unit;

use App\RoomTypes;
use App\TourPackage;
use App\Http\Controllers\RoomTypesController;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class RoomTypesSafetyTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        config(['database.default' => 'sqlite', 'database.connections.sqlite.database' => ':memory:']); DB::purge('sqlite');
        Schema::create('room_types', function ($t) { $t->increments('id'); $t->string('name'); $t->string('code'); $t->integer('sort_order'); $t->string('img')->default(''); $t->timestamps(); });
        foreach (['hotel_room_types', 'hotel_agreements_room_type_hotels', 'seasons_prices_room_type_hotels', 'prices_room_type_hotels', 'tour_room_type_hotels', 'offer_room_prices'] as $table) {
            Schema::create($table, function ($t) { $t->increments('id'); $t->integer('room_type_id'); });
        }
        Schema::create('comments', function ($t) { $t->increments('id'); $t->integer('reference_type'); $t->integer('reference_id'); $t->timestamps(); $t->softDeletes(); });
    }
    private function data($extra = []) { return array_replace(['name' => 'QA Room', 'code' => 'QAR', 'sort_order' => 1], $extra); }
    private function rejects($data, $room = null, $field = null, $files = [])
    {
        try { (new RoomTypesController)->validateRoomTypes(Request::create('/', 'POST', $data, [], $files), $room); $this->fail('Expected rejection'); }
        catch (ValidationException $e) { $this->assertNotEmpty($e->errors()); if ($field) $this->assertArrayHasKey($field, $e->errors()); }
    }
    public function test_bad_names_codes_sort_orders_and_duplicates_are_rejected()
    {
        foreach (['name' => ['', str_repeat('x', 192), '<script>'], 'code' => ['<img>', str_repeat('X', 11), 'bad code'], 'sort_order' => [-1, 1.5, 2147483648]] as $field => $values) {
            foreach ($values as $value) $this->rejects($this->data([$field => $value]), null, $field);
        }
        (new RoomTypesController)->store(Request::create('/', 'POST', $this->data()));
        $room = RoomTypes::first();
        $this->rejects($this->data(['code' => 'OTHER']), null, 'name');
        $this->rejects($this->data(['name' => 'Other']), null, 'code');
        (new RoomTypesController)->validateRoomTypes(Request::create('/', 'POST', $this->data()), $room);
        $this->assertSame(1, RoomTypes::count());
    }
    public function test_svg_and_svg_disguised_as_png_are_rejected()
    {
        foreach (['evil.svg', 'evil.png'] as $name) {
            $image = UploadedFile::fake()->createWithContent($name, '<svg xmlns="http://www.w3.org/2000/svg"><script>alert(1)</script></svg>');
            $this->rejects($this->data(), null, 'image', ['image' => $image]);
        }
        $png = UploadedFile::fake()->createWithContent('icon.png', base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mP8/x8AAwMCAO+jRZkAAAAASUVORK5CYII='));
        (new RoomTypesController)->validateRoomTypes(Request::create('/', 'POST', $this->data(), [], ['image' => $png]));
        $this->assertTrue(true);
    }
    public function test_system_codes_cannot_change_or_be_deleted()
    {
        foreach (array_keys(TourPackage::$roomsPeopleCount) as $code) {
            DB::table('room_types')->insert($this->data(['name' => $code, 'code' => $code])); $room = RoomTypes::where('code', $code)->first();
            $this->rejects($this->data(['name' => $code, 'code' => 'NEW']), $room, 'code');
            $this->assertBlocked($room);
        }
    }
    private function assertBlocked($room)
    {
        app()->instance('request', Request::create('/', 'POST', [], [], [], ['HTTP_ACCEPT' => 'application/json']));
        $response = (new RoomTypesController)->destroy($room->id);
        $this->assertSame(422, $response->getStatusCode()); $this->assertFalse($response->getData(true)['success']); $this->assertNotNull(RoomTypes::find($room->id));
    }
    public function test_all_six_reference_tables_protect_room_types()
    {
        DB::table('room_types')->insert($this->data()); $room = RoomTypes::first();
        foreach (['hotel_room_types', 'hotel_agreements_room_type_hotels', 'seasons_prices_room_type_hotels', 'prices_room_type_hotels', 'tour_room_type_hotels', 'offer_room_prices'] as $table) {
            DB::table($table)->insert(['room_type_id' => $room->id]); $this->assertBlocked($room); DB::table($table)->delete();
        }
    }
    public function test_custom_type_updates_without_losing_image_and_deletes_with_comments()
    {
        DB::table('room_types')->insert($this->data(['img' => 'old.png'])); $room = RoomTypes::first();
        (new RoomTypesController)->update(Request::create('/', 'PUT', $this->data(['name' => 'Renamed'])), $room->id);
        $this->assertSame('old.png', $room->fresh()->img);
        DB::table('comments')->insert(['reference_type' => 12, 'reference_id' => $room->id]);
        (new RoomTypesController)->destroy($room->id);
        $this->assertNull(RoomTypes::find($room->id)); $this->assertSame(0, DB::table('comments')->whereNull('deleted_at')->count());
    }
    public function test_failed_comment_cleanup_rolls_back_deletion()
    {
        DB::table('room_types')->insert($this->data()); $room = RoomTypes::first(); Schema::drop('comments');
        try { (new RoomTypesController)->destroy($room->id); $this->fail('Expected failure'); }
        catch (\Illuminate\Database\QueryException $e) { $this->assertNotNull(RoomTypes::find($room->id)); }
    }
    public function test_missing_system_codes_do_not_crash_package_pricing()
    {
        $package = new TourPackage; $package->setRelation('room_types_hotel', collect([(object)['room_type_id' => 99, 'price' => 100]]));
        $this->assertEquals(0, $package->getDoublePrice()); $this->assertEquals(0, $package->getSinglePrice());
        DB::table('room_types')->insert($this->data(['code' => 'DOU'])); $double = RoomTypes::first();
        $package->setRelation('room_types_hotel', collect([(object)['room_type_id' => $double->id, 'price' => 100]]));
        $this->assertEquals(100, $package->getDoublePrice()); $this->assertEquals(100, $package->getSinglePrice());
    }
    public function test_legacy_routes_require_permissions()
    {
        foreach (['/room_types/1/update' => ['POST', 'room_types.edit'], '/room_types/1/deleteMsg' => ['GET', 'room_types.destroy']] as $url => [$method, $permission]) {
            $request = Request::create($url, $method); $route = app('router')->getRoutes()->match($request);
            $this->assertSame($permission, $route->getAction('permissions')); $request->setRouteResolver(fn() => $route);
            $request->setUserResolver(fn() => new class { public $id = 1; public function hasPermissionTo($p) { return false; } });
            try { (new \App\Http\Middleware\PermissionsRequiredMiddleware)->handle($request, fn() => response('bypass')); $this->fail('Bypass'); }
            catch (\Symfony\Component\HttpKernel\Exception\HttpException $e) { $this->assertSame(403, $e->getStatusCode()); }
        }
    }
    public function test_detail_fields_escape_legacy_markup()
    {
        $room_types = new RoomTypes; $room_types->name = '<img src=x>'; $room_types->code = '<script>x</script>'; $room_types->sort_order = 1;
        preg_match('/<tbody>.*?<\/tbody>/s', file_get_contents(resource_path('views/room_types/show.blade.php')), $match);
        $html = (string) $this->blade($match[0], compact('room_types'));
        $this->assertStringContainsString(e($room_types->name), $html); $this->assertStringContainsString(e($room_types->code), $html); $this->assertStringNotContainsString('<script>', $html);
    }
    public function test_legacy_action_buttons_use_correct_resource_parameter()
    {
        $user = $this->createMock(\App\User::class); $user->method('can')->willReturn(true); \Illuminate\Support\Facades\Auth::setUser($user);
        $room = new RoomTypes; $room->id = 10;
        $html = (new RoomTypesController)->getButton(10, $room);
        foreach (['/room_types/10', '/room_types/10/edit', '/room_types/10/deleteMsg'] as $url) $this->assertStringContainsString($url, $html);
    }
}
