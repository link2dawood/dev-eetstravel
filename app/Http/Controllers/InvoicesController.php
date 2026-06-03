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
Use App\TourDay;
use App\Helper\TourPackage\TourService;
use App\Invoices;
use App\InvoicesTours;
use App\Client;
use App\Hotel;
use App\Event;
use App\Restaurant;
use App\Guide;
use App\Bus;
use App\Offices;
use App\TourPackage;
use App\Transaction;
use App\Notification;
use Yajra\Datatables\Datatables;
use App\Helper\ExportTrait;
use Carbon\Carbon;
use App\Helper\HelperTrait;
use App\Helper\LaravelFlashSessionHelper;
use App\Helper\PermissionHelper;
use Auth;
use View;
use Amranidev\Ajaxis\Ajaxis;
use URL;
use App\Helper\FileTrait;

class  InvoicesController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    use FileTrait;
    public function __construct()
    {
        $this->middleware('permissions.required');
        $this->middleware('preventBackHistory');
        $this->middleware('auth');
    }

    /**
     * get action buttons
     * @param  $id Invoices id
     * @return mixed
     */
    /**
     * AUDIT.md CC11 — was InvoicesTours::all() (load full pivot table)
     * followed by Invoices::find / Offices::find / Tour::find / TourPackage::find
     * / Transaction::where inside the row map — five extra queries per row,
     * across the entire table.
     *
     * Now: paginate(20) so we only process one page worth of rows, and bulk-
     * load every related model with whereIn so the cost is constant per page
     * (5 queries total instead of 5N).
     */
    public function index()
    {
        $perPage = 20;

        // Page-bounded slice of the pivot table.
        $page = InvoicesTours::orderByDesc('id')->paginate($perPage);

        // Bulk-fetch every related model the row map needs.
        $invoiceIds  = $page->getCollection()->pluck('invoices_id')->filter()->unique();
        $tourIds     = $page->getCollection()->pluck('invoices_tours_id')->filter()->unique();
        $packageIds  = $page->getCollection()->pluck('package_id')->filter()->unique();

        $invoices    = Invoices::whereIn('id', $invoiceIds)->get()->keyBy('id');
        $officeIds   = $invoices->pluck('office_id')->filter()->unique();
        $offices     = Offices::whereIn('id', $officeIds)->get()->keyBy('id');
        $tours       = Tour::whereIn('id', $tourIds)->get(['id', 'name'])->keyBy('id');
        $packages    = TourPackage::whereIn('id', $packageIds)->get(['id', 'name'])->keyBy('id');

        // Sum supplier payments grouped per invoice — one SUM-aggregate query,
        // not one per invoice.
        $paidByInvoice = Transaction::whereIn('invoice_id', $invoiceIds)
            ->where('pay_to', 'Supplier')
            ->selectRaw('invoice_id, SUM(amount) AS paid')
            ->groupBy('invoice_id')
            ->pluck('paid', 'invoice_id');

        // Decorate each row with the computed columns the view expects.
        $page->getCollection()->transform(function ($row) use ($invoices, $offices, $tours, $packages, $paidByInvoice) {
            $invoice            = $invoices->get($row->invoices_id);
            $office             = $invoice ? $offices->get($invoice->office_id) : null;
            $row->officeName    = $office->office_name ?? '';

            if ($invoice) {
                $row->invoice_no   = $invoice->invoice_no;
                $row->dueDate      = $invoice->dueDate;
                $row->receivedDate = $invoice->receivedDate;
                $row->total_amount = $invoice->total_amount;
                $row->extra_amount = $invoice->extra_amount;

                $paid      = (float) ($paidByInvoice[$invoice->id] ?? 0);
                $total     = (float) $invoice->total_amount;
                $remaining = $total - $paid;
                if (abs($paid - $total) < 0.005) {
                    $row->status = 'Paid';
                } elseif ($paid == 0) {
                    $row->status = 'You Owe ' . $total;
                } else {
                    $row->status = 'You Owe ' . $remaining;
                }
            }

            $row->tour    = optional($tours->get($row->invoices_tours_id))->name ?? '';
            $row->package = optional($packages->get($row->package_id))->name ?? 'Extra Cost';

            return $row;
        });

        return view('invoices.index', ['invoicesData' => $page]);
    }
    public function getButton($id, $isQuotation = false, $tour, array $perm)
    {
        $url = array(
            'show'       => route('invoices.show', ['invoice' => $id]),
            'edit'       => route('invoices.edit', ['invoice' => $id]),
            'delete_msg' => "/invoices/{$id}/deleteMsg",
            'id'         => $id
        );

        return DatatablesHelperController::getActionButtonTours($url, $isQuotation, $perm);
        //        return DatatablesHelperController::getActionButton($url, $isQuotation, $tour);
    }
    public function getShowButton($id, $isQuotation = false, $tour, array $perm)
    {
        $url = array(
            'show'       => route('invoices.show', ['invoice' => $id]),
            'edit'       => route('invoices.edit', ['invoice' => $id]),
            'delete_msg' => "/invoices/{$id}/deleteMsg",
            'id'         => $id
        );

        return DatatablesHelperController::getShowButton($url) . '<a class="delete btn btn-danger btn-sm" style="margin-right: 5px" data-toggle="modal" data-target="#myModal" data-link="' . $url["delete_msg"] . '"><i class="fa fa-trash-o"></i></a>';
        //        return DatatablesHelperController::getActionButton($url, $isQuotation, $tour);
    }

    public function create(Request $request)
    {
        $offices = Offices::all();
        $tours = Tour::all();
        $clients = Client::all();
        $tour_id = $request->input('tour_id', null);
       
        return view('invoices.create', compact('offices', 'tours', 'clients', 'tour_id'));
    }
    public function store(Request $request)
    {
        $this->validateInvoice($request);

        $currentDate = Carbon::now();

        // Validate payment amounts
        $payment_methods = $request->payment_method;
        $payment_date = $request->paymentdate;
        $paid_amount = $request->paid_amount;

        if (!empty($paid_amount)) {
            $payment_amount = 0;
            foreach ($paid_amount as $paidamount) {
                $payment_amount += $paidamount;
            }

            if ($request->total_amount < $payment_amount) {
                LaravelFlashSessionHelper::setFlashMessage("Payments Cannot be Greater than Total Amount", 'error');
                return redirect()->back()->withInput();
            }
        }

        try {
            // Create Invoice
            $invoice = new Invoices();
            $invoice->office_id = $request->office_id;
            $invoice->dueDate = $currentDate->copy()->addWeek();
            $invoice->receivedDate = $currentDate;
            $invoice->invoice_no = $request->invoice_no;
            $invoice->total_amount = $request->total_amount;
            $invoice->extra_amount = $request->extra_amount;
            $invoice->note = $request->note;
            $invoice->save();

            // Add file attachments
            $this->addFile($request, $invoice);

            // Add tours and packages to invoice
            $attachedTourId = null;
            if (is_array($request->tour_id) && count($request->tour_id) > 0) {
                foreach ($request->tour_id as $tour_id) {
                    $test = "package_id" . $tour_id;
                    $package_ids = $request->$test;

                    if (!is_null($package_ids)) {
                        foreach ($package_ids as $package_id) {
                            InvoicesTours::create([
                                "invoices_id" => $invoice->id,
                                "invoices_tours_id" => $tour_id,
                                "package_id" => $package_id,
                            ]);
                        }
                    } else {
                        InvoicesTours::create([
                            "invoices_id" => $invoice->id,
                            "invoices_tours_id" => $tour_id,
                        ]);
                    }

                    // remember first attached tour id for redirect
                    if ($attachedTourId === null) {
                        $attachedTourId = $tour_id;
                    }
                }
            }

            // Add payment transactions
            $i = 0;
            if (!empty($payment_methods)) {
                foreach ($payment_methods as $payment_method) {
                    Transaction::create([
                        "date" => $payment_date[$i],
                        "trans_no" => 'TXN-' . uniqid(),
                        "amount" => $paid_amount[$i],
                        "pay_to" => "Supplier",
                        "invoice_id" => $invoice->id,
                        "payment_method" => $payment_method,
                    ]);

                    $i += 1;
                }
            }

            // Success response - redirect to the related tour view if available, otherwise to invoices index
            LaravelFlashSessionHelper::setFlashMessage("Invoice $invoice->invoice_no created successfully", 'success');

            if ($attachedTourId) {
                return redirect()->route('tour.show', ['tour' => $attachedTourId]);
            }

            return redirect()->route('invoices.index');

        } catch (\Exception $e) {
            // Error handling
            LaravelFlashSessionHelper::setFlashMessage("Error creating invoice: " . $e->getMessage(), 'error');
            return redirect()->back()->withInput();
        }
    }
    public function validateInvoice(Request $request)
    {
        $this->validate($request, [
            'office_id'     => 'required',
            'tour_id'     => 'required|array|min:1',
            'tour_id.*'   => 'required|exists:tours,id',
            'invoice_no'    =>  'required',
            'total_amount'    => 'required|numeric|min:0',

        ]);
    }
    public function edit($id, Request $request)
    {
        $invoices = InvoicesTours::find($id);
		//$invoices = Invoices::find($id);
		
        $offices = Offices::all();
        $tours = Tour::all();
        $clients = Client::all();
		

        return view('invoices.edit', compact('offices','tours', 'clients', 'invoices'));
    }
    public function update($id, Request $request)
    {
		
		$invoices_tours = InvoicesTours::find($id);
		$invoices_tours->update([
		"invoices_tours_id"    => $request->tours,
        "package_id"   => $request->package_id,
		]);
		$invoice = Invoices::find($invoices_tours->invoices_id);
        $invoice->update([
		"office_id"    => $request->office_id,
        "invoice_no"   => $request->invoice_no,
        "total_amount"   =>$request->total_amount,
		]);
		Transaction::where('invoice_id', $invoices_tours->invoices_id)->where("pay_to", "Supplier")->delete();
		
		$payment_methods = $request->payment_method;
        $payment_date = $request->paymentdate;
        $paid_amount = $request->paid_amount;
		
		        $i = 0;
        if(!empty($payment_methods)){
        foreach ($payment_methods as $payment_method) {
            // dd($payment_method == "null");
            if ($payment_method == "null" ||  $payment_date[$i] == "null" ||   $paid_amount[$i] == "null"  ) {
                // No payment row for this index — skip silently.
                $i += 1;
                continue;
            } else {
             
                $transaction = Transaction::create([
                    "date" =>  $payment_date[$i],
                    "trans_no" => 'TXN-' . uniqid(),
                    "amount" => $paid_amount[$i],
                    "pay_to" => "Supplier",
                    "invoice_id" => $invoice->id,
                    "payment_method" => $payment_method,
                ]);
				
            }
            $i += 1;
        }
        }

        
        return view('invoices.index');
    }
    public function show($id, Request $request)
    {

        $title = 'Show - Payments';

        if ($request->ajax()) {
            return URL::to('$invoices/' . $id);
        }

        $invoice_tour= InvoicesTours::find($id);
		 $invoices = Invoices::find($invoice_tour->invoices_id);
        $office = Offices::find($invoices->office_id);



        if ($invoices == null) {
            return abort(404);
        }

        $files = $this->parseAttach($invoices);

        return view('invoices.show', compact('invoices', 'title', 'office', 'files','invoice_tour'));
    }



    public function DeleteMsg($id, Request $request)
    {

        $msg = Ajaxis::BtDeleting('Warning!!', 'Would you like to remove This?', '/invoices/' . $id . '/delete');
        if ($request->ajax()) {
            return $msg;
        }
    }
    public function destroy($id)
    {
        $invoices_tours = InvoicesTours::find($id)->delete();

        //$invoice_tours->find($id)->delete();
        // $invoices = Invoices::find($invoices_tours->invoices_id)->delete();
        //$invoices->delete();
        //LaravelFlashSessionHelper::setFlashMessage("Invoice  deleted", 'success');
        //return redirect()->back();
        return URL::to('invoices');
    }
	
	public function supplierdropdown($id, Request $request)
    {
        $tour = Tour::find($id);

		$multiple = "";
		$att_name = "";
		
		if($request->multiple == 1){
			
			$multiple = 'multiple="multiple"';
			$att_name =  $tour->id. '[]' ;
		}
        $tourDates = $this->prepareTourPackages($tour, $request)['tourDates'];
	
        $package_name = '<lable for= "service" id = "lable-service' . $tour->id . '">' . $tour->name . '</label>
    <select id="service' . $tour->id . '"  name="package_id' . $att_name . '" class="form-control tour_select"'.$multiple.'required>
	<option id="only_extra_cost">Extra Cost</option>';
        $n = 0;
        $previousDate = null;
        $count = 0;
        foreach ($tourDates as $tourDate) {
            //  dd(is_empty(count($tourDate->packages)));
            if (count($tourDate->packages) != 0) {
                # code...
                foreach ($tourDate->packages as $package) {
                    if ($package->name !== null) {
                       $currentDate = date('d-M-Y', strtotime($package->time_from));
                        /*
                foreach ($package->room_types_hotel as $item){
                    $listRoomsHotel[] = $item;
                }*/
                        
                                $n++;
                                $package_name .= '<option disabled>Day ' . $n . '</option>';
                            
                        

                        $listIdServices[] = ['id_service' => $package->reference, 'type_service' => $package->type];
                        $package_name .= "</br><option value = " . $package->id . ">" . $currentDate
                            . " : " . $package->name . "</option>";


                    }
                }
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
            
        }
       

        return ['tourDates' => $tourDates, 'tour' => $tour, 'last' => $last];
    }
	
	public function add_payment(Request $request,$id)
    {
        //$invoices = InvoicesTours::find($id);
		$invoices = Invoices::find($id);
		
        $offices = Offices::all();
        $tours = Tour::all();
        $clients = Client::all();
		

        return view('invoices.payment_create', compact('offices','tours', 'clients', 'invoices'));
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
                // No payment row for this index — skip silently.
                $i += 1;
                continue;
            } else {
            
                $transaction = Transaction::create([
                    "date" =>  $payment_date[$i],
                    "trans_no" => 'TXN-' . uniqid(),
                    "amount" => $paid_amount[$i],
                    "pay_to" => "Supplier",
                    "invoice_id" => $id,
                    "payment_method" => $payment_method,
                ]);
				
            }
            $i += 1;
        }
        }
		
		 $invoice_tour= Invoices::find($id);
		$invoice = $invoice_tour->tours()->first();
		LaravelFlashSessionHelper::setFlashMessage("payment $transaction->trans_no created", 'success');
        return redirect()->route('invoices.show', $invoice->id);
    }
}
