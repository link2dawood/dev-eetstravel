<?php

namespace Tests\Unit;

use App\ClientInvoices;
use App\Http\Controllers\ClientInvoiceController;
use App\Http\Controllers\TaxController;
use App\Repository\Contracts\ClientRepository;
use App\Services\InvoicePayments;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class AccountingSafetyTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        config(['database.default' => 'sqlite', 'database.connections.sqlite.database' => ':memory:']);
        DB::purge('sqlite');
        foreach (['office_fees', 'clients', 'tours'] as $table) {
            Schema::create($table, function (Blueprint $t) { $t->increments('id'); });
            DB::table($table)->insert(['id' => 1]);
        }
        Schema::create('client_invoices', function (Blueprint $t) {
            $t->increments('id');
            foreach (['invoice_no','date','currency'] as $name) $t->string($name);
            foreach (['office_id','client_id','tour_id'] as $name) $t->integer($name);
            $t->decimal('amount_receiveable', 15, 2);
            $t->string('note')->nullable(); $t->integer('extra_cost')->nullable();
        });
        Schema::create('invoice_items', function (Blueprint $t) {
            $t->increments('id'); $t->integer('invoice_id'); $t->string('item_name');
            foreach (['quantity','amount','vat','total_amount'] as $name) $t->decimal($name, 15, 2);
        });
        Schema::create('transactions', function (Blueprint $t) {
            $t->increments('id'); $t->integer('invoice_id'); $t->string('date');
            $t->string('pay_to'); $t->string('trans_no'); $t->string('payment_method');
            $t->decimal('amount', 15, 2); $t->timestamps();
        });
        Schema::create('invoice_taxes', function (Blueprint $t) { $t->increments('id'); $t->string('name'); $t->decimal('value'); });
    }

    private function invoiceRequest(array $extra = []): Request
    {
        return Request::create('/accounting', 'POST', $extra + [
            'currency' => 'EUR', 'office_id' => 1, 'tour_id' => 1, 'client_id' => 1,
            'items' => [['item_name' => 'Test service', 'quantity' => 2, 'amount' => 100, 'vat' => .2, 'total_amount' => 1]],
        ]);
    }

    public function test_create_recalculates_vat_and_keeps_multiple_payments_on_same_invoice()
    {
        (new ClientInvoiceController())->store($this->invoiceRequest([
            'paid_amount' => [20, 30], 'paymentdate' => ['2026-10-14','2026-10-14'], 'payment_method' => ['Card','Bank'],
        ]));
        $invoice = ClientInvoices::firstOrFail();
        $this->assertEquals(240, $invoice->amount_receiveable);
        $this->assertEquals(240, DB::table('invoice_items')->value('total_amount'));
        $this->assertSame([$invoice->id, $invoice->id], DB::table('transactions')->pluck('invoice_id')->all());
    }

    public function test_edit_without_loaded_payment_form_preserves_existing_payment()
    {
        $controller = new ClientInvoiceController();
        $controller->store($this->invoiceRequest(['paid_amount' => [50], 'paymentdate' => ['2026-10-14'], 'payment_method' => ['Bank']]));
        $id = ClientInvoices::firstOrFail()->id;
        $paymentId = DB::table('transactions')->value('id');
        $controller->update($id, $this->invoiceRequest());
        $this->assertEquals(240, ClientInvoices::find($id)->amount_receiveable);
        $this->assertEquals($paymentId, DB::table('transactions')->value('id'));
    }

    public function test_fractional_extra_cost_is_included_and_preserved_when_edit_field_is_missing()
    {
        $controller = new ClientInvoiceController();
        $controller->store($this->invoiceRequest(['extra_cost' => 5.55]));
        $invoice = ClientInvoices::firstOrFail();
        $this->assertEquals(245.55, $invoice->amount_receiveable);
        $controller->update($invoice->id, $this->invoiceRequest());
        $this->assertEquals(245.55, $invoice->fresh()->amount_receiveable);
    }

    public function test_overpayment_is_rejected_without_any_partial_invoice_write()
    {
        try {
            (new ClientInvoiceController())->store($this->invoiceRequest(['paid_amount' => [241], 'paymentdate' => ['2026-10-14'], 'payment_method' => ['Bank']]));
            $this->fail('Overpayment accepted');
        } catch (ValidationException $e) {
            $this->assertSame(0, DB::table('client_invoices')->count());
            $this->assertSame(0, DB::table('invoice_items')->count());
            $this->assertSame(0, DB::table('transactions')->count());
        }
    }

    public function test_additional_payment_respects_existing_balance()
    {
        $controller = new ClientInvoiceController();
        $controller->store($this->invoiceRequest(['paid_amount' => [200], 'paymentdate' => ['2026-10-14'], 'payment_method' => ['Bank']]));
        try {
            $controller->payment_store(ClientInvoices::firstOrFail()->id, Request::create('/', 'POST', ['paid_amount' => [41], 'paymentdate' => ['2026-10-14'], 'payment_method' => ['Bank']]));
            $this->fail('Balance exceeded');
        } catch (ValidationException $e) {
            $this->assertEquals(200, DB::table('transactions')->sum('amount'));
        }
    }

    public function test_negative_payment_is_rejected()
    {
        $this->expectException(ValidationException::class);
        InvoicePayments::rows(Request::create('/', 'POST', ['paid_amount' => [-1], 'paymentdate' => ['2026-10-14'], 'payment_method' => ['Bank']]));
    }

    public function test_misaligned_payment_arrays_are_rejected()
    {
        $this->expectException(ValidationException::class);
        InvoicePayments::rows(Request::create('/', 'POST', ['paid_amount' => [1,2], 'paymentdate' => ['2026-10-14'], 'payment_method' => ['Bank']]));
    }

    public function test_paid_invoice_cannot_be_deleted()
    {
        $controller = new ClientInvoiceController();
        $controller->store($this->invoiceRequest(['paid_amount' => [1], 'paymentdate' => ['2026-10-14'], 'payment_method' => ['Bank']]));
        try { $controller->destroy(ClientInvoices::firstOrFail()->id); $this->fail('Paid invoice deleted'); }
        catch (ValidationException $e) {
            $this->assertSame(1, ClientInvoices::count());
            $this->assertSame(1, DB::table('invoice_items')->count());
        }
    }

    public function test_ledger_edit_checks_balance_without_replacing_payment_reference()
    {
        (new ClientInvoiceController())->store($this->invoiceRequest(['paid_amount' => [50], 'paymentdate' => ['2026-10-14'], 'payment_method' => ['Bank']]));
        $payment = \App\Transaction::firstOrFail();
        $controller = new \App\Http\Controllers\TransactionController();
        try {
            $controller->update($payment->id, Request::create('/', 'PUT', ['date'=>'2026-10-14', 'amount'=>241, 'payment_method'=>'Bank']));
            $this->fail('Ledger overpayment accepted');
        } catch (ValidationException $e) {
            $this->assertEquals(50, $payment->fresh()->amount);
        }
        $controller->update($payment->id, Request::create('/', 'PUT', ['date'=>'2026-10-14', 'amount'=>75.25, 'payment_method'=>'Bank']));
        $this->assertSame($payment->trans_no, $payment->fresh()->trans_no);
        $this->assertEquals(75.25, $payment->fresh()->amount);
    }

    public function test_tax_deletion_never_deletes_client_with_same_id()
    {
        DB::table('invoice_taxes')->insert(['id'=>1,'name'=>'Test VAT','value'=>20]);
        $repository = $this->createMock(ClientRepository::class);
        $repository->expects($this->never())->method('deleteById');
        (new TaxController($repository))->destroy(1);
        $this->assertSame(0, DB::table('invoice_taxes')->count());
        $this->assertSame(1, DB::table('clients')->count());
    }

    public function test_reporting_paginates_and_excludes_soft_deleted_suppliers()
    {
        foreach (['hotels', 'events', 'guides', 'restaurants'] as $table) {
            Schema::create($table, function (Blueprint $t) {
                $t->increments('id'); $t->string('name'); $t->string('country')->nullable(); $t->integer('city')->nullable(); $t->softDeletes();
            });
        }
        Schema::create('countries', function (Blueprint $t) { $t->string('alias'); $t->string('name'); });
        Schema::create('cities', function (Blueprint $t) { $t->increments('id'); $t->string('name'); });
        DB::table('events')->insert(['name' => 'Deleted supplier', 'deleted_at' => '2026-10-14']);
        for ($i=0; $i<55; $i++) DB::table('hotels')->insert(['name' => sprintf('Hotel %02d', $i)]);
        $view = (new \App\Http\Controllers\ReportingController())->index(Request::create('/reporting'));
        $list = $view->getData()['servicesData'];
        $this->assertSame(55, $list->total());
        $this->assertSame(50, $list->count());
        $filtered = (new \App\Http\Controllers\ReportingController())->index(Request::create('/reporting', 'GET', ['q' => 'Hotel 54', 'service' => 'Hotel']))->getData()['servicesData'];
        $this->assertSame(1, $filtered->total());
        $this->assertSame('Hotel 54', $filtered->first()->name);
    }

    public function test_supplier_invoice_links_use_link_id_and_only_selected_supplier_type()
    {
        Schema::create('supplier_invoices', function (Blueprint $t) {
            $t->increments('id'); $t->string('invoice_no'); $t->string('dueDate'); $t->string('receivedDate'); $t->decimal('total_amount'); $t->integer('office_id');
        });
        Schema::create('tour_packages', function (Blueprint $t) {
            $t->increments('id'); $t->integer('type'); $t->integer('reference'); $t->string('name'); $t->softDeletes();
        });
        Schema::create('invoices_tours', function (Blueprint $t) {
            $t->increments('id'); $t->integer('invoices_id'); $t->integer('invoices_tours_id'); $t->integer('package_id');
        });
        Schema::table('tours', function (Blueprint $t) { $t->string('name')->nullable(); });
        Schema::table('office_fees', function (Blueprint $t) { $t->string('office_name')->nullable(); });
        DB::table('supplier_invoices')->insert(['id'=>3,'invoice_no'=>'TEST','dueDate'=>'2026-10-21','receivedDate'=>'2026-10-14','total_amount'=>100,'office_id'=>1]);
        DB::table('tour_packages')->insert(['id'=>7,'type'=>0,'reference'=>9,'name'=>'Same name']);
        DB::table('invoices_tours')->insert(['id'=>42,'invoices_id'=>3,'invoices_tours_id'=>1,'package_id'=>7]);
        $rows = \App\Helper\SupplierInvoices::forService(0, 9);
        $this->assertSame(42, $rows->first()->link_id);
        $this->assertCount(0, \App\Helper\SupplierInvoices::forService(1, 9));
    }
}
