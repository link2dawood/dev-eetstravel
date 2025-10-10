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
			'show'       => route('transaction.show', ['id' => $id]),
			'edit'       => route('transaction.edit', ['id' => $id]),
			'delete_msg' => "/transaction/{$id}/deleteMsg",
			'id'         => $id
		);

		return DatatablesHelperController::getShowButton($url) . '<a class="delete btn btn-danger btn-sm" style="margin-right: 5px" data-toggle="modal" data-target="#myModal" data-link="' . $url["delete_msg"] . '"><i class="fa fa-trash-o"></i></a>';
		//        return DatatablesHelperController::getActionButton($url, $isQuotation, $tour);
	}

	public function index()
	{
		$this->updateDeferredRevenueToSalesRevenue();
		$this->updatePayableToCash();
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
				$invoice_amount = $invoice->total_amount;
				$transaction_amount = $transaction->amount;
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
