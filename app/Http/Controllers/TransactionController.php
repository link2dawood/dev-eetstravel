<?php

namespace App\Http\Controllers;

use App\Transaction;
use App\TransDetail;
use App\Account;
use App\Invoices;
use App\ClientInvoices;
use Illuminate\Http\Request;
use Yajra\Datatables\Datatables;
use App\Helper\LaravelFlashSessionHelper;
use App\Helper\PermissionHelper;
use Auth;
use Amranidev\Ajaxis\Ajaxis;
use URL;
use Illuminate\Support\Str;
use DB;
use Carbon\Carbon;

class TransactionController extends Controller
{
	//
	public function getShowButton($id, $isQuotation = false, $tour, array $perm)
	{
		$url = array(
			'show'       => route('transaction.show', $id),
			'edit'       => route('transaction.edit', $id),
			'delete_msg' => "/transaction/{$id}/deleteMsg",
			'id'         => $id
		);

		$buttons = Auth::user()->can('transaction.show') ? DatatablesHelperController::getShowButton($url) : '';
        if (Auth::user()->can('transaction.edit')) $buttons .= '<a class="btn btn-primary btn-sm" href="' . $url['edit'] . '" aria-label="Edit payment"><i class="ti ti-edit"></i></a>';
        if (Auth::user()->can('transaction.destroy')) $buttons .= '<button type="button" class="delete btn btn-danger btn-sm" data-link="' . url('transaction/' . $id) . '" data-delete-method="DELETE" aria-label="Delete payment"><i class="ti ti-trash"></i></button>';
        return $buttons;
		//        return DatatablesHelperController::getActionButton($url, $isQuotation, $tour);
	}

	public function index()
	{
		$transactions = Transaction::all();
		$accounts = Account::all();

		$permission_destroy = PermissionHelper::$relationsPermissionDestroy['App\Invoices'];
		$permission_edit = PermissionHelper::$relationsPermissionEdit['App\Invoices'];
		$permission_show = PermissionHelper::$relationsPermissionShow['App\Invoices'];

		$perm = [];
		$perm['show'] = Auth::user()->can($permission_show);
		$perm['edit'] = Auth::user()->can($permission_edit);
		$perm['destroy'] = Auth::user()->can($permission_destroy);
		$perm['clone'] = Auth::user()->can('accounting.create');

		// Add computed columns to each transaction
		$transactionsData = $transactions->map(function ($transaction) use ($perm) {
			// Invoice number
			if ($transaction->pay_to == "Supplier") {
				$invoice = Invoices::find($transaction->invoice_id);
			} else {
				$invoice = ClientInvoices::find($transaction->invoice_id);
			}
			$transaction->invoice_no = $invoice->invoice_no ?? '';

			// Unallocated amount
			if ($invoice) {
				$invoice_amount = $transaction->pay_to === 'Client' ? $invoice->amount_receiveable : $invoice->total_amount;
				$transaction_amount = Transaction::where('invoice_id', $transaction->invoice_id)->where('pay_to', $transaction->pay_to)->sum('amount');
				$transaction->unallocated = $invoice_amount - $transaction_amount;
			} else {
				$transaction->unallocated = 0;
			}

			// Action buttons
			$transaction->action_buttons = $this->getShowButton($transaction->id, false, $transaction, $perm);

			return $transaction;
		});

		return view('transaction.index', compact('transactionsData', 'accounts'));
	}
	public function show($id)
	{
		$title = 'Show - Transactions';
		$transaction = Transaction::findOrFail($id);
		$invoice = $transaction->pay_to === 'Client' ? ClientInvoices::find($transaction->invoice_id) : Invoices::find($transaction->invoice_id);
		$transaction->invoice_no = $invoice->invoice_no ?? '';
		$accounts = Account::all();

		return view('transaction.show', compact('transaction', 'accounts'));
	}

	public function create(Request $request)
	{
		$transactions = Transaction::all();
		$accounts = Account::all();
		$invoices = Invoices::all();

		$client_invoices = ClientInvoices::all();

		return view('transaction.create', compact('transactions', 'accounts', 'invoices', 'client_invoices'));
	}

    public function store(Request $request)
    {
        $request->validate([
            'date' => 'required|date_format:Y-m-d', 'pay_to' => 'required|in:Client,Supplier',
            'invoice_id' => 'required|integer', 'amount' => 'required|numeric|min:0.01|max:999999999.99',
        ]);
        DB::transaction(function () use ($request) {
            $model = $request->pay_to === 'Client' ? ClientInvoices::class : Invoices::class;
            $invoice = $model::whereKey($request->invoice_id)->lockForUpdate()->firstOrFail();
            $paid = Transaction::where('invoice_id', $invoice->id)->where('pay_to', $request->pay_to)->sum('amount');
            $rows = [['date' => $request->date, 'amount' => round($request->amount, 2), 'payment_method' => 'Transfer Out from Business Bank']];
            \App\Services\InvoicePayments::ensureWithinBalance($rows, $request->pay_to === 'Client' ? $invoice->amount_receiveable : $invoice->total_amount, $paid);
            \App\Services\InvoicePayments::save($rows, $invoice->id, $request->pay_to);
        });
        return redirect()->route('transaction.index');
    }

    public function edit($id)
    {
        $transaction = Transaction::findOrFail($id);
        return view('transaction.edit', compact('transaction'));
    }

    public function update($id, Request $request)
    {
        $data = $request->validate(['date' => 'required|date_format:Y-m-d', 'amount' => 'required|numeric|min:0.01|max:999999999.99', 'payment_method' => 'required|string|max:191']);
        DB::transaction(function () use ($id, $data) {
            $payment = Transaction::findOrFail($id);
            $model = $payment->pay_to === 'Client' ? ClientInvoices::class : Invoices::class;
            $invoice = $model::whereKey($payment->invoice_id)->lockForUpdate()->firstOrFail();
            $payment = Transaction::whereKey($id)->lockForUpdate()->firstOrFail();
            $others = Transaction::where('invoice_id', $invoice->id)->where('pay_to', $payment->pay_to)->where('id', '<>', $id)->sum('amount');
            \App\Services\InvoicePayments::ensureWithinBalance([$data], $payment->pay_to === 'Client' ? $invoice->amount_receiveable : $invoice->total_amount, $others);
            $payment->update($data);
        });
        return redirect()->route('transaction.show', $id);
    }

    public function DeleteMsg($id, Request $request)
    {
        Transaction::findOrFail($id);
        return Ajaxis::BtDeleting('Warning', 'Remove this payment? The invoice balance will increase.', '/transaction/' . $id . '/delete');
    }

    public function destroy($id)
    {
        Transaction::findOrFail($id)->delete();
        return request()->ajax() ? URL::to('transaction') : redirect()->route('transaction.index');
    }
}
