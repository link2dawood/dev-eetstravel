<?php
// Supplier-invoice regression checks. Fixture writes run inside a rolled-back transaction.
require __DIR__ . '/../vendor/autoload.php';
$app = require __DIR__ . '/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Invoices;
use App\InvoicesTours;
use App\Tour;
use App\TourPackage;
use App\Offices;
use App\Transaction;
use App\Http\Controllers\InvoicesController;
use App\Services\InvoicePayments;

class QaInvoiceUser extends App\User
{
    public $allowInvoiceCreate = false;
    public function can($ability, $arguments = []) { return $ability === 'invoices.create' && $this->allowInvoiceCreate; }
}

if (DB::getDriverName() !== 'mysql') throw new RuntimeException('MySQL required');
$tables = ['supplier_invoices', 'invoices_tours', 'transactions'];
foreach ($tables as $table) {
    $engine = DB::selectOne('SELECT ENGINE AS engine FROM information_schema.TABLES WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = ?', [$table]);
    if (!$engine || $engine->engine !== 'InnoDB') throw new RuntimeException('Transactional table required: ' . $table);
}
$snapshot = fn () => collect($tables)->mapWithKeys(fn ($table) => [$table => hash('sha256', serialize(DB::table($table)->orderBy('id')->get()->toArray()))])->all();
$before = $snapshot();
$results = [];
$check = function ($name, $fn) use (&$results) {
    try { if ($fn() === false) throw new RuntimeException('Assertion failed'); $results[] = ['check' => $name, 'status' => 'passed']; }
    catch (Throwable $e) { $results[] = ['check' => $name, 'status' => 'failed', 'error' => $e->getMessage()]; }
};
$controller = new InvoicesController;
$userRow = DB::table('users')->first();
if (!$userRow) throw new RuntimeException('Need a local user to verify invoice archival audit metadata');
$user = new QaInvoiceUser; $user->setRawAttributes((array) $userRow); Auth::setUser($user);
DB::beginTransaction();
try {
    $tour = Tour::firstOrFail();
    $package = TourPackage::where('tour_id', $tour->id)->first();
    $office = Offices::firstOrFail();
    $tag = 'QA-SINV-' . bin2hex(random_bytes(6));
    $data = [
        'office_id' => $office->id, 'tour_id' => [$tour->id], 'invoice_no' => $tag,
        'total_amount' => 100.50, 'extra_amount' => 20.55, 'received_date' => date('Y-m-d'),
        'due_date' => date('Y-m-d', strtotime('+7 days')), 'note' => 'Local QA fixture',
        'paid_amount' => [50.25], 'paymentdate' => [date('Y-m-d')], 'payment_method' => ['QA Local'],
    ];
    if ($package) $data['package_id' . $tour->id] = [$package->id];

    $check('Extra cost is included in the available payment balance', function () {
        InvoicePayments::ensureWithinBalance([['amount' => 70.80]], 100.50, 50.25, 20.55);
        try { InvoicePayments::ensureWithinBalance([['amount' => 70.81]], 100.50, 50.25, 20.55); }
        catch (Illuminate\Validation\ValidationException $e) { return true; }
        return false;
    });
    $check('Supplier invoice stores supplied received and due dates and first payment', function () use ($controller, $data, $tag) {
        $controller->store(Request::create('/invoices', 'POST', $data));
        $invoice = Invoices::where('invoice_no', $tag)->first();
        return $invoice && abs((float) $invoice->extra_amount - 20.55) < .001 && $invoice->receivedDate === $data['received_date'] && $invoice->dueDate === $data['due_date']
            && abs((float) $invoice->total_amount - 100.50) < .001
            && abs((float) Transaction::where('invoice_id', $invoice->id)->where('pay_to', 'Supplier')->sum('amount') - 50.25) < .001;
    });
    $invoice = Invoices::where('invoice_no', $tag)->firstOrFail();
    $check('Remaining extra-cost balance can be paid exactly', function () use ($controller, $invoice) {
        $request = Request::create('/payment_store/' . $invoice->id, 'POST', [
            'paid_amount' => [70.80], 'paymentdate' => [date('Y-m-d')], 'payment_method' => ['QA Local'],
        ]);
        $controller->payment_store($invoice->id, $request);
        return abs((float) Transaction::where('invoice_id', $invoice->id)->where('pay_to', 'Supplier')->sum('amount') - 121.05) < .001;
    });
    $check('Invoice number duplicate in the same office is rejected', function () use ($controller, $data, $invoice) {
        $duplicate = $data; $duplicate['invoice_no'] = $invoice->invoice_no;
        try { $controller->validateInvoice(Request::create('/invoices', 'POST', $duplicate)); }
        catch (Illuminate\Validation\ValidationException $e) { return isset($e->errors()['invoice_no']); }
        return false;
    });
    $otherPackage = TourPackage::where('tour_id', '!=', $tour->id)->first();
    if ($otherPackage) {
        $check('A package belonging to another tour is rejected', function () use ($controller, $data, $tour, $otherPackage) {
            $wrong = $data; $wrong['package_id' . $tour->id] = [$otherPackage->id];
            try { $controller->validateInvoice(Request::create('/invoices', 'POST', $wrong)); }
            catch (Illuminate\Validation\ValidationException $e) { return true; }
            return false;
        });
    }
    $check('Invoice index is paginated and query count stays bounded', function () use ($controller, $tag) {
        DB::enableQueryLog(); DB::flushQueryLog();
        $page = $controller->index(Request::create('/invoices', 'GET', ['search' => $tag]))->getData()['invoicesData'];
        $count = count(DB::getQueryLog()); DB::disableQueryLog();
        return $page->total() === 1 && $page->perPage() === 25 && $count <= 9;
    });
    $check('Invoice show resolves by invoice ID with links intact', function () use ($controller, $invoice) {
        $view = $controller->show($invoice->id, Request::create('/invoices/' . $invoice->id));
        return $view->getData()['invoices']->id === $invoice->id && $view->getData()['invoices']->activeTours->count() >= 1;
    });
    $check('Missing invoice show returns 404', function () use ($controller) {
        try { $controller->show(2147483647, Request::create('/invoices/2147483647')); }
        catch (Illuminate\Database\Eloquent\ModelNotFoundException $e) { return true; }
        return false;
    });
    $check('Supplier package dropdown checks permission and returns 404 for unknown tour', function () use ($controller, $tour, $user) {
        try { $controller->supplierdropdown($tour->id, Request::create('/supplierdropdown/' . $tour->id)); }
        catch (Symfony\Component\HttpKernel\Exception\HttpException $e) { if ($e->getStatusCode() !== 403) return false; }
        $user->allowInvoiceCreate = true;
        try { $controller->supplierdropdown(2147483647, Request::create('/supplierdropdown/2147483647')); }
        catch (Illuminate\Database\Eloquent\ModelNotFoundException $e) { return true; }
        return false;
    });
    $check('Supplier package dropdown escapes tour labels', function () use ($controller, $tour, $user) {
        $original = $tour->name;
        DB::table('tours')->where('id', $tour->id)->update(['name' => '<img src=x onerror=alert(1)>']);
        try {
            $result = $controller->supplierdropdown($tour->id, Request::create('/supplierdropdown/' . $tour->id));
            $html = is_array($result) ? (string) ($result[0] ?? '') : (string) $result;
            return strpos($html, '&lt;img') !== false && strpos($html, '<img') === false;
        } finally {
            DB::table('tours')->where('id', $tour->id)->update(['name' => $original]);
        }
    });
    $check('Invoice edit changes dates while leaving payments untouched', function () use ($controller, $invoice, $data) {
        $beforePaid = (float) Transaction::where('invoice_id', $invoice->id)->where('pay_to', 'Supplier')->sum('amount');
        $newReceived = date('Y-m-d', strtotime('-1 day'));
        $request = Request::create('/invoices/' . $invoice->id . '/update', 'POST', [
            'office_id' => $invoice->office_id, 'invoice_no' => $invoice->invoice_no,
            'total_amount' => $invoice->total_amount, 'extra_amount' => $invoice->extra_amount,
            'received_date' => $newReceived, 'due_date' => date('Y-m-d', strtotime('+14 days')),
        ]);
        $controller->update($invoice->id, $request);
        $invoice->refresh();
        return $invoice->receivedDate === $newReceived && $invoice->dueDate === date('Y-m-d', strtotime('+14 days'))
            && (float) Transaction::where('invoice_id', $invoice->id)->where('pay_to', 'Supplier')->sum('amount') === $beforePaid;
    });
    $check('Invoices with payments stay protected from archive', function () use ($controller, $invoice) {
        try { $controller->destroy($invoice->id); }
        catch (Illuminate\Validation\ValidationException $e) { return Invoices::find($invoice->id) !== null; }
        return false;
    });
    $unpaidTag = $tag . '-UNPAID';
    $unpaidData = $data; $unpaidData['invoice_no'] = $unpaidTag; unset($unpaidData['paid_amount'], $unpaidData['paymentdate'], $unpaidData['payment_method']);
    $controller->store(Request::create('/invoices', 'POST', $unpaidData));
    $unpaidInvoice = Invoices::where('invoice_no', $unpaidTag)->firstOrFail();
    $check('Invoice archive retains header, tour links, and staff attribution', function () use ($controller, $unpaidInvoice, $user) {
        try { $controller->destroy($unpaidInvoice->id); }
        catch (Illuminate\Validation\ValidationException $e) { return false; }
        $archived = Invoices::withTrashed()->find($unpaidInvoice->id);
        return $archived && $archived->trashed() && (int) $archived->deleted_by === (int) $user->id
            && InvoicesTours::withTrashed()->where('invoices_id', $unpaidInvoice->id)->whereNotNull('deleted_at')->exists();
    });
} catch (Throwable $e) {
    $results[] = ['check' => 'Workflow setup', 'status' => 'failed', 'error' => $e->getMessage()];
} finally {
    while (DB::transactionLevel()) DB::rollBack();
}
$check('Invoice, link, and payment rows are unchanged after rollback', fn () => $before === $snapshot());
file_put_contents(storage_path('app/qa-mysql-invoices-results.json'), json_encode(['date' => date(DATE_ATOM), 'results' => $results], JSON_PRETTY_PRINT));
foreach ($results as $result) echo strtoupper($result['status']) . ' ' . $result['check'] . (isset($result['error']) ? ': ' . $result['error'] : '') . PHP_EOL;
exit(count(array_filter($results, fn ($result) => $result['status'] === 'failed')) ? 1 : 0);
