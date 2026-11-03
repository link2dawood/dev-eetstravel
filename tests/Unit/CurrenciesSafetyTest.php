<?php
namespace Tests\Unit;

use App\Currencies;
use App\Http\Controllers\CurrenciesController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class CurrenciesSafetyTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        config(['database.default' => 'sqlite', 'database.connections.sqlite.database' => ':memory:']); DB::purge('sqlite');
        Schema::create('currencies', function ($t) { $t->increments('id'); $t->string('name'); $t->string('code'); $t->string('symbol'); $t->string('cent')->nullable(); $t->string('symbol_cent')->nullable(); $t->timestamps(); });
        foreach (['tour_packages', 'hotel_offers', 'client_invoices', 'currency_rates'] as $table) Schema::create($table, function ($t) { $t->increments('id'); $t->string('currency'); });
        Schema::create('comments', function ($t) { $t->increments('id'); $t->integer('reference_type'); $t->integer('reference_id'); $t->timestamps(); $t->softDeletes(); });
    }
    private function data($extra = []) { return array_replace(['name' => 'QA Currency', 'code' => 'QAA', 'symbol' => '€', 'cent' => 'cent', 'symbol_cent' => '100'], $extra); }
    private function rejects($data, $currency = null, $field = null)
    {
        try { (new CurrenciesController)->validateCurrencies(Request::create('/', 'POST', $data), $currency); $this->fail('Expected failure'); }
        catch (ValidationException $e) { $this->assertNotEmpty($e->errors()); if ($field) $this->assertArrayHasKey($field, $e->errors()); }
    }
    public function test_invalid_fields_are_rejected()
    {
        foreach (['name' => ['', str_repeat('x', 192), '<img>'], 'code' => ['qa money 123', 'AB', 'ABCD', '<img>', ['EUR']], 'symbol' => ['', '<script>', str_repeat('x', 11)], 'cent' => [str_repeat('x', 11), '<script>'], 'symbol_cent' => ['abc', 0, -1, 1.5, 2147483648]] as $field => $values) {
            foreach ($values as $value) $this->rejects($this->data([$field => $value]), null, $field);
        }
        (new CurrenciesController)->validateCurrencies(Request::create('/', 'POST', $this->data(['symbol' => 'Kč', 'cent' => null, 'symbol_cent' => null])));
        $this->assertSame(0, Currencies::count());
    }
    public function test_codes_are_normalized_and_duplicate_codes_rejected_case_insensitively()
    {
        $controller = new CurrenciesController;
        $this->assertSame(302, $controller->store(Request::create('/', 'POST', $this->data(['code' => ' qaa '])))->getStatusCode());
        $currency = Currencies::first(); $this->assertSame('QAA', $currency->code);
        $this->rejects($this->data(['code' => 'qaa']), null, 'code');
        $this->assertSame(302, $controller->update(Request::create('/', 'PUT', $this->data(['name' => 'Renamed'])), $currency->id)->getStatusCode());
        DB::table('currencies')->where('id', $currency->id)->update(['code' => 'qaa']);
        $this->rejects($this->data(), null, 'code');
    }
    public function test_every_reference_blocks_delete_and_code_change()
    {
        (new CurrenciesController)->store(Request::create('/', 'POST', $this->data())); $currency = Currencies::first();
        app()->instance('request', Request::create('/', 'POST', [], [], [], ['HTTP_ACCEPT' => 'application/json']));
        foreach (['tour_packages' => $currency->id, 'hotel_offers' => $currency->id, 'client_invoices' => strtolower($currency->code), 'currency_rates' => $currency->code] as $table => $value) {
            DB::table($table)->insert(['currency' => $value]);
            $this->assertTrue($currency->isInUse());
            $response = (new CurrenciesController)->destroy($currency->id);
            $this->assertSame(422, $response->getStatusCode()); $this->assertFalse($response->getData(true)['success']);
            $this->assertNotNull(Currencies::find($currency->id));
            $this->rejects($this->data(['code' => 'QAB']), $currency, 'code');
            DB::table($table)->delete();
        }
    }
    public function test_unused_custom_currency_deletes_with_its_comments()
    {
        (new CurrenciesController)->store(Request::create('/', 'POST', $this->data())); $currency = Currencies::first();
        DB::table('comments')->insert(['reference_type' => 15, 'reference_id' => $currency->id]);
        $this->assertSame(302, (new CurrenciesController)->destroy($currency->id)->getStatusCode());
        $this->assertNull(Currencies::find($currency->id)); $this->assertSame(0, DB::table('comments')->whereNull('deleted_at')->count());
    }
    public function test_failed_comment_cleanup_rolls_back_deletion()
    {
        (new CurrenciesController)->store(Request::create('/', 'POST', $this->data())); $currency = Currencies::first(); Schema::drop('comments');
        try { (new CurrenciesController)->destroy($currency->id); $this->fail('Expected failure'); }
        catch (\Illuminate\Database\QueryException $e) { $this->assertNotNull(Currencies::find($currency->id)); }
    }
    public function test_legacy_update_and_confirmation_permissions()
    {
        foreach (['/currencies/1/update' => ['POST', 'currencies.edit'], '/currencies/1/deleteMsg' => ['GET', 'currencies.destroy']] as $url => [$method, $permission]) {
            $request = Request::create($url, $method); $route = app('router')->getRoutes()->match($request);
            $this->assertSame($permission, $route->getAction('permissions')); $request->setRouteResolver(fn() => $route);
            $request->setUserResolver(fn() => new class { public $id = 1; public function hasPermissionTo($p) { return false; } });
            try { (new \App\Http\Middleware\PermissionsRequiredMiddleware)->handle($request, fn() => response('bypass')); $this->fail('Bypass'); }
            catch (\Symfony\Component\HttpKernel\Exception\HttpException $e) { $this->assertSame(403, $e->getStatusCode()); }
        }
    }
    public function test_details_escape_all_legacy_markup_fields()
    {
        $currencies = new Currencies;
        foreach (['name', 'code', 'symbol', 'cent', 'symbol_cent'] as $field) $currencies->$field = '<script>' . $field . '</script>';
        preg_match('/<tbody>.*?<\/tbody>/s', file_get_contents(resource_path('views/currencies/show.blade.php')), $match);
        $html = (string) $this->blade($match[0], compact('currencies'));
        foreach (['name', 'code', 'symbol', 'cent', 'symbol_cent'] as $field) $this->assertStringContainsString(e($currencies->$field), $html);
        $this->assertStringNotContainsString('<script>', $html);
    }
    public function test_create_avoids_unused_query_and_helper_has_valid_urls()
    {
        DB::enableQueryLog(); DB::flushQueryLog(); (new CurrenciesController)->create();
        $this->assertSame([], DB::getQueryLog()); DB::disableQueryLog();
        $user = $this->createMock(\App\User::class); $user->method('can')->willReturn(true); \Illuminate\Support\Facades\Auth::setUser($user);
        $currency = new Currencies; $currency->id = 10;
        $html = (new CurrenciesController)->getButton(10, $currency);
        foreach (['/currencies/10', '/currencies/10/edit', '/currencies/10/deleteMsg'] as $url) $this->assertStringContainsString($url, $html);
    }
    public function test_ajax_show_and_edit_reject_missing_ids()
    {
        foreach (['show', 'edit'] as $method) {
            try { (new CurrenciesController)->$method(999999, Request::create('/', 'GET', [], [], [], ['HTTP_X_REQUESTED_WITH' => 'XMLHttpRequest'])); $this->fail('Missing record accepted'); }
            catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) { $this->assertSame([999999], $e->getIds()); }
        }
    }
}
