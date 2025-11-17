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
    $offices = Schema::hasTable('offices') ? Offices::all() : collect();
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
        $quotation = Quotation::where("tour_id", $tour->id)->where("is_confirm", "1")->first();

    $offices = Schema::hasTable('offices') ? Offices::all() : collect();
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

        $transactions = ClientInvoices::find($id);
		//dd($transactions->client);
        $tour = Tour::find($transactions->tour_id);

    $office = Schema::hasTable('offices') ? Offices::find($transactions->office_id) : null;

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

        $validatedData = $this->validateTransaction($request);
		$currentDate = Carbon::now();
        $invoice_no = 'INV-' . uniqid();
        $extra_amount = 0;
        $items = $request->items;
        if ($items) {
            foreach ($items as $item) {
                $extra = $item["quantity"] * $item["amount"];
                $extra_amount += $extra ;
            }
        }
        $total_amount =  $extra_amount;

        $payment_methods = $request->payment_method;
        $payment_date = $request->paymentdate;
        $paid_amount = $request->paid_amount;
        if(!empty($paid_amount)){
            $payment_amount = 0;
            foreach ($paid_amount as $paidamount) {
                $payment_amount += $paidamount;
            }
         
            if( $total_amount< $payment_amount){
                LaravelFlashSessionHelper::setFlashMessage("Payments Cannot be  Greather than total Amount", 'error');
             
                return redirect()->back();
             
            }
        }
		
        $transaction = ClientInvoices::create([
            "invoice_no"=>$invoice_no,
            "date"=> $currentDate->addWeek(2),
			"currency"=> $request->currency,
            "office_id"=> $request->office_id,
            "tour_id"=> $request->tour_id,
            "client_id"=> $request->client_id,
            "amount_receiveable"=> $total_amount,
			"extra_cost"=>  $request->extra_cost,
			"note"=>  $request->note,
        ]);
        
        if ($items) {
            foreach ($items as $item) {
                try {
                    if (!empty($item["item_name"]) and !empty($item["quantity"]) and  !empty($item["amount"])) {
                        DB::table('invoice_items')->insert([
                            'item_name' => $item["item_name"],
                            'quantity' => $item["quantity"],
							'vat' => $item["vat"],
                            'amount' => $item["amount"],
							'total_amount' => $item["total_amount"],
                            "invoice_id" => $transaction->id,
                        ]);
                    }
                } catch (\Exception $e) {
                    dd($e->getMessage());
                }
            }
        }
        $payment_methods = $request->payment_method;
        $payment_date = $request->paymentdate;
        $paid_amount = $request->paid_amount;
        $i = 0;
        if(!empty($payment_methods)){
        foreach ($payment_methods as $payment_method) {
            // dd($payment_method == "null");
            if ($payment_method == "null" ||  $payment_date[$i] == "null" ||   $paid_amount[$i] == "null"  ) {
                dd("ok");
            } else {
               // dd($payment_method);
                $transaction = Transaction::create([
                    "date" =>  $payment_date[$i],
                    "trans_no" => 'TXN-' . uniqid(),
                    "amount" => $paid_amount[$i],
                    "pay_to" => "Client",
                    "invoice_id" => $transaction->id,
                    "payment_method" => $payment_method,
                ]);
            }
            $i += 1;
        }
        }
        return redirect()->route('accounting.index');
    }
    public function validateTransaction(Request $request)
    {
        // Build rules dynamically: require office only when the table exists and has rows.
        $rules = [
            'currency' => 'required',
            'tour_id' => 'required',
        ];

        try {
            if (Schema::hasTable('offices') && Offices::count() > 0) {
                $rules['office_id'] = 'required';
            } else {
                // No offices available (or table missing) — accept office_id as nullable so form can be submitted.
                $rules['office_id'] = 'nullable';
            }
        } catch (\Exception $e) {
            // In case of any DB/schema issues, fallback to nullable to avoid blocking the form.
            \Log::warning('validateTransaction: could not check offices table: ' . $e->getMessage());
            $rules['office_id'] = 'nullable';
        }

        $this->validate($request, $rules);
    }
    public function edit($id, Request $request)
    {
        $transactions = ClientInvoices::find($id);
    $offices = Schema::hasTable('offices') ? Offices::all() : collect();
        $tours = Tour::all();
        $clients = Client::all();
        $quotation = Quotation::find($transactions->quotation_id);
        return view('accounting.edit', compact('tours', 'clients', 'transactions', 'offices', 'quotation'));
    }
    public function update($id, Request $request)
    {
        $this->validateTransaction($request);
		
		$extra_amount = 0;
        $items = $request->items;
        if ($items) {
            foreach ($items as $item) {
                $extra = $item["quantity"] * $item["amount"];
				$extra = $extra + ($extra *  $item["vat"] );
                $extra_amount += $extra ;
            }
        }
        $total_amount = $extra_amount;

        $payment_methods = $request->payment_method;
        $payment_date = $request->paymentdate;
        $paid_amount = $request->paid_amount;
        if(!empty($paid_amount)){
            $payment_amount = 0;
            foreach ($paid_amount as $paidamount) {
                $payment_amount += $paidamount;
            }
         
            if( $total_amount< $payment_amount){
                LaravelFlashSessionHelper::setFlashMessage("Payments Cannot be  Greather than total Amount", 'error');
             
                return redirect()->back();
             
            }
        }
		
        $transaction = ClientInvoices::find($id);
        $transaction->update([
			"currency"=> $request->currency,
            "office_id"=> $request->office_id,
            "tour_id"=> $request->tour_id,
            "client_id"=> $request->client_id,
            "amount_receiveable"=> $total_amount,
		]);
	
		DB::table('invoice_items')->where("invoice_id", $id)->delete();
			
		if ($items) {
            foreach ($items as $item) {
                try {
                    if (!empty($item["item_name"]) and !empty($item["quantity"]) and  !empty($item["amount"])) {
                        DB::table('invoice_items')->insert([
                            'item_name' => $item["item_name"],
                            'quantity' => $item["quantity"],
							'vat' => $item["vat"],
                            'amount' => $item["amount"],
							'total_amount' => $item["total_amount"],
                            "invoice_id" => $transaction->id,
                        ]);
                    }
                } catch (\Exception $e) {
                    dd($e->getMessage());
                }
            }
        }
		
		Transaction::where('invoice_id', $id)->where("pay_to", "Client")->delete();
		
		$payment_methods = $request->payment_method;
        $payment_date = $request->paymentdate;
        $paid_amount = $request->paid_amount;
        $i = 0;
        if(!empty($payment_methods)){
			
			foreach ($payment_methods as $payment_method) {
				// dd($payment_method == "null");
				if ($payment_method == "null" ||  $payment_date[$i] == "null" ||   $paid_amount[$i] == "null"  ) {
					dd("ok");
				} else {
				   // dd($payment_method);
				
					Transaction::create([
						"date" =>  $payment_date[$i],
						"trans_no" => 'TXN-' . uniqid(),
						"amount" => $paid_amount[$i],
						"pay_to" => "Client",
						"invoice_id" => $transaction->id,
						"payment_method" => $payment_method,
					]);
					
				}
				$i += 1;
			}
        }
        return redirect()->route('accounting.index');
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

        $transaction = ClientInvoices::find($id);
        $transaction->find($id)->delete();
        LaravelFlashSessionHelper::setFlashMessage("Transaction $transaction->tours deleted", 'success');

        return URL::to('accounting');
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

        $transactions = ClientInvoices::find($id);
		$tour = Tour::find($transactions->tour_id);
		$quot = $tour->quotations->where("is_confirm", 1)->first();
		if(!empty($quot)){
        $quotation = Quotation::findOrFail($quot->id);
		$calculations = $quotation->calculation;
		}
		else{
			$quotation = [];
			$calculations =[];
		}
        
    $office = Schema::hasTable('offices') ? Offices::find($transactions->office_id) : null;
        $client = Client::find($transactions->client_id);
        $tourDates = $this->prepareTourPackages($tour, $request)['tourDates'];
        $tourdays = $tour->tour_days;
        $invoice_items = DB::table('invoice_items')->where("invoice_id", $transactions->id)->get();

        view()->share([
            'tour' => $tour,
            'office' => $office,
            'client' => $client,
            'transactions' => $transactions,
            'tourDates' => $tourDates,
            'quotation' => $quotation,
            'calculations' => $calculations,
            'tourdays' => $tourdays,
            'invoice_items' => $invoice_items,
        ]);
        PDF::setOptions(['isHtml5ParserEnabled' => true, 'defaultPaperSize' => 'a3']);
        $pdf = PDF::loadView('export.accounting.billingPdf');
        $pdfName = 'invoice.pdf';
        return $pdf->stream(str_replace(" ", "_", $pdfName));
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
        $quotation = Quotation::where("tour_id", $tour->id)->where("is_confirm", "1")->first();
        if (!empty($quotation)) {
            $input =$quotation->id;
        } else {
            $input ="";
        }
        return $input;
    }
    public function excelExport(Request $request, $id, string $export = "xlsx")
    {
		
        $transactions = ClientInvoices::find($id);
		
        $tour = Tour::find($transactions->tour_id);
		$quot = $tour->quotations->where("is_confirm", 1)->first();
		if(!empty($quot)){
        $quotation = Quotation::findOrFail($quot->id);
		$calculations = $quotation->calculation;
		}
		else{
			$quotation = [];
			$calculations =[];
		}
    $office = Schema::hasTable('offices') ? Offices::find($transactions->office_id) : null;
        $client = Client::find($transactions->client_id);
        $tourDates = $this->prepareTourPackages($tour, $request)['tourDates'];
        $tourdays = $tour->tour_days;
        $invoice_items = DB::table('invoice_items')->where("invoice_id", $transactions->id)->get();

        if ($export == 'csv') {

            // $this->csvExport($tour, $type);
        } else $this->prepareExport($tour, $office, $client, $transactions, $tourDates, $quotation, $calculations, $tourdays, $invoice_items, $export);

        return back();
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
                return view('export.accounting.billingExcel', compact('tour', 'office', 'client', 'transactions', 'tourDates', 'quotation', 'calculations', 'tourdays', 'invoice_items'));
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
        $transactions = ClientInvoices::find($id);
    $offices = Schema::hasTable('offices') ? Offices::all() : collect();
        $tours = Tour::all();
        $clients = Client::all();
        $quotation = Quotation::find($transactions->quotation_id);
        return view('accounting.payment_create', compact('tours', 'clients', 'transactions', 'offices', 'quotation'));
    }
	public function payment_store($id,Request $request)
    {
		
		
		$payment_methods = $request->payment_method;
        $payment_date = $request->paymentdate;
        $paid_amount = $request->paid_amount;
        $i = 0;
        if(!empty($payment_methods)){
			
			foreach ($payment_methods as $payment_method) {
				// dd($payment_method == "null");
				if ($payment_method == "null" ||  $payment_date[$i] == "null" ||   $paid_amount[$i] == "null"  ) {
					dd("ok");
				} else {
				   // dd($payment_method);
				
					Transaction::create([
						"date" =>  $payment_date[$i],
						"trans_no" => 'TXN-' . uniqid(),
						"amount" => $paid_amount[$i],
						"pay_to" => "Client",
						"invoice_id" => $id,
						"payment_method" => $payment_method,
					]);
					
				}
				$i += 1;
			}
        }
		

        return redirect()->route("accounting.show" ,$id);
    }
}
