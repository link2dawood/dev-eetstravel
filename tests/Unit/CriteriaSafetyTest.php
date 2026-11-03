<?php
namespace Tests\Unit;

use App\Criteria;
use App\Http\Controllers\CriteriaController;
use App\Library\Services\DeleteModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class CriteriaSafetyTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        config(['database.default' => 'sqlite', 'database.connections.sqlite.database' => ':memory:']); DB::purge('sqlite');
        Schema::create('criterias', function ($t) { $t->increments('id'); $t->string('name'); $t->string('short_name'); $t->string('icon')->nullable(); $t->integer('criteria_type'); $t->timestamps(); });
        Schema::create('criteria_types', function ($t) { $t->increments('id'); $t->string('name'); });
        DB::table('criteria_types')->insert([['id' => 1, 'name' => 'Hotel'], ['id' => 2, 'name' => 'Restaurant']]);
        Schema::create('services_has_criterias', function ($t) { $t->increments('id'); $t->integer('service_id'); $t->integer('service_type'); $t->integer('criteria_id'); });
        Schema::create('comments', function ($t) { $t->increments('id'); $t->integer('reference_type'); $t->integer('reference_id'); $t->timestamps(); $t->softDeletes(); });
    }
    private function data($extra = []) { return array_replace(['name' => 'QA Wi-Fi', 'short_name' => 'Wi-Fi', 'icon' => 'wifi', 'criteria_type' => 1], $extra); }
    private function rejects($data, $criteria = null, $field = null)
    {
        try { (new CriteriaController)->validateCriteria(Request::create('/', 'POST', $data), $criteria); $this->fail('Expected validation failure'); }
        catch (ValidationException $e) { $this->assertNotEmpty($e->errors()); if ($field) $this->assertArrayHasKey($field, $e->errors()); }
    }
    public function test_validation_and_name_type_uniqueness()
    {
        foreach (['name' => ['', str_repeat('x', 192), '<img src=x>'], 'short_name' => ['', str_repeat('x', 192), '<script>'], 'icon' => ['star" onclick="x', str_repeat('x', 101)], 'criteria_type' => [999999, 'abc', [1]]] as $field => $values) foreach ($values as $value) $this->rejects($this->data([$field => $value]), null, $field);
        (new CriteriaController)->store(Request::create('/', 'POST', $this->data()));
        $criteria = Criteria::first();
        $this->rejects($this->data(), null, 'name');
        (new CriteriaController)->validateCriteria(Request::create('/', 'POST', $this->data()), $criteria);
        (new CriteriaController)->validateCriteria(Request::create('/', 'POST', $this->data(['criteria_type' => 2])));
        $this->assertSame(1, Criteria::count());
    }
    public function test_each_supplier_link_blocks_delete_and_type_change()
    {
        (new CriteriaController)->store(Request::create('/', 'POST', $this->data())); $criteria = Criteria::first();
        app()->instance('request', Request::create('/', 'POST', [], [], [], ['HTTP_ACCEPT' => 'application/json']));
        foreach (\App\ServicesHasCriteria::$serviceTypes as $type) {
            DB::table('services_has_criterias')->insert(['criteria_id' => $criteria->id, 'service_id' => 42, 'service_type' => $type]);
            $response = (new CriteriaController)->destroy($criteria->id, new DeleteModel);
            $this->assertSame(422, $response->getStatusCode()); $this->assertFalse($response->getData(true)['success']); $this->assertNotNull(Criteria::find($criteria->id));
            $this->rejects($this->data(['criteria_type' => 2]), $criteria, 'criteria_type');
            DB::table('services_has_criterias')->delete();
        }
    }
    public function test_unused_criterion_can_retype_then_delete_with_comments()
    {
        $controller = new CriteriaController; $controller->store(Request::create('/', 'POST', $this->data())); $criteria = Criteria::first();
        $this->assertSame(302, $controller->update(Request::create('/', 'PUT', $this->data(['criteria_type' => 2])), $criteria->id)->getStatusCode());
        $this->assertEquals(2, $criteria->fresh()->criteria_type);
        DB::table('comments')->insert(['reference_type' => 16, 'reference_id' => $criteria->id]);
        $this->assertSame(302, $controller->destroy($criteria->id, new DeleteModel)->getStatusCode());
        $this->assertNull(Criteria::find($criteria->id)); $this->assertSame(0, DB::table('comments')->whereNull('deleted_at')->count());
    }
    public function test_cleanup_failure_rolls_back_deletion()
    {
        (new CriteriaController)->store(Request::create('/', 'POST', $this->data())); $criteria = Criteria::first(); Schema::drop('comments');
        try { (new CriteriaController)->destroy($criteria->id, new DeleteModel); $this->fail('Expected failure'); }
        catch (\Illuminate\Database\QueryException $e) { $this->assertNotNull(Criteria::find($criteria->id)); }
    }
    public function test_list_paginates_searches_and_keeps_search_on_next_page()
    {
        for ($i = 0; $i < 31; $i++) DB::table('criterias')->insert($this->data(['name' => sprintf('Criterion %02d', $i)]));
        $controller = new CriteriaController;
        $request = Request::create('/criteria', 'GET', ['search' => 'Criterion']); app()->instance('request', $request);
        $rows = $controller->index($request)->getData()['criteriaData'];
        $this->assertSame(25, $rows->count()); $this->assertSame(31, $rows->total());
        $this->assertStringContainsString('search=Criterion', $rows->nextPageUrl());
        $this->assertSame(0, $controller->index(Request::create('/', 'GET', ['search' => 'not present']))->getData()['criteriaData']->total());
        $this->assertSame(31, $controller->index(Request::create('/', 'GET', ['search' => 'Hotel']))->getData()['criteriaData']->total());
    }
    public function test_legacy_update_and_confirmation_permissions()
    {
        foreach (['/criteria/1/update' => ['POST', 'criteria.edit'], '/criteria/1/deleteMsg' => ['GET', 'criteria.destroy']] as $url => [$method, $permission]) {
            $request = Request::create($url, $method); $route = app('router')->getRoutes()->match($request);
            $this->assertSame($permission, $route->getAction('permissions')); $request->setRouteResolver(fn() => $route);
            $request->setUserResolver(fn() => new class { public $id = 1; public function hasPermissionTo($p) { return false; } });
            try { (new \App\Http\Middleware\PermissionsRequiredMiddleware)->handle($request, fn() => response('bypass')); $this->fail('Bypass'); }
            catch (\Symfony\Component\HttpKernel\Exception\HttpException $e) { $this->assertSame(403, $e->getStatusCode()); }
        }
    }
    public function test_details_and_supplier_report_fragments_escape_legacy_markup()
    {
        $criteria = new Criteria;
        foreach (['name', 'short_name', 'icon'] as $field) $criteria->$field = '<script>' . $field . '</script>';
        preg_match('/<tbody>.*?<\/tbody>/s', file_get_contents(resource_path('views/criteria/show.blade.php')), $match);
        $html = (string) $this->blade($match[0], ['criteria' => $criteria, 'criteria_type' => null]);
        foreach (['name', 'short_name', 'icon'] as $field) $this->assertStringContainsString(e($criteria->$field), $html);
        $this->assertStringNotContainsString('<script>', $html);
        foreach (['restaurant/show', 'cruises/show', 'flight/show', 'reporting/event_show', 'reporting/guide_show', 'reporting/hotel_show', 'reporting/restaurant_show'] as $view) {
            $source = preg_replace('/\{\{--.*?--\}\}/s', '', file_get_contents(resource_path('views/' . $view . '.blade.php')));
            preg_match('/<td\b[^>]*>\s*\{\{\s*\$criteria->name\s*\}\}\s*<\/td>/s', $source, $fragment);
            $this->assertNotEmpty($fragment, $view);
            $html = (string) $this->blade($fragment[0], compact('criteria'));
            $this->assertStringContainsString(e($criteria->name), $html); $this->assertStringNotContainsString('<script>', $html);
        }
    }
}
