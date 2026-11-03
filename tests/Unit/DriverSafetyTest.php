<?php
namespace Tests\Unit;

use App\Driver;
use App\Http\Controllers\DriverController;
use App\Library\Services\DeleteModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Validator;
use Tests\TestCase;

class DriverSafetyTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        config(['database.default' => 'sqlite', 'database.connections.sqlite.database' => ':memory:']);
        DB::purge('sqlite');
        Schema::create('drivers', function ($t) {
            $t->increments('id'); $t->string('name'); $t->string('phone')->default('');
            $t->string('email')->default(''); $t->integer('transfer_id')->nullable(); $t->timestamps();
        });
        Schema::create('transfers', function ($t) { $t->increments('id'); $t->softDeletes(); });
        foreach (['transfer_to_drivers', 'trip_to_drivers'] as $table) {
            Schema::create($table, function ($t) { $t->increments('id'); $t->integer('driver_id'); });
        }
        Schema::create('files', function ($t) { $t->increments('id'); $t->integer('driver_id'); });
        DB::table('transfers')->insert([['id' => 1, 'deleted_at' => null], ['id' => 2, 'deleted_at' => now()]]);
    }

    private function controller() { return app(DriverController::class); }

    public function test_validation_rejects_bad_fields_and_deleted_or_unknown_companies()
    {
        $rules = $this->controller()->validationRules();
        $valid = ['name' => "José d'Orsay", 'phone' => '+49 (123) 456-78', 'email' => 'driver@example.test', 'transfer_id' => 1];
        $this->assertTrue(Validator::make($valid, $rules)->passes());
        foreach (['name' => ['', str_repeat('x', 192), '<script>'], 'phone' => ['abc', '<script>'], 'email' => ['invalid'], 'transfer_id' => [2, 999, 'abc'], 'attach_bus' => ['bad']] as $field => $values) {
            foreach ($values as $value) $this->assertTrue(Validator::make(array_replace($valid, [$field => $value]), $rules)->fails(), $field);
        }
        DB::table('drivers')->insert(['id' => 5, 'name' => 'Driver', 'email' => $valid['email']]);
        $this->assertTrue(Validator::make($valid, $rules)->fails());
        $this->assertTrue(Validator::make($valid, $this->controller()->validationRules(5))->passes());
    }

    public function test_used_driver_is_preserved_and_json_delete_reports_failure()
    {
        DB::table('drivers')->insert(['id' => 5, 'name' => 'Scheduled Driver']);
        foreach (['transfer_to_drivers', 'trip_to_drivers'] as $table) {
            DB::table($table)->insert(['driver_id' => 5]);
            $request = Request::create('/driver/5/delete', 'POST', [], [], [], ['HTTP_ACCEPT' => 'application/json']);
            app()->instance('request', $request);
            $response = $this->controller()->destroy(5, new DeleteModel);
            $this->assertSame(422, $response->getStatusCode());
            $this->assertFalse($response->getData(true)['success']);
            $this->assertNotNull(Driver::find(5));
            DB::table($table)->delete();
        }
    }

    public function test_unused_driver_and_attachment_rows_delete_without_comment_type_error()
    {
        DB::table('drivers')->insert(['id' => 5, 'name' => 'Unused Driver']);
        DB::table('files')->insert(['driver_id' => 5]);
        Driver::withoutEvents(function () {
            $response = $this->controller()->destroy(5, new DeleteModel);
            $this->assertSame(route('driver.index'), $response->getTargetUrl());
        });
        $this->assertNull(Driver::find(5));
        $this->assertSame(0, DB::table('files')->count());
    }

    public function test_failed_delete_rolls_back_attachment_cleanup()
    {
        DB::table('drivers')->insert(['id' => 5, 'name' => 'Driver']);
        DB::table('files')->insert(['driver_id' => 5]);
        $repository = $this->createMock(\App\Repository\Contracts\DriverRepository::class);
        $repository->method('deleteById')->willThrowException(new \RuntimeException('Simulated failure'));
        try {
            (new DriverController($repository))->destroy(5, new DeleteModel);
            $this->fail('Expected failure');
        } catch (\RuntimeException $e) {
            $this->assertSame(1, DB::table('files')->count());
            $this->assertNotNull(Driver::find(5));
        }
    }

    public function test_normal_and_json_saves_return_appropriate_responses()
    {
        Driver::withoutEvents(function () {
            foreach ([false, true] as $json) {
                $request = Request::create('/driver', 'POST', ['name' => 'Valid Driver'], [], [], $json ? ['HTTP_ACCEPT' => 'application/json'] : []);
                $response = $this->controller()->store($request);
                $this->assertSame($json ? 200 : 302, $response->getStatusCode());
                $driver = Driver::orderByDesc('id')->first();
                $this->assertSame('', $driver->email);
                $response = $this->controller()->update($request, $driver->id);
                $this->assertSame($json ? 200 : 302, $response->getStatusCode());
            }
        });
    }

    public function test_lookup_and_delete_confirmation_routes_require_permissions()
    {
        foreach (['/driver_bus_transfer/api/1' => 'transfer.show', '/driver_bus_transfer/table/api/1' => 'transfer.show', '/driver/5/delete_msg' => 'driver.destroy'] as $url => $permission) {
            $route = app('router')->getRoutes()->match(Request::create($url));
            $this->assertSame($permission, $route->getAction('permissions'));
            $request = Request::create($url);
            $request->setRouteResolver(fn() => $route);
            $request->setUserResolver(fn() => new class { public $id = 1; public function hasPermissionTo($p) { return false; } });
            try {
                (new \App\Http\Middleware\PermissionsRequiredMiddleware)->handle($request, fn() => response('forbidden bypass'));
                $this->fail('Permission bypass');
            } catch (\Symfony\Component\HttpKernel\Exception\HttpException $e) {
                $this->assertSame(403, $e->getStatusCode());
            }
            $request->setUserResolver(fn() => new class { public $id = 1; public function hasPermissionTo($p) { return true; } });
            $this->assertSame('allowed', (new \App\Http\Middleware\PermissionsRequiredMiddleware)->handle($request, fn() => response('allowed'))->getContent());
        }
    }

    public function test_detail_fields_escape_legacy_markup_and_missing_company()
    {
        $driver = new Driver(['name' => '<img src=x onerror=alert(1)>', 'phone' => '<script>phone</script>', 'email' => '<b>email</b>']);
        $driver->setRelation('transfer', new \App\Transfer(['name' => '<script>company</script>']));
        preg_match('/<tbody>.*?<\/tbody>/s', file_get_contents(resource_path('views/driver/show.blade.php')), $match);
        $html = (string) $this->blade($match[0], compact('driver'));
        foreach (['name', 'phone', 'email'] as $field) $this->assertStringContainsString(e($driver->$field), $html);
        $this->assertStringContainsString(e($driver->transfer->name), $html);
        $this->assertStringNotContainsString('<script>', $html);
        $driver->setRelation('transfer', null);
        $this->assertStringContainsString('—', (string) $this->blade($match[0], compact('driver')));
    }

    public function test_unsafe_attachment_and_identity_fields_cannot_be_submitted()
    {
        $rules = $this->controller()->validationRules();
        $upload = \Illuminate\Http\UploadedFile::fake()->createWithContent('evil.svg', '<svg><script>alert(1)</script></svg>');
        $this->assertTrue(Validator::make(['name' => 'Driver', 'attach' => [$upload]], $rules)->fails());
        $driver = new Driver(['id' => 999, 'created_at' => '2000-01-01', 'name' => 'Driver']);
        $this->assertNull($driver->id);
        $this->assertNull($driver->created_at);
    }

    public function test_legacy_action_buttons_build_valid_driver_urls()
    {
        $user = $this->createMock(\App\User::class);
        $user->method('can')->willReturn(true);
        \Illuminate\Support\Facades\Auth::setUser($user);
        $driver = new Driver(['name' => 'Driver']); $driver->id = 5;
        $html = $this->controller()->getButton(5, $driver);
        foreach (['/driver/5', '/driver/5/edit', '/driver/5/delete_msg'] as $url) $this->assertStringContainsString($url, $html);
    }
}
