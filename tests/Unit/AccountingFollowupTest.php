<?php
namespace Tests\Unit;

use App\ClientInvoices;
use App\Http\Controllers\ClientInvoiceController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Auth;
use Tests\TestCase;

class AccountingFollowupTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        config(['database.default' => 'sqlite', 'database.connections.sqlite.database' => ':memory:']); DB::purge('sqlite');
        foreach (['clients', 'tours'] as $table) Schema::create($table, function ($t) { $t->increments('id'); $t->string('name'); $t->integer('client_id')->nullable(); $t->softDeletes(); });
        DB::table('clients')->insert(['id' => 1, 'name' => 'QA Client']); DB::table('tours')->insert(['id' => 200, 'name' => 'QA Tour', 'client_id' => 1]);
        Schema::create('client_invoices', function ($t) { $t->increments('id'); $t->string('invoice_no'); $t->date('date'); $t->integer('client_id'); $t->integer('tour_id'); $t->decimal('amount_receiveable', 15, 2); $t->integer('deleted_by')->nullable(); $t->softDeletes(); });
        Schema::create('invoice_items', function ($t) { $t->increments('id'); $t->integer('invoice_id'); $t->string('item_name'); $t->decimal('amount', 15, 2); $t->softDeletes(); });
        Schema::create('transactions', function ($t) { $t->increments('id'); $t->integer('invoice_id'); $t->string('pay_to'); $t->decimal('amount', 15, 2); });
        Schema::create('quotations', function ($t) { $t->increments('id'); $t->integer('tour_id'); $t->boolean('is_confirm'); $t->softDeletes(); });
        foreach (['hotels', 'events', 'buses', 'restaurants'] as $table) Schema::create($table, function ($t) { $t->increments('id'); $t->softDeletes(); });
        Schema::create('invoice_taxes', function ($t) { $t->increments('id'); });
    }
    private function user($grants = [])
    {
        $user = $this->getMockBuilder(\App\User::class)->onlyMethods(['hasPermissionTo'])->getMock(); $user->id = 8;
        $user->method('hasPermissionTo')->willReturnCallback(fn($permission) => in_array($permission, $grants, true)); Auth::setUser($user); return $user;
    }
    private function invoice($suffix = 'one')
    {
        $invoice = ClientInvoices::create(['invoice_no' => 'QA-' . $suffix, 'date' => '2026-10-30', 'client_id' => 1, 'tour_id' => 200, 'amount_receiveable' => 240]);
        DB::table('invoice_items')->insert(['invoice_id' => $invoice->id, 'item_name' => 'Preserved service', 'amount' => 100]); return $invoice;
    }
    public function test_archive_preserves_header_items_and_staff_audit_metadata()
    {
        $invoice = $this->invoice(); $this->user();
        app()->instance('request', Request::create('/', 'POST', [], [], [], ['HTTP_ACCEPT' => 'application/json']));
        $response = (new ClientInvoiceController)->destroy($invoice->id);
        $this->assertSame(200, $response->getStatusCode()); $this->assertNull(ClientInvoices::find($invoice->id));
        $archived = ClientInvoices::withTrashed()->find($invoice->id); $this->assertTrue($archived->trashed()); $this->assertEquals(8, $archived->deleted_by);
        $item = DB::table('invoice_items')->first(); $this->assertNotNull($item->deleted_at); $this->assertSame('Preserved service', $item->item_name); $this->assertSame(1, DB::table('client_invoices')->count());
    }
    public function test_archive_is_atomic_when_invoice_delete_fails()
    {
        $invoice = $this->invoice();
        ClientInvoices::deleting(function () { throw new \RuntimeException('Simulated delete failure'); });
        try { (new ClientInvoiceController)->destroy($invoice->id); $this->fail('Expected failure'); }
        catch (\RuntimeException $e) { $this->assertNull(DB::table('invoice_items')->value('deleted_at')); $this->assertNotNull(ClientInvoices::find($invoice->id)); }
        finally { ClientInvoices::flushEventListeners(); }
    }
    public function test_paginated_search_and_payment_sum_use_constant_query_count()
    {
        for ($i = 0; $i < 31; $i++) $this->invoice((string)$i);
        DB::table('transactions')->insert([['invoice_id' => 31, 'pay_to' => 'Client', 'amount' => 50], ['invoice_id' => 31, 'pay_to' => 'Supplier', 'amount' => 999]]);
        DB::enableQueryLog(); DB::flushQueryLog();
        $request = Request::create('/accounting', 'GET', ['search' => 'QA']); app()->instance('request', $request);
        $page = (new ClientInvoiceController)->index($request)->getData()['accountingData'];
        $this->assertLessThanOrEqual(4, count(DB::getQueryLog())); DB::disableQueryLog();
        $this->assertSame(25, $page->count()); $this->assertSame(31, $page->total()); $this->assertSame('They Owe 190', $page->first()->Status);
        $this->assertStringContainsString('search=QA', $page->nextPageUrl());
        $this->assertSame(31, (new ClientInvoiceController)->index(Request::create('/', 'GET', ['search' => 'QA Client']))->getData()['accountingData']->total());
        $this->assertSame(0, (new ClientInvoiceController)->index(Request::create('/', 'GET', ['search' => 'no match']))->getData()['accountingData']->total());
    }
    public function test_service_transaction_uses_tour_id_and_preselects_client_and_quotation()
    {
        DB::table('quotations')->insert(['id' => 19, 'tour_id' => 200, 'is_confirm' => 1]);
        $view = (new ClientInvoiceController)->serviceTransactionCreate(200, Request::create('/'));
        $this->assertSame('accounting.create', $view->name()); $this->assertEquals(200, $view->getData()['selectedTourId']);
        $this->assertEquals(1, $view->getData()['selectedClientId']); $this->assertEquals(19, $view->getData()['quotation']->id);
        $this->assertSame(0, ClientInvoices::count());
    }
    public function test_quote_lookup_and_service_creation_use_existing_permissions()
    {
        foreach (['/accountingServiceTransaction/create/200', '/accounting/api/getTourquotation/200', '/accounting/1/deleteMsg'] as $url) {
            $request = Request::create($url); $route = app('router')->getRoutes()->match($request); $request->setRouteResolver(fn() => $route);
            $expected = strpos($url, 'deleteMsg') !== false ? 'transactions.destroy' : 'transactions.create'; $this->assertSame($expected, $route->getAction('permissions'));
            $request->setUserResolver(fn() => new class { public $id = 1; public function hasPermissionTo($p) { return false; } });
            try { (new \App\Http\Middleware\PermissionsRequiredMiddleware)->handle($request, fn() => response('bypass')); $this->fail('Permission bypass'); }
            catch (\Symfony\Component\HttpKernel\Exception\HttpException $e) { $this->assertSame(403, $e->getStatusCode()); }
            $request->setUserResolver(fn() => new class { public function hasPermissionTo($p) { return true; } });
            $this->assertSame('allowed', (new \App\Http\Middleware\PermissionsRequiredMiddleware)->handle($request, fn() => response('allowed'))->getContent());
        }
    }
    public function test_http_denies_unprivileged_accounting_and_lookup_access()
    {
        $this->actingAs($this->user());
        foreach (['/accounting', '/accounting/create', '/accountingServiceTransaction/create/200', '/accounting/api/getTourquotation/200', '/api/getInvoiceItem?invoice_id=1'] as $url) $this->getJson($url)->assertForbidden();
    }
    public function test_missing_tour_and_archived_invoice_are_not_found()
    {
        $this->user(['transactions.show']); $invoice = $this->invoice(); (new ClientInvoiceController)->destroy($invoice->id);
        try { (new ClientInvoiceController)->getInvoiceItem(Request::create('/', 'GET', ['invoice_id' => $invoice->id])); $this->fail('Archived data read'); }
        catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) { $this->assertTrue(true); }
        $this->expectException(\Illuminate\Database\Eloquent\ModelNotFoundException::class); (new ClientInvoiceController)->getTourquotation(999999);
    }
}
