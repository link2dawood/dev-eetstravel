<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Offices;
use App\Client;
use App\Tour;
use App\ClientInvoices;
use App\Invoices;
use App\InvoicesTours;
use App\Transaction;

class AccountingSampleSeeder extends Seeder
{
    public function run()
    {
        DB::transaction(function () {
            $office = Offices::firstOrCreate(['office_name' => 'QA SAMPLE Accounting Office'], [
                'office_address' => 'Sample address - test data only', 'bank_name' => 'Sample Bank',
                'account_no' => 'TEST-ACCOUNT-001', 'swift_code' => 'TESTONLY', 'tel' => '', 'fax' => '',
            ]);
            $partner = Offices::firstOrCreate(['office_name' => 'QA SAMPLE Partner Office'], [
                'office_address' => 'Sample partner address', 'bank_name' => 'Sample Bank',
                'account_no' => 'TEST-ACCOUNT-002', 'swift_code' => 'TESTONLY', 'tel' => '', 'fax' => '',
            ]);
            $client = Client::firstOrCreate(['name' => 'QA SAMPLE Accounting Client'], [
                'country' => 'AT', 'work_email' => 'accounting-sample@example.invalid',
                'password' => Hash::make(\Illuminate\Support\Str::random(40)),
                'address' => 'Sample client address - test data only',
            ]);
            $tour = Tour::where('name', 'QA SAMPLE Accounting Tour')->first();
            if (!$tour) {
                $template = Tour::firstOrFail();
                $tour = $template->replicate();
                $tour->name = 'QA SAMPLE Accounting Tour';
                $tour->client_id = $client->id;
                $tour->overview = 'QA SAMPLE tour for accounting tests only.';
                $tour->remark = 'QA SAMPLE - no real booking or supplier reservation.';
                $tour->pax = 2;
                $tour->pax_free = 0;
                $tour->total_amount = 0;
                $tour->price_for_one = 0;
                $tour->external_name = 'qa-sample-accounting-' . \Illuminate\Support\Str::random(8);
                $tour->departure_date = now()->addDays(14)->toDateString();
                $tour->retirement_date = now()->addDays(16)->toDateString();
                $tour->save();
            }
            foreach ([['UNPAID', 0], ['PARTIAL', 50], ['PAID', 240]] as [$label, $paid]) {
                $invoice = ClientInvoices::firstOrCreate(['invoice_no' => 'QA-SAMPLE-CLIENT-' . $label], [
                    'date' => now()->toDateString(), 'currency' => 'EUR', 'office_id' => $office->id,
                    'tour_id' => $tour->id, 'client_id' => $client->id, 'amount_receiveable' => 240,
                    'extra_cost' => 0, 'note' => 'QA SAMPLE - test invoice only. No real payment.',
                ]);
                if (!DB::table('invoice_items')->where('invoice_id', $invoice->id)->exists()) {
                    DB::table('invoice_items')->insert(['invoice_id' => $invoice->id, 'item_name' => 'QA SAMPLE service', 'quantity' => 2, 'amount' => 100, 'vat' => 0.2, 'total_amount' => 240]);
                }
                if ($paid) Transaction::firstOrCreate(['trans_no' => 'QA-SAMPLE-CLIENT-' . $label], ['invoice_id' => $invoice->id, 'date' => now()->toDateString(), 'pay_to' => 'Client', 'amount' => $paid, 'payment_method' => 'Business Debit Card']);
            }
            $supplier = Invoices::firstOrCreate(['invoice_no' => 'QA-SAMPLE-SUPPLIER-PARTIAL'], ['office_id' => $office->id, 'dueDate' => now()->addDays(7)->toDateString(), 'receivedDate' => now()->toDateString(), 'total_amount' => 300, 'extra_amount' => 0, 'note' => 'QA SAMPLE - no real payment.']);
            InvoicesTours::firstOrCreate(['invoices_id' => $supplier->id, 'invoices_tours_id' => $tour->id, 'package_id' => 0]);
            Transaction::firstOrCreate(['trans_no' => 'QA-SAMPLE-SUPPLIER-PARTIAL'], ['invoice_id' => $supplier->id, 'date' => now()->toDateString(), 'pay_to' => 'Supplier', 'amount' => 75, 'payment_method' => 'Business Debit Card']);
            foreach ([['QA SAMPLE Zero VAT', 0], ['QA SAMPLE VAT 20%', 20]] as [$name, $value]) DB::table('invoice_taxes')->updateOrInsert(['name' => $name], ['value' => $value]);
            foreach ([
                ['office_tours', ['tour_name' => 'QA SAMPLE Tour expense'], ['tour_expenses' => 100, 'tour_departure_date' => now()->addDays(14)->toDateString(), 'tour_return_date' => now()->addDays(16)->toDateString()]],
                ['office_utility_expenses', ['subject_of_expense' => 'QA SAMPLE Utilities'], ['month' => now()->startOfMonth()->toDateString(), 'monthly_expense' => 25.50]],
                ['office_employes_salary', ['employe_name' => 'QA SAMPLE Employee'], ['month' => now()->startOfMonth()->toDateString(), 'employe_salary' => 200, 'bonuses' => '10']],
                ['office_earnings', ['month' => now()->startOfMonth()->toDateString()], ['revenue' => 1000, 'profit' => 400]],
                ['office_balance', ['subject_of_balance' => 'QA SAMPLE Balance'], ['month' => now()->startOfMonth()->toDateString(), 'total_amount' => 64.50, 'due_date' => now()->addDays(7)->toDateString()]],
            ] as [$table, $identity, $values]) {
                DB::table($table)->updateOrInsert($identity + ['office_id' => $office->id], $values);
            }
            $header = DB::table('officeinvoice_data')->where('invoice_no', 'QA-SAMPLE-OFFICE-001')->first();
            if (!$header) {
                $headerId = DB::table('officeinvoice_data')->insertGetId(['from_office' => $office->id, 'to_office' => $partner->id, 'invoice_no' => 'QA-SAMPLE-OFFICE-001', 'date' => now()->toDateString()], 'officeinvoice_dataId');
                DB::table('office_invoices')->insert(['officeinvoice_dataId' => $headerId, 'officeinvoice_item' => 'QA SAMPLE Office service', 'officeinvoice_date' => now()->toDateString(), 'officeinvoice_code' => 'QA-001', 'officeinvoice_amount' => 125.50, 'des' => 'Sample interoffice invoice']);
            }
            $this->command?->info('QA SAMPLE accounting records are ready. No emails or external payments were sent.');
        });
    }
}
