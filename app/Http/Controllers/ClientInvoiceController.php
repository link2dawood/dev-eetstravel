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

class ClientInvoiceController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */


    public function index()
    {
        // Get all transactions data (same as the AJAX data method)
        $transactions = ClientInvoices::all();
        $permission_destroy = PermissionHelper::$relationsPermissionDestroy['App\ClientInvoices'];
        $permission_edit = PermissionHelper::$relationsPermissionEdit['App\ClientInvoices'];
        $permission_show = PermissionHelper::$relationsPermissionShow['App\ClientInvoices'];

        $perm = [];
        $perm['show'] = Auth::user()->can($permission_show);
        $perm['edit'] = Auth::user()->can($permission_edit);
        $perm['destroy'] = Auth::user()->can($permission_destroy);
        $perm['clone'] = Auth::user()->can('accounting.create');

        // Add computed columns to each transaction
        $accountingData = $transactions->map(function ($transaction) use ($perm) {
            $office = Offices::find($transaction->office_id);
            $transaction->officeName = $office->office_name ?? "";

            $tour = Tour::find($transaction->tour_id);
            $transaction->tourName = $tour->name ?? "";

            $client = Client::find($transaction->client_id);
            $transaction->clientName = $client->name ?? "";

            $transaction->Status = $transaction->status($transaction);
            $transaction->action_buttons = $this->getButton($transaction->id, false, $transaction, $perm);

            return $transaction;
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
    public function data(Request $request)
    {

        $transactions = ClientInvoices::all();
        $permission_destroy = PermissionHelper::$relationsPermissionDestroy['App\ClientInvoices'];
        $permission_edit = PermissionHelper::$relationsPermissionEdit['App\ClientInvoices'];
        $permission_show = PermissionHelper::$relationsPermissionShow['App\ClientInvoices'];

        $perm = [];
        $perm['show'] = Auth::user()->can($permission_show);
        $perm['edit'] = Auth::user()->can($permission_edit);
        $perm['destroy'] = Auth::user()->can($permission_destroy);
        $perm['clone'] = Auth::user()->can('accounting.create');

        return Datatables::of($transactions)
            ->addColumn('officeName', function ($transactions) {
                $office = Offices::find($transactions->office_id);
                return $office->office_name??"";
            })
            ->addColumn('tourName', function ($transactions) {
                $tour = Tour::find($transactions->tour_id);
                return $tour->name??"";
            })
			->addColumn('clientName', function ($transactions) {
                $client = Client::find($transactions->client_id);
                return $client->name??"";
            })
            ->addColumn('Status', function ($transactions) {
               return  $transactions->status($transactions);
            })
            ->addColumn('action', function ($transactions) use ($perm) {
                return $this->getButton($transactions->id, false, $transactions, $perm);
            })
            ->rawColumns(['select', 'action', 'link'])
            ->make(true);
    }
	public function ClientAccountingData(Request $request, $client_id)
    {

        $transactions = ClientInvoices::where("client_id", $client_id)->get();
        $permission_destroy = PermissionHelper::$relationsPermissionDestroy['App\ClientInvoices'];
        $permission_edit = PermissionHelper::$relationsPermissionEdit['App\ClientInvoices'];
        $permission_show = PermissionHelper::$relationsPermissionShow['App\ClientInvoices'];

        $perm = [];
        $perm['show'] = Auth::user()->can($permission_show);
        $perm['edit'] = Auth::user()->can($permission_edit);
        $perm['destroy'] = Auth::user()->can($permission_destroy);
        $perm['clone'] = Auth::user()->can('accounting.create');

        return Datatables::of($transactions)
            ->addColumn('officeName', function ($transactions) {
                $office = Offices::find($transactions->office_id);
                return $office->office_name??"";
            })
            ->addColumn('tourName', function ($transactions) {
                $tour = Tour::find($transactions->tour_id);
                return $tour->name??"";
            })
            ->addColumn('totalAmount', function ($transactions) {
                $transactions_cust = ClientInvoices::where("tour_id", $transactions->tour_id)->get();
                $total = 0;
                foreach ($transactions_cust as $transaction_cust) {
                    # code...
                    $total = $transaction_cust->total_amount + $total;
                }
                return $total;
            })
          
            ->addColumn('extraAmount', function ($transactions) {
                $transactions_cust = ClientInvoices::where("tour_id", $transactions->tour_id)->get();
                $total = 0;
                foreach ($transactions_cust as $transaction_cust) {
                    # code...
                    $total = $transaction_cust->extra_amount + $total;
                }
                return $total;
            })
			->addColumn('clientName', function ($transactions) {
                $client = Client::find($transactions->client_id);
                return $client->name??"";
            })
			->addColumn('Status', function ($transactions) {
               return $transactions->status($transactions);
            })
            ->addColumn('action', function ($transactions) use ($perm) {
                return $this->getButton($transactions->id, false, $transactions, $perm);
            })
            ->rawColumns(['select', 'action', 'link'])
            ->make(true);
    }


    public function serviceTransactionData($pay_to, $invoice_id,Request $request)
    {
		if($pay_to == 2){
        	$transactions = Transaction::where("invoice_id", $invoice_id)->where("pay_to", "Supplier")->get();
		}
		else{
			$transactions = Transaction::where("invoice_id", $invoice_id)->where("pay_to", "Client")->get();
		}
		
        $permission_destroy = PermissionHelper::$relationsPermissionDestroy['App\ClientInvoices'];
        $permission_edit = PermissionHelper::$relationsPermissionEdit['App\ClientInvoices'];
        $permission_show = PermissionHelper::$relationsPermissionShow['App\ClientInvoices'];

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
			->addColumn('total_amount', function ($transactions) use ($perm) {
				if ($transactions->pay_to == "Supplier") {
					$invoice = Invoices::find($transactions->invoice_id);
					$invoice_amount = $invoice->total_amount;
				} else {
					$invoice = ClientInvoices::find($transactions->invoice_id);
					$invoice_amount = $invoice->amount_receiveable;
				}

				return $invoice_amount;
			})
			->addColumn('unallocated', function ($transactions) use ($perm) {
				if ($transactions->pay_to == "Supplier") {
					$invoice = Invoices::find($transactions->invoice_id);
					$invoice_amount = $invoice->total_amount;
				} else {
					$invoice = ClientInvoices::find($transactions->invoice_id);
					$invoice_amount = $invoice->amount_receiveable;
				}
				
				$transaction_amount = $transactions->amount;
				$unallocated = $invoice_amount - $transaction_amount;

				return $unallocated;
			})
            ->addColumn('action', function ($transactions) use ($perm) {
                return $this->getButton($transactions->id, false, $transactions, $perm);
            })
            ->rawColumns(['select', 'action', 'link'])
            ->make(true);
    }
    public function serviceTransactionCreate($tourId, Request $request)
    {
        $transactions = ClientInvoices::find($tourId);
        $tour = Tour::find($transactions->tour_id);
        $offices = Offices::all();
        $tourName = $tour->name;
        $clients = Client::all();
        $hotels = Hotel::all();
        $events = Event::all();
        $buses = Bus::all();
        $restaurants = Restaurant::all();

        $options = array("Event", "Restaurant", "Bus");
        return view('accounting.service_transaction.create', compact('offices', 'tourName', 'clients', 'hotels', 'options', 'events', 'buses', 'restaurants'));
    }
    public function create(Request $request)
    {

        $tour = Tour::all()->first();
        $quotation = Quotation::where("tour_id", $tour->id)->where("is_confirm", "1")->first();

        $offices = Offices::all();
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

        $office = Offices::find($transactions->office_id);

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
        return view('accounting.index');
    }
    public function validateTransaction(Request $request)
    {
        $this->validate($request, [
            'office_id'  => 'required',
			'currency'  => 'required',
            'tour_id'     => 'required',
        ]);
    }
    public function edit($id, Request $request)
    {
        $transactions = ClientInvoices::find($id);
        $offices = Offices::all();
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
        return view('accounting.index');
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
        
        $office = Offices::find($transactions->office_id);
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
        $office = Offices::find($transactions->office_id);
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
        return Excel::create('Invoice' . $excelName, function ($excel) use ($tour, $office, $client, $transactions, $tourDates, $quotation, $calculations, $tourdays, $invoice_items) {
            $excel->sheet('Tour Information', function ($sheet) use ($tour, $office, $client, $transactions, $tourDates, $quotation, $calculations, $tourdays, $invoice_items) {

                $sheet->loadView('export.accounting.billingExcel', compact('tour', 'office', 'client', 'transactions', 'tourDates', 'quotation', 'calculations', 'tourdays', 'invoice_items'));
            });
        })->export($export);
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
        $offices = Offices::all();
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
