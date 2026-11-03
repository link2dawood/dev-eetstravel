<?php
namespace Tests\Unit;

use App\Http\Controllers\ReportingController;
use App\Http\Middleware\PermissionsRequiredMiddleware;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class ReportingSafetyTest extends TestCase
{
    public function test_all_reporting_routes_share_existing_permission()
    {
        foreach (['/reporting', '/reporting/1?service=Hotel', '/reporting_supplier_show?service=Hotel', '/reporting/hotel/1/show', '/reporting/event/1/show', '/reporting/guide/1/show', '/reporting/restaurant/1/show'] as $url) {
            $request = Request::create($url);
            $route = app('router')->getRoutes()->match($request);
            $this->assertSame('reporting.index', $route->getAction('permissions'));
            $request->setRouteResolver(fn () => $route);
            foreach ([true, false] as $allowed) {
                $request->setUserResolver(fn () => new class($allowed) {
                    public $id = 1;
                    private $allowed;
                    public function __construct($allowed) { $this->allowed = $allowed; }
                    public function hasPermissionTo($p) { return $this->allowed && $p === 'reporting.index'; }
                });
                try {
                    $response = (new PermissionsRequiredMiddleware)->handle($request, fn () => response('ok'));
                    $this->assertTrue($allowed);
                    $this->assertSame('ok', $response->getContent());
                } catch (\Symfony\Component\HttpKernel\Exception\HttpException $e) {
                    $this->assertFalse($allowed);
                    $this->assertSame(403, $e->getStatusCode());
                }
            }
        }
    }

    public function test_invalid_filters_fail_before_querying()
    {
        foreach ([['service' => 'Bogus'], ['service' => ['Hotel']], ['q' => ['bad']]] as $input) {
            try {
                (new ReportingController)->index(Request::create('/reporting', 'GET', $input));
                $this->fail('Expected validation failure');
            } catch (ValidationException $e) { $this->assertNotEmpty($e->errors()); }
        }
    }

    public function test_search_wildcards_are_literal_and_pagination_is_retained()
    {
        config(['database.default' => 'sqlite', 'database.connections.sqlite.database' => ':memory:']);
        DB::purge('sqlite');
        foreach (['hotels', 'events', 'restaurants', 'guides'] as $table) {
            Schema::create($table, function ($t) { $t->increments('id'); $t->string('name'); $t->string('city')->nullable(); $t->string('country')->nullable(); $t->softDeletes(); });
        }
        Schema::create('cities', function ($t) { $t->increments('id'); $t->string('name'); });
        Schema::create('countries', function ($t) { $t->string('alias'); $t->string('name'); });
        DB::table('hotels')->insert([['name' => 'Normal'], ['name' => '100% Wild_Card!']]);
        foreach (['%', '_', '!'] as $search) {
            $view = (new ReportingController)->index(Request::create('/reporting', 'GET', ['q' => $search]));
            $rows = $view->getData()['servicesData'];
            $this->assertSame(1, $rows->total());
            $this->assertSame('100% Wild_Card!', $rows->first()->name);
            $this->assertSame(50, $rows->perPage());
        }
        $this->assertSame(0, (new ReportingController)->index(Request::create('/reporting', 'GET', ['service' => 'Event']))->getData()['servicesData']->total());
    }
}
