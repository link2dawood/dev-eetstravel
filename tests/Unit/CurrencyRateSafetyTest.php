<?php
namespace Tests\Unit;

use App\CurrencyRate;
use App\Http\Controllers\CurrencyRateController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class CurrencyRateSafetyTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        config(['database.default' => 'sqlite', 'database.connections.sqlite.database' => ':memory:']); DB::purge('sqlite');
        Schema::create('currency_rates', function ($t) { $t->increments('id'); $t->string('currency'); $t->decimal('rate', 18, 6); $t->date('date'); $t->timestamps(); });
        Schema::create('currencies', function ($t) { $t->increments('id'); $t->string('code'); $t->string('name'); });
        DB::table('currencies')->insert([['code' => 'USD', 'name' => 'US dollar'], ['code' => 'EUR', 'name' => 'Euro']]);
        Schema::create('comments', function ($t) { $t->increments('id'); $t->integer('reference_type'); $t->integer('reference_id'); $t->timestamps(); $t->softDeletes(); });
    }
    private function data($extra = []) { return array_replace(['currency' => 'USD', 'rate' => '1.082567', 'date' => date('Y-m-d')], $extra); }
    private function rejects($data, $rate = null, $field = null)
    {
        try { (new CurrencyRateController)->validateCurrencyRate(Request::create('/', 'POST', $data), $rate); $this->fail('Expected validation failure'); }
        catch (ValidationException $e) { $this->assertNotEmpty($e->errors()); if ($field) $this->assertArrayHasKey($field, $e->errors()); }
    }
    public function test_invalid_currency_rates_precision_and_dates_are_rejected()
    {
        foreach (['currency' => ['unknown', 'ZZZ', '<img>', ['USD']], 'rate' => [0, -1, '1000000000', '1.1234567', '1e2', 'abc', '0.0000001'], 'date' => ['2099-01-01', 'tomorrow', '2025-02-30']] as $field => $values) {
            foreach ($values as $value) $this->rejects($this->data([$field => $value]), null, $field);
        }
        foreach (['0.000001', '1.082567', '999999'] as $value) (new CurrencyRateController)->validateCurrencyRate(Request::create('/', 'POST', $this->data(['rate' => $value])));
        $this->assertSame(0, CurrencyRate::count());
    }
    public function test_currency_is_normalized_and_same_currency_date_is_unique()
    {
        $controller = new CurrencyRateController;
        $controller->store(Request::create('/', 'POST', $this->data(['currency' => ' usd '])));
        $rate = CurrencyRate::first(); $this->assertSame('USD', $rate->currency);
        $this->rejects($this->data(['currency' => 'usd']), null, 'date');
        $this->assertSame(302, $controller->update(Request::create('/', 'PUT', $this->data(['rate' => '1.234567'])), $rate->id)->getStatusCode());
        $this->assertSame('1.234567', $rate->fresh()->rate);
        $controller->store(Request::create('/', 'POST', $this->data(['currency' => 'EUR'])));
        $this->assertSame(2, CurrencyRate::count());
    }
    public function test_decimal_formatter_does_not_convert_through_float()
    {
        foreach (['1.082567' => '1.082567', '25.530000' => '25.53', '999999999999.999999' => '999999999999.999999', '0.000001' => '0.000001', '100000' => '100000'] as $raw => $expected) {
            $rate = new CurrencyRate; $rate->setRawAttributes(['rate' => $raw]); $this->assertSame($expected, $rate->rate);
        }
    }
    public function test_deletion_is_atomic_and_cleans_comments()
    {
        (new CurrencyRateController)->store(Request::create('/', 'POST', $this->data())); $rate = CurrencyRate::first();
        DB::table('comments')->insert(['reference_type' => 14, 'reference_id' => $rate->id]);
        app()->instance('request', Request::create('/', 'POST', [], [], [], ['HTTP_ACCEPT' => 'application/json']));
        $this->assertSame(200, (new CurrencyRateController)->destroy($rate->id)->getStatusCode());
        $this->assertNull(CurrencyRate::find($rate->id)); $this->assertSame(0, DB::table('comments')->whereNull('deleted_at')->count());
    }
    public function test_failed_comment_cleanup_rolls_back_deletion()
    {
        (new CurrencyRateController)->store(Request::create('/', 'POST', $this->data())); $rate = CurrencyRate::first(); Schema::drop('comments');
        try { (new CurrencyRateController)->destroy($rate->id); $this->fail('Expected failure'); }
        catch (\Illuminate\Database\QueryException $e) { $this->assertNotNull(CurrencyRate::find($rate->id)); }
    }
    public function test_legacy_update_and_confirmation_routes_require_permissions()
    {
        foreach (['/currency_rate/1/update' => ['POST', 'currency_rate.edit'], '/currency_rate/1/deleteMsg' => ['GET', 'currency_rate.destroy']] as $url => [$method, $permission]) {
            $request = Request::create($url, $method); $route = app('router')->getRoutes()->match($request);
            $this->assertSame($permission, $route->getAction('permissions')); $request->setRouteResolver(fn() => $route);
            $request->setUserResolver(fn() => new class { public $id = 1; public function hasPermissionTo($p) { return false; } });
            try { (new \App\Http\Middleware\PermissionsRequiredMiddleware)->handle($request, fn() => response('bypass')); $this->fail('Bypass'); }
            catch (\Symfony\Component\HttpKernel\Exception\HttpException $e) { $this->assertSame(403, $e->getStatusCode()); }
        }
    }
    public function test_details_escape_legacy_markup()
    {
        $currency_rate = new CurrencyRate;
        $currency_rate->setRawAttributes(['currency' => '<img src=x>', 'rate' => '<script>x</script>', 'date' => '<script>date</script>']);
        preg_match('/<tbody>.*?<\/tbody>/s', file_get_contents(resource_path('views/currency_rate/show.blade.php')), $match);
        $html = (string) $this->blade($match[0], compact('currency_rate'));
        foreach (['currency', 'rate', 'date'] as $field) $this->assertStringContainsString(e($currency_rate->$field), $html);
        $this->assertStringNotContainsString('<script>', $html);
    }
}
