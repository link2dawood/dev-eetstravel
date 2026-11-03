<?php
namespace Tests\Unit;

use App\Status;
use App\Http\Controllers\StatusController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class StatusSafetyTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        config(['database.default' => 'sqlite', 'database.connections.sqlite.database' => ':memory:']); DB::purge('sqlite');
        Schema::create('status', function ($t) {
            $t->increments('id'); $t->string('name'); $t->string('type'); $t->string('color')->nullable();
            $t->integer('sort_order'); $t->boolean('status')->default(0); $t->boolean('is_completed')->default(0); $t->boolean('is_aborted')->default(0); $t->timestamps();
        });
        Schema::create('status_types', function ($t) { $t->increments('id'); $t->string('type'); $t->string('name'); });
        foreach (['task', 'tour', 'bus', 'hotel', 'service_in_tour', 'offer'] as $type) DB::table('status_types')->insert(['type' => $type, 'name' => $type]);
        foreach (['tasks', 'tours', 'tour_packages'] as $table) Schema::create($table, function ($t) { $t->increments('id'); $t->integer('status'); });
        Schema::create('bus_days', function ($t) { $t->increments('id'); $t->integer('status_id'); });
        Schema::create('hotel_offers', function ($t) { $t->increments('id'); $t->integer('tms_status')->nullable(); $t->string('status'); });
        Schema::create('comments', function ($t) { $t->increments('id'); $t->integer('reference_type'); $t->integer('reference_id'); $t->timestamps(); $t->softDeletes(); });
    }
    private function data($overrides = []) { return array_replace(['name' => 'QA Status', 'type' => 'task', 'sort_order' => 1, 'color' => '#336699'], $overrides); }
    private function status($overrides = []) { return Status::create($this->data($overrides)); }
    private function rejects($data, $status = null, $field = null)
    {
        try { (new StatusController)->validateStatus(Request::create('/', 'POST', $data), $status); $this->fail('Expected validation rejection'); }
        catch (ValidationException $e) { $this->assertNotEmpty($e->errors()); if ($field) $this->assertArrayHasKey($field, $e->errors()); }
    }
    public function test_validation_rejects_bad_values_and_duplicate_name_type()
    {
        foreach (['name' => ['', str_repeat('x', 192), '<img src=x>'], 'type' => ['unknown'], 'sort_order' => [-1, -5.5, 2147483648], 'color' => ['red;background-image:url(x)', '<img>', '#zzzzzz'], 'is_completed' => ['invalid']] as $field => $values) {
            foreach ($values as $value) $this->rejects($this->data([$field => $value]), null, $field);
        }
        $status = $this->status();
        $this->rejects($this->data(), null, 'name');
        (new StatusController)->validateStatus(Request::create('/', 'POST', $this->data()), $status);
        (new StatusController)->validateStatus(Request::create('/', 'POST', $this->data(['type' => 'tour'])));
        $this->assertSame(1, Status::count());
    }
    public function test_task_flags_persist_and_cannot_conflict_or_apply_to_other_types()
    {
        DB::table('status')->insert(array_merge($this->data(['name' => 'Sequence seed']), ['id' => 100]));
        DB::table('status')->where('id', 100)->delete();
        $controller = new StatusController;
        $controller->store(Request::create('/', 'POST', $this->data(['is_completed' => 1])));
        $status = Status::first();
        $this->assertEquals(1, $status->is_completed);
        $controller->update(Request::create('/', 'PUT', $this->data(['is_completed' => 0, 'is_aborted' => 1])), $status->id);
        $this->assertEquals(1, $status->fresh()->is_aborted);
        $this->assertEquals(0, $status->fresh()->is_completed);
        $this->rejects($this->data(['is_completed' => 1, 'is_aborted' => 1]), $status, 'is_aborted');
        $this->rejects($this->data(['type' => 'bus', 'is_completed' => 1]), null, 'type');
    }
    public function test_every_used_status_type_blocks_retyping_and_deletion()
    {
        foreach (['task' => ['tasks', 'status'], 'tour' => ['tours', 'status'], 'hotel' => ['tour_packages', 'status'], 'service_in_tour' => ['tour_packages', 'status'], 'bus' => ['bus_days', 'status_id']] as $type => [$table, $column]) {
            $status = $this->status(['name' => 'Used ' . $type, 'type' => $type]);
            // Avoid the reserved IDs so usage, rather than system protection, is exercised.
            DB::table('status')->where('id', $status->id)->update(['id' => 100 + $status->id]); $status->id += 100;
            DB::table($table)->insert([$column => $status->id]);
            $this->assertTrue($status->isInUse());
            $this->rejects($this->data(['name' => $status->name, 'type' => $type === 'task' ? 'tour' : 'task']), $status, 'type');
            $this->assertBlockedDelete($status);
        }
    }
    private function assertBlockedDelete($status)
    {
        app()->instance('request', Request::create('/', 'POST', [], [], [], ['HTTP_ACCEPT' => 'application/json']));
        $response = (new StatusController)->destroy($status->id);
        $this->assertSame(422, $response->getStatusCode());
        $this->assertFalse($response->getData(true)['success']);
        $this->assertNotNull(Status::find($status->id));
    }
    public function test_offer_name_references_block_delete_and_rename()
    {
        DB::table('status')->insert(array_merge($this->data(['name' => 'Offer text', 'type' => 'offer']), ['id' => 100]));
        $status = Status::find(100);
        DB::table('hotel_offers')->insert(['status' => $status->name]);
        $this->assertTrue($status->isInUse()); $this->assertBlockedDelete($status);
        $this->rejects($this->data(['name' => 'Renamed', 'type' => 'offer']), $status, 'name');
    }
    public function test_system_statuses_are_protected_even_when_unused()
    {
        foreach (Status::SYSTEM_IDS as $id) {
            DB::table('status')->insert(array_merge($this->data(['name' => 'System ' . $id]), ['id' => $id]));
            $status = Status::find($id); $this->assertBlockedDelete($status);
            $this->rejects($this->data(['name' => 'Rename ' . $id]), $status, 'name');
            $this->rejects($this->data(['name' => $status->name, 'is_completed' => 1]), $status, 'is_completed');
        }
    }
    public function test_unused_custom_status_deletes_with_its_comments()
    {
        DB::table('status')->insert(array_merge($this->data(), ['id' => 100]));
        DB::table('comments')->insert(['reference_type' => 11, 'reference_id' => 100]);
        (new StatusController)->destroy(100);
        $this->assertNull(Status::find(100)); $this->assertSame(0, DB::table('comments')->whereNull('deleted_at')->count());
    }
    public function test_ajax_show_returns_status_url_and_checks_missing_id()
    {
        $status = $this->status();
        $request = Request::create('/', 'GET', [], [], [], ['HTTP_X_REQUESTED_WITH' => 'XMLHttpRequest']);
        $this->assertSame(route('status.show', $status->id), (new StatusController)->show($status->id, $request));
        $this->expectException(\Illuminate\Database\Eloquent\ModelNotFoundException::class);
        (new StatusController)->show(999999, $request);
    }
    public function test_failed_comment_cleanup_rolls_back_status_deletion()
    {
        DB::table('status')->insert(array_merge($this->data(), ['id' => 100]));
        Schema::drop('comments');
        try { (new StatusController)->destroy(100); $this->fail('Expected cleanup failure'); }
        catch (\Illuminate\Database\QueryException $e) { $this->assertNotNull(Status::find(100)); }
    }
    public function test_older_update_callers_preserve_existing_task_flags()
    {
        DB::table('status')->insert(array_merge($this->data(), ['id' => 7, 'is_completed' => 1]));
        (new StatusController)->update(Request::create('/', 'PUT', $this->data()), 7);
        $this->assertEquals(1, Status::find(7)->is_completed);
    }
    public function test_legacy_update_and_delete_confirmation_permissions()
    {
        foreach (['/status/100/update' => ['POST', 'status.edit'], '/status/100/deleteMsg' => ['GET', 'status.destroy']] as $url => [$method, $permission]) {
            $request = Request::create($url, $method); $route = app('router')->getRoutes()->match($request);
            $this->assertSame($permission, $route->getAction('permissions'));
            $request->setRouteResolver(fn() => $route);
            $request->setUserResolver(fn() => new class { public $id = 1; public function hasPermissionTo($p) { return false; } });
            try { (new \App\Http\Middleware\PermissionsRequiredMiddleware)->handle($request, fn() => response('bypass')); $this->fail('Bypass'); }
            catch (\Symfony\Component\HttpKernel\Exception\HttpException $e) { $this->assertSame(403, $e->getStatusCode()); }
        }
    }
    public function test_legacy_markup_and_css_are_not_rendered_as_active_content()
    {
        $status = new Status(['name' => '<img src=x onerror=alert(1)>', 'type' => 'task', 'sort_order' => 1, 'color' => 'red;background-image:url(x)']);
        $this->assertNull($status->color);
        preg_match('/<tbody>.*?<\/tbody>/s', file_get_contents(resource_path('views/status/show.blade.php')), $match);
        $html = (string) $this->blade($match[0], ['status' => $status, 'status_type' => null]);
        $this->assertStringContainsString(e($status->name), $html); $this->assertStringNotContainsString('<img', $html);
        $this->assertStringNotContainsString('background-image', $html);
    }
}
