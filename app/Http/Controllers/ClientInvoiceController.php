<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Redirect;
use Illuminate\Http\Request;
use Image;
use DB;
use App\Attachment;
use App\User;
use App\Status;
use App\RoomTypes;
use App\Tour;
use App\Quotation;
use App\ClientInvoices;
use App\Invoices;
use App\Client;
use App\Hotel;
use App\Event;
use App\Restaurant;
use App\Guide;
use App\Bus;
use App\Tax;
use Yajra\Datatables\Datatables;
use App\Helper\ExportTrait;
use Carbon\Carbon;
use App\Helper\HelperTrait;
use App\Helper\LaravelFlashSessionHelper;
use App\Helper\PermissionHelper;
use App\TourDay;
use App\Transaction;
use App\Helper\TourPackage\TourService;
use Auth;
use View;
use Amranidev\Ajaxis\Ajaxis;
use URL;
use App\Offices;
use PDF;
use App\Services\InvoicePayments;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Schema;

class ClientInvoiceController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $clientInvoices = ClientInvoices::with(['client'])->orderBy('date', 'desc')->get();

        $permission_edit = PermissionHelper::$relationsPermissionEdit['App\ClientInvoices'] ?? 'accounting.edit';
        $permission_destroy = PermissionHelper::$relationsPermissionDestroy['App\ClientInvoices'] ?? 'accounting.destroy';
        $permission_show = PermissionHelper::$relationsPermissionShow['App\ClientInvoices'] ?? 'accounting.show';

        $perm = [];
        $perm['show'] = Auth::user()->can($permission_show);
        $perm['edit'] = Auth::user()->can($permission_edit);
        $perm['destroy'] = Auth::user()->can($permission_destroy);
        $perm['clone'] = Auth::user()->can('accounting.create') ?? false;

        // Process each invoice to add computed fields
        $accountingData = $clientInvoices->map(function ($invoice) use ($perm) {
            // Tour name
            $tour = Tour::find($invoice->tour_id);
            $invoice->tourName = $tour->name ?? '';

            // Client name
            $client = Client::find($invoice->client_id);
            $invoice->clientName = $client->name ?? '';

            // Status calculation
            $transaction = Transaction::where("invoice_id", $invoice->id)->where("pay_to", "Client");
            $sum_amount = $transaction->sum("amount");
            $amount = $invoice->amount_receiveable ?? 0;
            $remaining_amount = $amount - $sum_amount;

            if ($sum_amount == $amount) {
                $result = "Paid";
            } elseif ($sum_amount == 0) {
                $result = "They Owe " . $amount;
            } else {
                $result = "They Owe " . $remaining_amount;
            }
            $invoice->Status = $result;

            // Action buttons will be generated in the view using the action_buttons component

            return $invoice;
        });

        return view('accounting.index', compact('accountingData'));
    }
    
    public function getButton($id, $isQuotation = false, $tour, array $perm)
    {
        $url = array(
            'show'       => route('accounting.show', ['accounting' => $id]),
            'edit'       => route('accounting.edit', ['accounting' => $id]),
            'delete_msg' => "/accounting/{$id}/deleteMsg",
            'id'         => $id
        );

        return DatatablesHelperController::getActionButtonTours($url, $isQuotation, $perm);
        //        return DatatablesHelperController::getActionButton($url, $isQuotation, $tour);
    }
    public function getShowButton($id, $isQuotation = false, $tour, array $perm)
    {
        $url = array(
            'show'       => route('accounting.show', ['accounting' => $id]),
            'edit'       => route('accounting.edit', ['accounting' => $id]),
            'delete_msg' => "/accounting/{$id}/deleteMsg",
            'id'         => $id
        );

        return DatatablesHelperController::getShowButton($url);
        //        return DatatablesHelperController::getActionButton($url, $isQuotation, $tour);
    }


    public function serviceTransactionCreate($tourId, Request $request)
    {
        $transactions = ClientInvoices::find($tourId);
        $tour = Tour::find($transactions->tour_id);
    $offices = Schema::hasTable((new Offices)->getTable()) ? Offices::all() : collect();
        $tourName = $tour->name;
        $clients = Client::all();
        $hotels = Hotel::all();
        $events = Event::all();
        $buses = Bus::all();
        $restaurants = Restaurant::all();

        $options = array("Event", "Restaurant", "Bus");
        return view('accounting.service_transaction.create', compact('offices', 'tourName', 'clients', 'hotels', 'options', 'events', 'buses', 'restaurants'));
    }
    public function create(Request $request, $tour_id = null)
    {

        $tour = Tour::all()->first();
        $quotation = $tour ? Quotation::where("tour_id", $tour->id)->where("is_confirm", "1")->first() : null;

    $offices = Schema::hasTable((new Offices)->getTable()) ? Offices::all() : collect();
        $tours = Tour::all();
        $clients = Client::all();
        $hotels = Hotel::all();
        $events = Event::all();
        $buses = Bus::all();
        $restaurants = Restaurant::all();

        $options = array("Event", "Restaurant", "Bus");
		$taxes = Tax::all();
		$count =1;
        return view('accounting.create', compact('offices', 'tours', 'clients', 'hotels', 'options', 'events', 'buses', 'restaurants', 'quotation','taxes','count'));
    }
    public function show($id, Request $request)
    {

        $title = 'Show - Transactions';

        if ($request->ajax()) {
            return URL::to('clients/' . $id);
        }

        $transactions = ClientInvoices::findOrFail($id);
		//dd($transactions->client);
        $tour = Tour::find($transactions->tour_id);

    $office = Schema::hasTable((new Offices)->getTable()) ? Offices::find($transactions->office_id) : null;

        $transactions_cust = ClientInvoices::where("tour_id", $transactions->tour_id)->get();
        $total_amount = 0;
        $extra_amount = 0;
        $amount_payable = 0;
        foreach ($transactions_cust as $transaction_cust) {
            # code...
            $total_amount = $transaction_cust->total_amount + $total_amount;
            $extra_amount = $transaction_cust->extra_amount + $extra_amount;
            $amount_payable = $transaction_cust->amount_payable + $amount_payable;
        }

        if ($transactions == null) {
            return abort(404);
        }


        return view('accounting.show', compact('transactions', 'title', 'tour', 'office', 'total_amount', 'extra_amount', 'amount_payable'));
    }
    public function store(Request $request)
    {
        return $this->saveInvoice($request);
    }
    public function validateTransaction(Request $request)
    {
        return $request->validate([
            'currency' => 'required|in:EUR,USD,CHF',
            'office_id' => 'required|integer|exists:office_fees,id',
            'tour_id' => 'required|integer|exists:tours,id',
            'client_id' => 'required|integer|exists:clients,id',
            'items' => 'required|array|min:1',
            'items.*.item_name' => 'required|string|max:191',
            'items.*.quantity' => 'required|numeric|min:0.01|max:999999',
            'items.*.amount' => 'required|numeric|min:0|max:999999999',
            'items.*.vat' => 'required|numeric|between:0,1',
            'note' => 'nullable|string|max:400',
            'extra_cost' => 'nullable|numeric|min:0',
        ]);
    }

    private function saveInvoice(Request $request, $id = null)
    {
        $this->validateTransaction($request);
        $payments = InvoicePayments::rows($request);
        $items = [];
        foreach ($request->items as $item) {
            $items[] = [
                'item_name' => $item['item_name'], 'quantity' => $item['quantity'],
                'amount' => $item['amount'], 'vat' => $item['vat'],
                'total_amount' => round($item['quantity'] * $item['amount'] * (1 + $item['vat']), 2),
            ];
        }
        $total = round(array_sum(array_column($items, 'total_amount')), 2);
        DB::transaction(function () use ($request, $id, $payments, $items, $total) {
            $invoice = $id ? ClientInvoices::whereKey($id)->lockForUpdate()->firstOrFail() : new ClientInvoices();
            $total = round($total + (float) $request->input('extra_cost', $invoice->extra_cost ?? 0), 2);
            // Payment editing is explicit: a missing/failed async payment form must never erase existing payments.
            $replacePayments = $request->boolean('payments_loaded');
            $alreadyPaid = $id && !$replacePayments ? Transaction::where('invoice_id', $id)->where('pay_to', 'Client')->sum('amount') : 0;
            InvoicePayments::ensureWithinBalance($payments, $total, $alreadyPaid);
            $invoice->fill($request->only(['currency', 'office_id', 'tour_id', 'client_id', 'note', 'extra_cost']));
            $invoice->amount_receiveable = $total;
            if (!$id) {
                $invoice->invoice_no = 'INV-' . \Illuminate\Support\Str::uuid();
                $invoice->date = Carbon::today()->format('Y-m-d');
            }
            $invoice->save();
            DB::table('invoice_items')->where('invoice_id', $invoice->id)->delete();
            foreach ($items as $item) DB::table('invoice_items')->insert($item + ['invoice_id' => $invoice->id]);
            if ($id && $replacePayments) Transaction::where('invoice_id', $id)->where('pay_to', 'Client')->delete();
            InvoicePayments::save($payments, $invoice->id, 'Client');
        });
        return redirect()->route('accounting.index');
    }
    public function edit($id, Request $request)
    {
        $transactions = ClientInvoices::findOrFail($id);
    $offices = Schema::hasTable((new Offices)->getTable()) ? Offices::all() : collect();
        $tours = Tour::all();
        $clients = Client::all();
        $quotation = Quotation::find($transactions->quotation_id);
        return view('accounting.edit', compact('tours', 'clients', 'transactions', 'offices', 'quotation'));
    }
    public function update($id, Request $request)
    {
        return $this->saveInvoice($request, $id);
    }
    public function DeleteMsg($id, Request $request)
    {
        $msg = Ajaxis::BtDeleting('Warning!!', 'Would you like to remove This?', '/accounting/' . $id . '/delete');

        if ($request->ajax()) {
            return $msg;
        }
    }
    public function destroy($id)
    {
        DB::transaction(function () use ($id) {
            $invoice = ClientInvoices::whereKey($id)->lockForUpdate()->firstOrFail();
            if (Transaction::where('invoice_id', $id)->where('pay_to', 'Client')->exists()) {
                throw \Illuminate\Validation\ValidationException::withMessages(['invoice' => 'An invoice with payments cannot be deleted.']);
            }
            DB::table('invoice_items')->where('invoice_id', $id)->delete();
            $invoice->delete();
        });
        return request()->ajax() ? URL::to('accounting') : redirect()->route('accounting.index');
    }
    public function supplierdropdown($id, Request $request)
    {
        $tour = Tour::find($id);

        $tourDates = $this->prepareTourPackages($tour, $request)['tourDates'];
        $package_name = '<lable for= "service" id = "lable-service' . $tour->id . '">' . $tour->name . '</label>
    <select id="service' . $tour->id . '"  name="package_id' . $tour->id . '[]" class="form-control tour_select"  multiple="multiple" required>';
        $n = 1;
        $previousDate = null;
        $count = 0;
        foreach ($tourDates as $tourDate) {
            //  dd(is_empty(count($tourDate->packages)));
            if (count($tourDate->packages) != 0) {
                # code...
                foreach ($tourDate->packages as $package) {
                    if ($package->name !== null and $package->paid == "No") {
                        $currentDate = date('d-M-Y', strtotime($package->time_from));
                        /*
                foreach ($package->room_types_hotel as $item){
                    $listRoomsHotel[] = $item;
                }*/
                        if ($previousDate !== null) {
                            if ($currentDate > $previousDate) {
                                $n++;
                                $package_name .= '<option disabled>Day ' . $n . '</option>';
                            }
                        } else {
                            $package_name .= '<option disabled>Day 1</option>';
                        }

                        $listIdServices[] = ['id_service' => $package->reference, 'type_service' => $package->type];
                        $package_name .= "</br><option value = " . $package->id . ">" . $currentDate
                            . " : " . $package->name . "</option>";


                        $previousDate = $currentDate;
                    }
                }
            } else {
                $package_name .= "<option disabled>Please Select Services</option>";
                break;
            }
        }
        //dd($tourDates);
        $package_name .= '</select></br>';

        return  array($package_name);
    }
    public function prepareTourPackages($tour, Request $request)
    {
        $tourDates = TourDay::with('packages')->where('tour', $tour->id)->get()->sortBy('date');
        //echo (count($tourDates))."<br>";
        $tourPackageType = TourService::$serviceTypes;
        $last = '';

        foreach ($tourDates as $tourDate) {
            if ($request->pdf_type == 'voucher') $tourDate->packages = $tourDate->packages->where('description_package', null);
            $last_package = $tourDate->packages->last();
            if ($last_package) $last = $last_package->id;
            foreach ($tourDate->packages as $package) {
                if ($package->status) {
                    $package->status = $package->getStatusName();
                }
                $package->paid = $package->paid ? 'Yes' : 'No';
                // $package->type = $tourPackageType[$package->type];
                $package->issued_by = $request->user()->name;
                // $package->assigned_user = User::findOrFail($tour->assigned_user)->name;
            }
        }
        if ($request->input('exclude') > 0 && $request->pdf_type  !== 'voucher') {
            foreach ($tourDates as $tourDate) {

                if ($request->pdf_type == 'voucher') $tourDate->packages = $tourDate->packages->where('description_package', null);

                foreach ($tourDate->packages as $id => $package) {
                    if (in_array($package->id, $request->input('exclude'))) {
                        unset($tourDate->packages[$id]);
                    }
                }
            }
        }

        return ['tourDates' => $tourDates, 'tour' => $tour, 'last' => $last];
    }

    public function pdfExport(Request $request, $id)
    {
        return PDF::loadView('export.accounting.invoice', $this->invoiceExportData($id))->setPaper('a4')->stream('invoice-' . $id . '.pdf');
    }

    private function invoiceExportData($id): array
    {
        $invoice = ClientInvoices::findOrFail($id);
        return [
            'invoice' => $invoice, 'office' => Offices::findOrFail($invoice->office_id),
            'client' => Client::findOrFail($invoice->client_id), 'tour' => Tour::findOrFail($invoice->tour_id),
            'items' => DB::table('invoice_items')->where('invoice_id', $id)->get(),
            'payments' => Transaction::where('invoice_id', $id)->where('pay_to', 'Client')->orderBy('date')->get(),
        ];
    }
    public function getItemInvoiceView(Request $request)
    {
        $count = $request->get('itemCount');
		$taxes = Tax::all();
        $view = View::make('component.invoice_item_form', compact('count','taxes'));

        return $view->render();
    }
	public function getInvoiceItem(Request $request)
    {
		$count = $request->get('itemCount');
        $invoice_id = $request->get('invoice_id');
		$taxes = Tax::all();
		$invoice_items = DB::table('invoice_items')->where('invoice_id', $invoice_id)->get();
		
        $view = View::make('component.invoice_items', compact('count','taxes','invoice_items'));

        return $view->render();
    }
    public function getTourquotation($tour_id)
    {
        $tour = Tour::find($tour_id);
        $quotation = $tour ? Quotation::where("tour_id", $tour->id)->where("is_confirm", "1")->first() : null;
        if (!empty($quotation)) {
            $input =$quotation->id;
        } else {
            $input ="";
        }
        return $input;
    }
    public function excelExport(Request $request, $id, string $export = "xlsx")
    {
        $data = $this->invoiceExportData($id);
        $sheet = new class($data) implements \Maatwebsite\Excel\Concerns\FromView {
            private array $data;
            public function __construct(array $data) { $this->data = $data; }
            public function view(): \Illuminate\Contracts\View\View { return view('export.accounting.invoice', $this->data); }
        };
        return Excel::download($sheet, 'invoice-' . $id . '.xlsx');
    }
    public function prepareExport($tour, $office, $client, $transactions, $tourDates, $quotation, $calculations, $tourdays, $invoice_items, $export)
    {

        $excelName = str_replace(" ", "_", $tour->name);
        $exportClass = new class($tour, $office, $client, $transactions, $tourDates, $quotation, $calculations, $tourdays, $invoice_items) implements \Maatwebsite\Excel\Concerns\FromView {
            protected $tour, $office, $client, $transactions, $tourDates, $quotation, $calculations, $tourdays, $invoice_items;
            public function __construct($tour, $office, $client, $transactions, $tourDates, $quotation, $calculations, $tourdays, $invoice_items) {
                $this->tour = $tour;
                $this->office = $office;
                $this->client = $client;
                $this->transactions = $transactions;
                $this->tourDates = $tourDates;
                $this->quotation = $quotation;
                $this->calculations = $calculations;
                $this->tourdays = $tourdays;
                $this->invoice_items = $invoice_items;
            }
            public function view(): \Illuminate\Contracts\View\View {
                // properties, not locals: compact() here threw "Undefined variable"
                return view('export.accounting.billingExcel', get_object_vars($this));
            }
        };
        return Excel::download($exportClass, 'Invoice' . $excelName . '.' . $export);
    }

    public function getPaymentView(Request $request){
        $count = $request->get('itemCount');
        $view = View::make('component.payment_form', compact('count'));

        return $view->render();
    }
	public function getInvoicePayments($pay_to,Request $request)
    {
		$count = $request->get('itemCount');
        $invoice_id = $request->get('invoice_id');
		
		if($pay_to == 2){
        $payments = Transaction::where("invoice_id", $invoice_id)->where("pay_to", "Supplier")->get();
		}
		else{
			$payments = Transaction::where("invoice_id", $invoice_id)->where("pay_to", "Client")->get();
		}
		

		
        $view = View::make('component.get_payment_form', compact('count','payments'));

        return $view->render();
    }
	public function add_payment(Request $request,$id)
    {
        $transactions = ClientInvoices::findOrFail($id);
    $offices = Schema::hasTable((new Offices)->getTable()) ? Offices::all() : collect();
        $tours = Tour::all();
        $clients = Client::all();
        $quotation = Quotation::find($transactions->quotation_id);
        return view('accounting.payment_create', compact('tours', 'clients', 'transactions', 'offices', 'quotation'));
    }
    public function payment_store($id, Request $request)
    {
        $rows = InvoicePayments::rows($request, true);
        DB::transaction(function () use ($id, $rows) {
            $invoice = ClientInvoices::whereKey($id)->lockForUpdate()->firstOrFail();
            $paid = Transaction::where('invoice_id', $id)->where('pay_to', 'Client')->sum('amount');
            InvoicePayments::ensureWithinBalance($rows, $invoice->amount_receiveable, $paid);
            InvoicePayments::save($rows, $id, 'Client');
        });
        return redirect()->route('accounting.show', $id);
    }
}
