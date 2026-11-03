<?php
namespace Tests\Unit;

use App\Http\Controllers\ActivitiesController;
use App\Services\ActivityAccess;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class ActivitiesSafetyTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        config(['database.default' => 'sqlite', 'database.connections.sqlite.database' => ':memory:']);
        DB::purge('sqlite');
        Schema::create('users', function ($t) { $t->increments('id'); $t->string('name'); });
        Schema::create('activity_log', function ($t) {
            $t->increments('id'); $t->string('log_name'); $t->text('description');
            $t->integer('causer_id')->nullable(); $t->string('causer_type')->nullable();
            $t->text('properties'); $t->timestamps();
        });
        DB::table('users')->insert([['id' => 1, 'name' => 'Admin'], ['id' => 2, 'name' => 'Agent']]);
        for ($i = 1; $i <= 125; $i++) {
            DB::table('activity_log')->insert([
                'log_name' => $i % 2 ? 'Tour' : 'Client', 'description' => $i === 1 ? '100% Wild_Card!' : 'Entry ' . $i,
                'causer_id' => $i % 2 ? 1 : 2, 'causer_type' => User::class,
                'properties' => json_encode(['link' => 'https://dev.eetstravel.com/tour/' . $i]),
                'created_at' => '2026-10-31 12:00:00', 'updated_at' => '2026-10-31 12:00:00',
            ]);
        }
    }
    private function user($admin = false)
    {
        return new class($admin) {
            public $id; private $admin;
            public function __construct($admin) { $this->admin = $admin; $this->id = $admin ? 1 : 2; }
            public function hasRole($role) { return $this->admin && $role === 'admin'; }
        };
    }
    private function listing($filters = [], $admin = true)
    {
        $request = Request::create('/activities', 'GET', $filters);
        $request->setUserResolver(fn () => $this->user($admin));
        return (new ActivitiesController)->index($request)->getData()['activitiesData'];
    }
    public function test_pagination_preserves_every_event_and_uses_two_queries()
    {
        DB::enableQueryLog(); DB::flushQueryLog();
        $rows = $this->listing();
        $this->assertSame(125, $rows->total()); $this->assertCount(50, $rows);
        $this->assertSame(125, $rows->first()->id);
        $this->assertSame('/tour/125', $rows->first()->safe_link);
        $this->assertCount(2, DB::getQueryLog()); DB::disableQueryLog();
        $this->assertCount(10, $this->listing(['per_page' => 10]));
    }
    public function test_non_admin_scope_cannot_be_widened_by_filters()
    {
        $this->assertSame(62, $this->listing([], false)->total());
        $this->assertSame(0, $this->listing(['user' => 1], false)->total());
        DB::table('activity_log')->insert(['log_name' => 'Tour', 'description' => 'Other actor type', 'causer_id' => 2, 'causer_type' => 'Other', 'properties' => '{}']);
        $this->assertSame(62, ActivityAccess::query($this->user())->count());
    }
    public function test_filters_search_literals_dates_user_and_module()
    {
        foreach (['%', '_', '!'] as $q) $this->assertSame(1, $this->listing(['q' => $q])->total());
        $this->assertSame(62, $this->listing(['user' => 2, 'module' => 'Client'])->total());
        $this->assertSame(125, $this->listing(['from' => '2026-10-31', 'to' => '2026-10-31'])->total());
        $this->assertSame(0, $this->listing(['to' => '2026-10-30'])->total());
        $this->assertSame(125, $this->listing(['q' => 'Agent'])->total() + $this->listing(['q' => 'Admin'])->total());
    }
    public function test_invalid_filters_are_rejected()
    {
        foreach ([['per_page' => 100000], ['user' => -1], ['module' => ['Tour']], ['q' => ['bad']], ['from' => 'bad'], ['from' => '2026-10-31', 'to' => '2026-10-30']] as $filters) {
            try { $this->listing($filters); $this->fail('Expected validation error'); }
            catch (ValidationException $e) { $this->assertNotEmpty($e->errors()); }
        }
    }
    public function test_links_normalize_known_hosts_and_reject_unsafe_urls()
    {
        config(['app.url' => 'https://travel.example']);
        foreach (['/tour/1', 'https://dev.eetstravel.com/tour/1', 'https://travel.example/tour/1'] as $link) {
            $this->assertSame('/tour/1', ActivityAccess::link($link));
        }
        foreach ([null, [], '', 'javascript:alert(1)', '//evil.example/a', 'https://evil.example/a', '/%2fevil.example', '/\\evil', '/%5cevil', " /tour/1", 'https://user@travel.example/tour/1', 'data:text/html,test'] as $link) {
            $this->assertNull(ActivityAccess::link($link));
        }
        $this->assertSame('/tour/1?q=%22#details', ActivityAccess::link('/tour/1?q=%22#details'));
    }
    public function test_dashboard_widget_uses_same_scope_and_link_rules()
    {
        $user = \Mockery::mock(User::class)->makePartial();
        $user->id = 2;
        $user->shouldReceive('hasRole')->with('admin')->andReturn(false);
        \Illuminate\Support\Facades\Auth::setUser($user);
        DB::table('activity_log')->where('id', 124)->update([
            'description' => '<p>Safe <strong>text</strong></p><script>alert(1)</script>',
            'properties' => json_encode(['link' => 'javascript:alert(1)']),
        ]);
        $controller = (new \ReflectionClass(\App\Http\Controllers\Api\DashboardController::class))->newInstanceWithoutConstructor();
        $rows = $controller->getActivities();
        $this->assertCount(5, $rows);
        $this->assertSame(124, $rows->first()->id);
        $this->assertSame('Safe text', $rows->first()->description);
        $this->assertNull($rows->first()->getExtraProperty('link'));
        foreach ($rows as $row) $this->assertSame(2, $row->causer_id);
    }
    public function test_guest_is_redirected_to_login()
    {
        $request = Request::create('/activities');
        $route = app('router')->getRoutes()->match($request);
        $request->setRouteResolver(fn () => $route);
        $response = (new \App\Http\Middleware\PermissionsRequiredMiddleware)->handle($request, fn () => response('private'));
        $this->assertSame(302, $response->getStatusCode());
        $this->assertSame(route('login'), $response->getTargetUrl());
    }
    public function test_activity_rows_escape_description_user_and_link_markup()
    {
        $source = file_get_contents(resource_path('views/activities/index.blade.php'));
        $start = strpos($source, '@forelse($activitiesData as $activity)');
        $end = strpos($source, '@endforelse', $start) + strlen('@endforelse');
        $blade = substr($source, $start, $end - $start);
        $activity = (object)[
            'created_at' => '2026-10-31', 'causer' => '<img src=x onerror=alert(1)>',
            'formatted_description' => '<script>alert(1)</script>',
            'safe_link' => ActivityAccess::link('/tour/1?x="onclick="alert(1)'),
        ];
        $activitiesData = collect([$activity]);
        $__env = app('view');
        $compiled = app('blade.compiler')->compileString($blade);
        ob_start();
        try { eval('?>' . $compiled); $html = ob_get_contents(); }
        finally { ob_end_clean(); }
        $this->assertStringNotContainsString('<script>', $html);
        $this->assertStringNotContainsString('<img', $html);
        $this->assertStringContainsString('&lt;script&gt;', $html);
        $this->assertStringContainsString('href="/tour/1?x=&quot;onclick=&quot;alert(1)"', $html);
    }
    public function test_route_permission_allows_existing_grants_and_denies_others()
    {
        $request = Request::create('/activities'); $route = app('router')->getRoutes()->match($request);
        $this->assertSame('activities_index', $route->getName());
        $this->assertSame('activities.index', $route->getAction('permissions'));
        $request->setRouteResolver(fn () => $route);
        foreach ([true, false] as $grant) {
            $request->setUserResolver(fn () => new class($grant) {
                public $id = 1; private $grant;
                public function __construct($grant) { $this->grant = $grant; }
                public function hasPermissionTo($p) { return $this->grant && $p === 'activities.index'; }
            });
            try {
                $result = (new \App\Http\Middleware\PermissionsRequiredMiddleware)->handle($request, fn () => response('ok'));
                $this->assertTrue($grant); $this->assertSame('ok', $result->getContent());
            } catch (\Symfony\Component\HttpKernel\Exception\HttpException $e) { $this->assertFalse($grant); $this->assertSame(403, $e->getStatusCode()); }
        }
    }
}
