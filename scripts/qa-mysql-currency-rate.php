<?php
// Local MySQL workflows; all fixture writes are rolled back.
require __DIR__ . '/../vendor/autoload.php';
$app = require __DIR__ . '/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\CurrencyRate;
use App\Http\Controllers\CurrencyRateController;
if (DB::getDriverName() !== 'mysql') throw new RuntimeException('MySQL required');
$results = [];
$check = function ($name, $fn) use (&$results) {
    try { if ($fn() === false) throw new RuntimeException('Assertion failed'); $results[] = ['check' => $name, 'status' => 'passed']; }
    catch (Throwable $e) { $results[] = ['check' => $name, 'status' => 'failed', 'error' => $e->getMessage()]; }
};
$tables = ['currency_rates', 'comments'];
foreach ($tables as $table) {
    $engine = DB::selectOne('SELECT ENGINE AS engine FROM information_schema.TABLES WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = ?', [$table]);
    if (!$engine || $engine->engine !== 'InnoDB') throw new RuntimeException('Transactional table required: ' . $table);
}
$snapshot = fn() => collect($tables)->mapWithKeys(fn($t) => [$t => hash('sha256', serialize(DB::table($t)->orderBy('id')->get()->toArray()))])->all();
$before = $snapshot();
DB::beginTransaction();
try {
    $controller = new CurrencyRateController;
    $currency = DB::table('currencies')->whereRaw('LENGTH(code) = 3')->orderBy('id')->first();
    if (!$currency) throw new RuntimeException('Known currency required');
    $date = '2000-01-01';
    while (CurrencyRate::whereRaw('UPPER(currency) = ?', [strtoupper($currency->code)])->where('date', $date)->exists()) $date = date('Y-m-d', strtotime($date . ' +1 day'));
    $data = ['currency' => strtolower($currency->code), 'rate' => '1.082567', 'date' => $date];
    $check('CR3 schema stores decimal(18,6)', function () {
        $column = DB::selectOne("SELECT COLUMN_TYPE AS type FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'currency_rates' AND COLUMN_NAME = 'rate'");
        return $column->type === 'decimal(18,6)';
    });
    $check('Create redirects normally', fn() => $controller->store(Request::create('/', 'POST', $data))->getStatusCode() === 302);
    $rate = CurrencyRate::where('currency', strtoupper($currency->code))->where('date', $date)->firstOrFail();
    $check('CR3 six-decimal rate round-trips exactly', fn() => $rate->rate === '1.082567');
    $check('CR6 currency stored in canonical uppercase', fn() => $rate->currency === strtoupper($currency->code));
    $check('CR7 duplicate currency/date rejected', function () use ($controller, $data) {
        try { $controller->store(Request::create('/', 'POST', $data)); }
        catch (Illuminate\Validation\ValidationException $e) { return isset($e->errors()['date']); }
        return false;
    });
    $check('Edit excludes own currency/date pair', fn() => $controller->update(Request::create('/', 'PUT', array_replace($data, ['rate' => '0.000001'])), $rate->id)->getStatusCode() === 302 && $rate->fresh()->rate === '0.000001');
    foreach ([['rate', '0'], ['rate', '-5'], ['rate', '1000000000'], ['rate', '1.1234567'], ['currency', 'ZZZ'], ['date', '2099-01-01']] as [$field, $value]) {
        $check("Invalid $field $value rejected", function () use ($controller, $data, $rate, $field, $value) {
            try { $controller->update(Request::create('/', 'PUT', array_replace($data, [$field => $value])), $rate->id); }
            catch (Illuminate\Validation\ValidationException $e) { return isset($e->errors()[$field]); }
            return false;
        });
    }
    $check('List pagination executes', fn() => $controller->index(Request::create('/currency_rate'))->getData()['currency_rates']->total() >= 1);
    app()->instance('request', Request::create('/', 'POST', [], [], [], ['HTTP_ACCEPT' => 'application/json']));
    $check('CR9 unused rate deletes with JSON success', fn() => $controller->destroy($rate->id)->getStatusCode() === 200 && CurrencyRate::find($rate->id) === null);
} catch (Throwable $e) { $results[] = ['check' => 'Workflow setup', 'status' => 'failed', 'error' => $e->getMessage()]; }
finally { while (DB::transactionLevel()) DB::rollBack(); }
$check('All currency-rate/comment values preserved after rollback', fn() => $before === $snapshot());
file_put_contents(storage_path('app/qa-mysql-currency-rate-results.json'), json_encode(['date' => date(DATE_ATOM), 'results' => $results], JSON_PRETTY_PRINT));
foreach ($results as $r) echo strtoupper($r['status']) . ' ' . $r['check'] . (isset($r['error']) ? ': ' . $r['error'] : '') . PHP_EOL;
exit(count(array_filter($results, fn($r) => $r['status'] === 'failed')) ? 1 : 0);
