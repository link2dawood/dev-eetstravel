<?php
namespace Tests\Unit;

use App\Rate;
use App\Http\Controllers\RateController;
use App\Library\Services\DeleteModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class RateSafetyTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        config(['database.default' => 'sqlite', 'database.connections.sqlite.database' => ':memory:']); DB::purge('sqlite');
        Schema::create('rates', function ($t) { $t->increments('id'); $t->string('name'); $t->string('mark'); $t->string('rate_type')->nullable(); $t->integer('sort_order'); $t->timestamps(); });
        Schema::create('rate_types', function ($t) { $t->increments('id'); $t->string('name'); });
        DB::table('rate_types')->insert(['id' => 1, 'name' => 'Hotel stars']);
        foreach (['hotels', 'events', 'restaurants', 'guides', 'transfers'] as $table) Schema::create($table, function ($t) { $t->increments('id'); $t->integer('rate'); $t->softDeletes(); });
        Schema::create('comments', function ($t) { $t->increments('id'); $t->integer('reference_type'); $t->integer('reference_id'); $t->timestamps(); $t->softDeletes(); });
    }
    private function data($extra = []) { return array_replace(['name' => 'QA Rate', 'mark' => '4****+', 'rate_type' => 1, 'sort_order' => 1], $extra); }
    private function rejects($data, $rate = null, $field = null)
    {
        try { (new RateController)->validateRate(Request::create('/', 'POST', $data), $rate); $this->fail('Expected validation failure'); }
        catch (ValidationException $e) { $this->assertNotEmpty($e->errors()); if ($field) $this->assertArrayHasKey($field, $e->errors()); }
    }
    public function test_validation_rejects_bad_names_marks_types_and_sort_orders()
    {
        foreach (['name' => ['', str_repeat('x', 192), '<img src=x>'], 'mark' => ['', str_repeat('*', 192), '<script>'], 'rate_type' => [999999, 'NULL', null, 'abc'], 'sort_order' => [-1, 1.5, 2147483648]] as $field => $values) {
            foreach ($values as $value) $this->rejects($this->data([$field => $value]), null, $field);
        }
        (new RateController)->store(Request::create('/', 'POST', $this->data()));
        $rate = Rate::first();
        $this->rejects($this->data(), null, 'name');
        (new RateController)->validateRate(Request::create('/', 'POST', $this->data()), $rate);
        $this->assertSame('4****+', $rate->mark);
    }
    public function test_each_supplier_table_blocks_deletion_including_archived_records()
    {
        DB::table('rates')->insert($this->data()); $rate = Rate::first();
        app()->instance('request', Request::create('/', 'POST', [], [], [], ['HTTP_ACCEPT' => 'application/json']));
        foreach (['hotels', 'events', 'restaurants', 'guides', 'transfers'] as $table) {
            foreach ([null, now()] as $deletedAt) {
                DB::table($table)->insert(['rate' => $rate->id, 'deleted_at' => $deletedAt]);
                $message = (new DeleteModel)->check($rate, 'Rate');
                $this->assertStringContainsString($table, $message); $this->assertNotNull(Rate::find($rate->id));
                $response = (new RateController)->destroy($rate->id, new DeleteModel);
                $this->assertSame(422, $response->getStatusCode()); $this->assertFalse($response->getData(true)['success']);
                $this->assertNotNull(Rate::find($rate->id)); DB::table($table)->delete();
            }
        }
    }
    public function test_normal_create_update_and_unused_delete_with_comment_cleanup()
    {
        $controller = new RateController;
        $this->assertSame(302, $controller->store(Request::create('/', 'POST', $this->data()))->getStatusCode());
        $rate = Rate::first();
        $this->assertSame(302, $controller->update(Request::create('/', 'PUT', $this->data(['name' => "Müller's rating"])), $rate->id)->getStatusCode());
        $this->assertSame("Müller's rating", $rate->fresh()->name);
        DB::table('comments')->insert(['reference_type' => 13, 'reference_id' => $rate->id]);
        $this->assertSame(302, $controller->destroy($rate->id, new DeleteModel)->getStatusCode());
        $this->assertNull(Rate::find($rate->id)); $this->assertSame(0, DB::table('comments')->whereNull('deleted_at')->count());
    }
    public function test_failed_comment_cleanup_rolls_back_deletion()
    {
        DB::table('rates')->insert($this->data()); $rate = Rate::first(); Schema::drop('comments');
        try { (new RateController)->destroy($rate->id, new DeleteModel); $this->fail('Expected cleanup failure'); }
        catch (\Illuminate\Database\QueryException $e) { $this->assertNotNull(Rate::find($rate->id)); }
    }
    public function test_legacy_update_and_confirmation_routes_require_permissions()
    {
        foreach (['/rate/1/update' => ['POST', 'rate.edit'], '/rate/1/deleteMsg' => ['GET', 'rate.destroy']] as $url => [$method, $permission]) {
            $request = Request::create($url, $method); $route = app('router')->getRoutes()->match($request);
            $this->assertSame($permission, $route->getAction('permissions')); $request->setRouteResolver(fn() => $route);
            $request->setUserResolver(fn() => new class { public $id = 1; public function hasPermissionTo($p) { return false; } });
            try { (new \App\Http\Middleware\PermissionsRequiredMiddleware)->handle($request, fn() => response('bypass')); $this->fail('Bypass'); }
            catch (\Symfony\Component\HttpKernel\Exception\HttpException $e) { $this->assertSame(403, $e->getStatusCode()); }
        }
    }
    public function test_details_escape_legacy_markup_and_label_unclassified_types()
    {
        $rate = new Rate; $rate->name = '<img src=x>'; $rate->mark = '<script>x</script>'; $rate->sort_order = 1;
        preg_match('/<tbody>.*?<\/tbody>/s', file_get_contents(resource_path('views/rate/show.blade.php')), $match);
        $html = (string) $this->blade($match[0], ['rate' => $rate, 'rate_type' => null]);
        $this->assertStringContainsString(e($rate->name), $html); $this->assertStringContainsString(e($rate->mark), $html);
        $this->assertStringNotContainsString('<script>', $html); $this->assertStringContainsString('Unclassified', $html);
    }
    public function test_helper_builds_resource_urls_and_ajax_missing_ids_fail()
    {
        $user = $this->createMock(\App\User::class); $user->method('can')->willReturn(true); \Illuminate\Support\Facades\Auth::setUser($user);
        $rate = new Rate; $rate->id = 10;
        $html = (new RateController)->getButton(10, $rate);
        foreach (['/rate/10', '/rate/10/edit', '/rate/10/deleteMsg'] as $url) $this->assertStringContainsString($url, $html);
        $this->expectException(\Illuminate\Database\Eloquent\ModelNotFoundException::class);
        (new RateController)->show(999999, Request::create('/', 'GET', [], [], [], ['HTTP_X_REQUESTED_WITH' => 'XMLHttpRequest']));
    }
    public function test_normalization_preserves_valid_types_and_other_fields()
    {
        foreach (['1', 'NULL', '', '999999', null] as $index => $type) DB::table('rates')->insert($this->data(['name' => 'Rate ' . $index, 'rate_type' => $type]));
        require_once database_path('migrations/2026_10_30_140000_normalize_invalid_rate_type_references.php');
        $migration = new \NormalizeInvalidRateTypeReferences; $migration->up(); $migration->up();
        $this->assertSame(['1', null, null, null, null], DB::table('rates')->orderBy('id')->pluck('rate_type')->all());
        $this->assertSame(5, DB::table('rates')->where('mark', '4****+')->count());
    }
}
