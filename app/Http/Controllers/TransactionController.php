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
			'show'       => route('transaction.show', ['transaction' => $id]),
			'edit'       => route('transaction.edit', ['transaction' => $id]),
			'delete_msg' => "/transaction/{$id}/deleteMsg",
			'id'         => $id
		);

		return DatatablesHelperController::getShowButton($url) . '<a class="delete btn btn-danger btn-sm" style="margin-right: 5px" data-toggle="modal" data-target="#myModal" data-link="' . $url["delete_msg"] . '"><i class="fa fa-trash-o"></i></a>';
		//        return DatatablesHelperController::getActionButton($url, $isQuotation, $tour);
	}

	/**
	 * AUDIT.md CC11 — was Transaction::all() + per-row Invoices::find /
	 * ClientInvoices::find. Now paginate(20) and bulk-load both invoice
	 * tables in two whereIn queries per page.
	 */
	public function index()
	{
		// $this->updateDeferredRevenueToSalesRevenue();
		// $this->updatePayableToCash();
		// — removed: the methods these called were deleted from this
		// controller in an earlier refactor; the callsites were left
		// behind and triggered "Method does not exist" on every /transaction
		// hit. If the accounting transitions are needed elsewhere they
		// should be implemented as services and called from there.

		$perPage = 20;
		$page = Transaction::orderByDesc('id')->paginate($perPage);

		$permission_destroy = PermissionHelper::$relationsPermissionDestroy['App\Invoices'];
		$permission_edit    = PermissionHelper::$relationsPermissionEdit['App\Invoices'];
		$permission_show    = PermissionHelper::$relationsPermissionShow['App\Invoices'];

		$perm = [
			'show'    => Auth::user()->can($permission_show),
			'edit'    => Auth::user()->can($permission_edit),
			'destroy' => Auth::user()->can($permission_destroy),
			'clone'   => Auth::user()->can('accounting.create'),
		];

		// Bulk-load every invoice referenced by THIS page.
		$supplierIds = $page->getCollection()->where('pay_to', 'Supplier')->pluck('invoice_id')->filter()->unique();
		$clientIds   = $page->getCollection()->where('pay_to', '!=', 'Supplier')->pluck('invoice_id')->filter()->unique();
		$supplierInvoices = Invoices::whereIn('id', $supplierIds)->get(['id', 'invoice_no', 'total_amount'])->keyBy('id');
		$clientInvoices   = ClientInvoices::whereIn('id', $clientIds)->get(['id', 'invoice_no', 'amount_receiveable'])->keyBy('id');

		$page->getCollection()->transform(function ($transaction) use ($supplierInvoices, $clientInvoices, $perm) {
			if ($transaction->pay_to === 'Supplier') {
				$invoice = $supplierInvoices->get($transaction->invoice_id);
				$invoiceAmount = $invoice->total_amount ?? null;
			} else {
				$invoice = $clientInvoices->get($transaction->invoice_id);
				// ClientInvoices uses amount_receiveable as its outstanding
				// total; mirror the legacy arithmetic (invoice - transaction).
				$invoiceAmount = $invoice->amount_receiveable ?? null;
			}
			$transaction->invoice_no  = $invoice->invoice_no ?? '';
			$transaction->unallocated = $invoiceAmount !== null
				? ((float) $invoiceAmount - (float) $transaction->amount)
				: 0;
			$transaction->action_buttons = $this->getShowButton($transaction->id, false, $transaction, $perm);
			return $transaction;
		});

		// Accounts dropdown — small reference table; ::all() is fine.
		$accounts = Account::all();

		return view('transaction.index', ['transactionsData' => $page, 'accounts' => $accounts]);
	}
	public function show($id)
	{
		$title = 'Show - Transactions';
		$transaction = Transaction::find($id);
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
}
