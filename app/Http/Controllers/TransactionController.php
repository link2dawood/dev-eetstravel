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

		return view('transaction.index', compact('transactions', 'accounts'));
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
	public function data(Request $request)
	{

		$transactions = Transaction::all();

		$permission_destroy = PermissionHelper::$relationsPermissionDestroy['App\Invoices'];
		$permission_edit = PermissionHelper::$relationsPermissionEdit['App\Invoices'];
		$permission_show = PermissionHelper::$relationsPermissionShow['App\Invoices'];

		$perm = [];
		$perm['show'] = Auth::user()->can($permission_show);
		$perm['edit'] = Auth::user()->can($permission_edit);
		$perm['destroy'] = Auth::user()->can($permission_destroy);
		$perm['clone'] = Auth::user()->can('accounting.create');

		return Datatables::of($transactions)

			->addColumn('invoice_no', function ($transactions) use ($perm) {
				if ($transactions->pay_to == "Supplier") {
					$invoice = Invoices::find($transactions->invoice_id);
				} else {
					$invoice = ClientInvoices::find($transactions->invoice_id);
				}
				

				return $invoice->invoice_no;
			})
			->addColumn('unallocated', function ($transactions) use ($perm) {
				if ($transactions->pay_to == "Supplier") {
					$invoice = Invoices::find($transactions->invoice_id);
				} else {
					$invoice = ClientInvoices::find($transactions->invoice_id);
				}
				$invoice_amount = $invoice->total_amount;
				$transaction_amount = $transactions->amount;
				$unallocated = $invoice_amount - $transaction_amount;

				return $unallocated;
			})

			->addColumn('action', function ($transactions) use ($perm) {

				return $this->getShowButton($transactions->id, false, $transactions, $perm);
			})
			->rawColumns(['select', 'action', 'link'])
			->make(true);
	}
	function updateDeferredRevenueToSalesRevenue()
	{
		$today = Carbon::now();
		$pastTransactions = TransDetail::where('trans_date', '<', $today)
			->where('account_no', '401') // Filter only entries with deferred revenue
			->get();

		foreach ($pastTransactions as $transaction) {
			$transation =   DB::table('trans_detail')->where('id', $transaction->id);
			$transaction->update([
				'account_id' => 5,
				'account_no' => 501, // Set deferred_revenue to 0
				'account_desc' => "Sales Revenue", // Add deferred_revenue to sales_revenue
			]);
		}
	}
	function updatePayableToCash()
	{
		$today = Carbon::now();
		$pastTransactions = TransDetail::where('trans_date', '<', $today)
			->where('account_no', '301') // Filter only entries with deferred revenue
			->get();

		foreach ($pastTransactions as $transaction) {
			$amount =  $transaction->amount;
			$amount = "-" . $amount;
			$transaction->account_id = 1;
			$transaction->account_no = 101;
			$transaction->account_desc = "Cash";
			$transaction->amount = $amount;
			$transaction->save();
		}
	}
	public function store(Request $request)
	{
		
		$data_validate = $this->validate($request, [
			'date' => 'required|date',
			'pay_to' => 'required',
			'invoice_id' => 'required',
			'amount' => 'required',
		]);

		$transaction = Transaction::create($request->except("data"));
		$transaction->trans_no = 'TXN-' . uniqid();
		$transaction->save();

		

		

		LaravelFlashSessionHelper::setFlashMessage("Transaction created Successfully", 'success');
		return redirect()->route('transaction.index');
	}
	public function DeleteMsg($id, Request $request)
	{

		$msg = Ajaxis::BtDeleting('Warning!!', 'Would you like to remove This?', '/transaction/' . $id . '/delete');
		if ($request->ajax()) {
			return $msg;
		}
	}

	public function destroy($id)
	{
		$transaction = Transaction::find($id)->delete();


		LaravelFlashSessionHelper::setFlashMessage("Transaction deleted", 'success');

		return URL::to('transaction');
	}
}
