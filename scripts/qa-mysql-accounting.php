<?php
// Local invoice workflows. No external payments/sends; all fixture writes roll back.
require __DIR__ . '/../vendor/autoload.php';
$app = require __DIR__ . '/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\ClientInvoices;
use App\Http\Controllers\ClientInvoiceController;
class QaAccountingUser extends App\User
{
    protected $table = 'users';
    public function hasPermissionTo($permission, $guardName = null): bool { return in_array($permission, ['transactions.index', 'transactions.show', 'transactions.create', 'transactions.edit', 'transactions.destroy'], true); }
}
if (DB::getDriverName() !== 'mysql') throw new RuntimeException('MySQL required');
$results = [];
$check = function ($name, $fn) use (&$results) {
    try { if ($fn() === false) throw new RuntimeException('Assertion failed'); $results[] = ['check' => $name, 'status' => 'passed']; }
    catch (Throwable $e) { $results[] = ['check' => $name, 'status' => 'failed', 'error' => $e->getMessage()]; }
};
$tables = ['client_invoices', 'invoice_items', 'transactions', 'users'];
foreach ($tables as $table) {
    $engine = DB::selectOne('SELECT ENGINE AS engine FROM information_schema.TABLES WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = ?', [$table]);
    if (!$engine || $engine->engine !== 'InnoDB') throw new RuntimeException('Transactional table required: ' . $table);
}
$snapshot = fn() => collect($tables)->mapWithKeys(fn($t) => [$t => hash('sha256', serialize(DB::table($t)->orderBy('id')->get()->toArray()))])->all();
$before = $snapshot();
DB::beginTransaction();
try {
    $user = new QaAccountingUser; $user->setRawAttributes((array)DB::table('users')->first()); Auth::setUser($user);
    $controller = new ClientInvoiceController;
    $check('Existing billing permissions cover all invoice actions', function () {
        $expected = ['transactions.show', 'transactions.create', 'transactions.edit', 'transactions.destroy'];
        $present = DB::table('permissions')->whereIn('name', $expected)->pluck('name')->unique()->all();
        $missing = array_diff($expected, $present);
        if ($missing) throw new RuntimeException('Missing permissions: ' . implode(', ', $missing));
        return true;
    });
    $tag = 'QA MySQL Accounting ' . bin2hex(random_bytes(5));
    $data = ['currency' => 'EUR', 'office_id' => App\Offices::firstOrFail()->id, 'tour_id' => App\Tour::firstOrFail()->id, 'client_id' => App\Client::firstOrFail()->id, 'note' => $tag, 'extra_cost' => 5.55, 'items' => [['item_name' => $tag, 'quantity' => 2, 'amount' => 100, 'vat' => .2, 'total_amount' => 1]]];
    $check('Validated invoice creation succeeds', fn() => $controller->store(Request::create('/', 'POST', $data))->getStatusCode() === 302);
    $invoice = ClientInvoices::where('note', $tag)->firstOrFail();
    $check('VAT and extra-cost cents are recalculated and stored', fn() => abs((float)$invoice->amount_receiveable - 245.55) < .001 && abs((float)$invoice->extra_cost - 5.55) < .001);
    $payment = ['paid_amount' => [50.10], 'paymentdate' => [date('Y-m-d')], 'payment_method' => ['QA Local Fixture']];
    $check('Local payment row saves against the correct invoice', fn() => $controller->payment_store($invoice->id, Request::create('/', 'POST', $payment))->getStatusCode() === 302 && abs((float)DB::table('transactions')->where('invoice_id', $invoice->id)->where('pay_to', 'Client')->sum('amount') - 50.10) < .001);
    $check('Paid invoice remains protected against archive', function () use ($controller, $invoice) {
        try { $controller->destroy($invoice->id); }
        catch (Illuminate\Validation\ValidationException $e) { return ClientInvoices::find($invoice->id) !== null; }
        return false;
    });
    $check('AC4 paginated data uses at most four queries and correct balance', function () use ($controller, $invoice) {
        DB::enableQueryLog(); DB::flushQueryLog();
        $rows = $controller->index(Request::create('/accounting', 'GET', ['search' => $invoice->invoice_no]))->getData()['accountingData'];
        $count = count(DB::getQueryLog()); DB::disableQueryLog();
        return $count <= 4 && $rows->total() === 1 && $rows->perPage() === 25 && $rows->first()->Status === 'They Owe 195.45';
    });
    $check('AC2 service form uses its tour argument', function () use ($controller, $data) {
        $view = $controller->serviceTransactionCreate($data['tour_id'], Request::create('/'));
        return $view->name() === 'accounting.create' && $view->getData()['selectedTourId'] == $data['tour_id'];
    });
    $check('AC5 unknown quotation parent gives not-found', function () use ($controller) {
        try { $controller->getTourquotation(2147483647); }
        catch (Illuminate\Database\Eloquent\ModelNotFoundException $e) { return true; }
        return false;
    });
    $data['note'] = $tag . ' unpaid'; $controller->store(Request::create('/', 'POST', $data));
    $unpaid = ClientInvoices::where('note', $data['note'])->firstOrFail();
    $itemsBefore = DB::table('invoice_items')->where('invoice_id', $unpaid->id)->orderBy('id')->get()->toArray();
    app()->instance('request', Request::create('/', 'POST', [], [], [], ['HTTP_ACCEPT' => 'application/json']));
    $check('AC1 archive preserves header and staff audit metadata', function () use ($controller, $unpaid, $user) {
        $response = $controller->destroy($unpaid->id);
        $archived = ClientInvoices::withTrashed()->find($unpaid->id);
        return $response->getStatusCode() === 200 && ClientInvoices::find($unpaid->id) === null && $archived && $archived->trashed() && $archived->deleted_by == $user->id;
    });
    $check('AC1 archive preserves all original item values', function () use ($unpaid, $itemsBefore) {
        $after = DB::table('invoice_items')->where('invoice_id', $unpaid->id)->orderBy('id')->get()->toArray();
        foreach ($after as $item) if (!$item->deleted_at) return false;
        foreach ([$itemsBefore, $after] as $items) foreach ($items as $item) unset($item->deleted_at);
        return serialize($itemsBefore) === serialize($after);
    });
    $check('Archived invoice items cannot be read through helper', function () use ($controller, $unpaid) {
        try { $controller->getInvoiceItem(Request::create('/', 'GET', ['invoice_id' => $unpaid->id])); }
        catch (Illuminate\Database\Eloquent\ModelNotFoundException $e) { return true; }
        return false;
    });
    $check('Archived invoice disappears from active search', fn() => $controller->index(Request::create('/accounting', 'GET', ['search' => $unpaid->invoice_no]))->getData()['accountingData']->total() === 0);
} catch (Throwable $e) { $results[] = ['check' => 'Workflow setup', 'status' => 'failed', 'error' => $e->getMessage()]; }
finally { while (DB::transactionLevel()) DB::rollBack(); }
$check('All invoice/item/payment/user values preserved after rollback', fn() => $before === $snapshot());
file_put_contents(storage_path('app/qa-mysql-accounting-results.json'), json_encode(['date' => date(DATE_ATOM), 'results' => $results], JSON_PRETTY_PRINT));
foreach ($results as $r) echo strtoupper($r['status']) . ' ' . $r['check'] . (isset($r['error']) ? ': ' . $r['error'] : '') . PHP_EOL;
exit(count(array_filter($results, fn($r) => $r['status'] === 'failed')) ? 1 : 0);
