<?php
// Local MySQL currency workflows. All fixture writes are rolled back.
require __DIR__ . '/../vendor/autoload.php';
$app = require __DIR__ . '/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Currencies;
use App\Http\Controllers\CurrenciesController;
if (DB::getDriverName() !== 'mysql') throw new RuntimeException('MySQL required');
$results = [];
$check = function ($name, $fn) use (&$results) {
    try { if ($fn() === false) throw new RuntimeException('Assertion failed'); $results[] = ['check' => $name, 'status' => 'passed']; }
    catch (Throwable $e) { $results[] = ['check' => $name, 'status' => 'failed', 'error' => $e->getMessage()]; }
};
$references = ['tour_packages', 'hotel_offers', 'client_invoices', 'currency_rates'];
$tables = array_merge(['currencies', 'comments'], $references);
foreach ($tables as $table) {
    $engine = DB::selectOne('SELECT ENGINE AS engine FROM information_schema.TABLES WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = ?', [$table]);
    if (!$engine || $engine->engine !== 'InnoDB') throw new RuntimeException('Transactional table required: ' . $table);
}
$inventory = DB::select("SELECT TABLE_NAME AS name, COLUMN_NAME AS field FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND COLUMN_NAME IN ('currency', 'currency_id', 'currencies_id')");
$snapshot = fn() => collect($tables)->mapWithKeys(fn($t) => [$t => hash('sha256', serialize(DB::table($t)->orderBy('id')->get()->toArray()))])->all();
$before = $snapshot();
DB::beginTransaction();
try {
    $controller = new CurrenciesController;
    do { $code = 'Q' . chr(random_int(65, 90)) . chr(random_int(65, 90)); } while (Currencies::whereRaw('UPPER(code) = ?', [$code])->exists());
    $data = ['name' => 'QA MySQL currency ' . bin2hex(random_bytes(5)), 'code' => strtolower($code), 'symbol' => '¤', 'cent' => 'cent', 'symbol_cent' => 100];
    $check('Create redirects normally', fn() => $controller->store(Request::create('/', 'POST', $data))->getStatusCode() === 302);
    $currency = Currencies::where('code', $code)->firstOrFail();
    $check('CU5 currency code stored in uppercase', fn() => $currency->code === $code);
    $data['name'] .= ' edited';
    $check('Update ignores own unique code and preserves symbol', fn() => $controller->update(Request::create('/', 'PUT', $data), $currency->id)->getStatusCode() === 302 && $currency->fresh()->name === $data['name'] && $currency->fresh()->symbol === '¤');
    $reject = function ($input, $field, $record = null) use ($controller) {
        try { $controller->validateCurrencies(Request::create('/', 'POST', $input), $record); }
        catch (Illuminate\Validation\ValidationException $e) { return isset($e->errors()[$field]); }
        return false;
    };
    $check('CU4 duplicate code rejected case-insensitively', fn() => $reject($data, 'code'));
    foreach (['code' => 'qa money 123', 'name' => str_repeat('x', 192), 'symbol' => '<script>', 'cent' => str_repeat('x', 11), 'symbol_cent' => 'abc'] as $field => $value) {
        $check('Invalid ' . $field . ' rejected', fn() => $reject(array_replace($data, [$field => $value]), $field, $currency));
    }
    app()->instance('request', Request::create('/', 'POST', [], [], [], ['HTTP_ACCEPT' => 'application/json']));
    foreach ($references as $table) {
        $source = DB::table($table)->first();
        if (!$source) throw new RuntimeException('Reference fixture required: ' . $table);
        $value = in_array($table, ['tour_packages', 'hotel_offers'], true) ? $currency->id : $currency->code;
        DB::table($table)->where('id', $source->id)->update(['currency' => $value]);
        $check('CU1 deletion blocked by ' . $table, fn() => $controller->destroy($currency->id)->getStatusCode() === 422 && Currencies::find($currency->id) !== null);
        do { $newCode = 'Q' . chr(random_int(65, 90)) . chr(random_int(65, 90)); } while (Currencies::whereRaw('UPPER(code) = ?', [$newCode])->exists());
        $check('Used code cannot change: ' . $table, fn() => $reject(array_replace($data, ['code' => $newCode]), 'code', $currency));
        DB::table($table)->where('id', $source->id)->update(['currency' => $source->currency]);
    }
    $check('Unused currency deletes with JSON success', fn() => $controller->destroy($currency->id)->getStatusCode() === 200 && Currencies::find($currency->id) === null);
    $check('List pagination executes', fn() => $controller->index(Request::create('/currencies'))->getData()['currencies']->total() >= 1);
} catch (Throwable $e) { $results[] = ['check' => 'Workflow setup', 'status' => 'failed', 'error' => $e->getMessage()]; }
finally { while (DB::transactionLevel()) DB::rollBack(); }
$check('All currency/reference/comment values preserved after rollback', fn() => $before === $snapshot());
file_put_contents(storage_path('app/qa-mysql-currencies-results.json'), json_encode(['date' => date(DATE_ATOM), 'reference_columns' => $inventory, 'results' => $results], JSON_PRETTY_PRINT));
foreach ($results as $r) echo strtoupper($r['status']) . ' ' . $r['check'] . (isset($r['error']) ? ': ' . $r['error'] : '') . PHP_EOL;
echo 'Currency reference inventory: ' . json_encode($inventory) . PHP_EOL;
exit(count(array_filter($results, fn($r) => $r['status'] === 'failed')) ? 1 : 0);
