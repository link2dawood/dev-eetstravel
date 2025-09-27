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
        $this->middleware('preventBackHistory', ['except' => 'landingPage']);
        $this->middleware('auth', ['except' => 'landingPage']);
    }

    /**
     * get action buttons
     * @param  $id Invoices id
     * @return mixed
     */
    public function index()
    {
        // Get all invoices tours data (same as the AJAX data method)
        $invoices_tours = InvoicesTours::all();

        $permission_destroy = PermissionHelper::$relationsPermissionDestroy['App\Invoices'];
        $permission_edit = PermissionHelper::$relationsPermissionEdit['App\Invoices'];
        $permission_show = PermissionHelper::$relationsPermissionShow['App\Invoices'];

        $perm = [];
        $perm['show'] = Auth::user()->can($permission_show);
        $perm['edit'] = Auth::user()->can($permission_edit);
        $perm['destroy'] = Auth::user()->can($permission_destroy);
        $perm['clone'] = Auth::user()->can('accounting.create');

        // Add computed columns to each invoice tour
        $invoicesData = $invoices_tours->map(function ($invoices_tours) use ($perm) {
            $invoices = Invoices::find($invoices_tours->invoices_id);

            // Office Name
            $office_name = "";
            if (!empty($invoices)) {
                $office = Offices::find($invoices->office_id);
                $office_name = $office->office_name ?? "";
            }
            $invoices_tours->officeName = $office_name;

            // Invoice data
            if (!empty($invoices)) {
                $invoices_tours->invoice_no = $invoices->invoice_no;
                $invoices_tours->dueDate = $invoices->dueDate;
                $invoices_tours->receivedDate = $invoices->receivedDate;
                $invoices_tours->total_amount = $invoices->total_amount;
                $invoices_tours->extra_amount = $invoices->extra_amount;
            }

            // Tour name
            $tour = Tour::find($invoices_tours->invoices_tours_id);
            $invoices_tours->tour = $tour->name ?? "";

            // Package name
            $tour_package = TourPackage::find($invoices_tours->package_id);
            $package_name = "Extra Cost";
            if (!empty($tour_package)) {
                $package_name = $tour_package->name;
            }
            $invoices_tours->package = $package_name;

            // Status calculation
            if (!empty($invoices)) {
                $transaction = Transaction::where("invoice_id", $invoices->id)->where("pay_to", "Supplier");
                $sum_amount = $transaction->sum("amount");
                $amount = $invoices->total_amount;
                $remaining_amount = $amount - $sum_amount;

                if ($sum_amount == $amount) {
                    $result = "Paid";
                } elseif ($sum_amount == 0) {
                    $result = "You Owe " . $amount;
                } else {
                    $result = "You Owe " . $remaining_amount;
                }
                $invoices_tours->status = $result;
            }

            // Action buttons
            $invoices_tours->action_buttons = $this->getButton($invoices_tours->id, false, $invoices_tours, $perm);

            return $invoices_tours;
        });

        return view('invoices.index', compact('invoicesData'));
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
    public function clientTransactionData($invoiceId, Request $request)
    {
        //$invoices = Invoices::where("tour_id",$tourId)->get();
        $invoice_tours = InvoicesTours::where("invoices_id", $invoiceId)->get();

        $permission_destroy = PermissionHelper::$relationsPermissionDestroy['App\Invoices'];
        $permission_edit = PermissionHelper::$relationsPermissionEdit['App\Invoices'];
        $permission_show = PermissionHelper::$relationsPermissionShow['App\Invoices'];

        $perm = [];
        $perm['show'] = Auth::user()->can($permission_show);
        $perm['edit'] = Auth::user()->can($permission_edit);
        $perm['destroy'] = Auth::user()->can($permission_destroy);
        $perm['clone'] = Auth::user()->can('accounting.create');

        return Datatables::of($invoice_tours)->addColumn('action', function ($invoice_tours) use ($perm) {
            return $this->getShowButton($invoice_tours->id, false, $invoice_tours, $perm);
        })

            ->addColumn('tourName', function ($invoice_tours) {

                $tour = Tour::find($invoice_tours->invoices_tours_id);
                return $tour->name;
            })

            ->addColumn('packageName', function ($invoice_tours) {
                $package = TourPackage::find($invoice_tours->package_id);
                return $package->name;
            })

            ->addColumn('totalAmount', function ($invoice_tours) {
                $package = TourPackage::find($invoice_tours->package_id);
                return $package->total_amount;
            })
            ->rawColumns(['select', 'action', 'link'])
            ->make(true);
    }
    public function TourInvoiceData($tourid, Request $request)
    {
        //$invoices = Invoices::where("tour_id",$tourId)->get();
		 $invoice_tours = InvoicesTours::where("invoices_tours_id", $tourid)->get();
		if(!is_numeric($tourid)){
			
			$invoice_tours = array();
			$invoices = InvoicesTours::all();
			
			foreach($invoices as $invoice){
				$package_name = $invoice->package->name??"";
				if($package_name == $tourid){
					array_push($invoice_tours,$invoice);
				}
			}
			
		}
       
//dd($invoice_tours);
        $permission_destroy = PermissionHelper::$relationsPermissionDestroy['App\Invoices'];
        $permission_edit = PermissionHelper::$relationsPermissionEdit['App\Invoices'];
        $permission_show = PermissionHelper::$relationsPermissionShow['App\Invoices'];

        $perm = [];
        $perm['show'] = Auth::user()->can($permission_show);
        $perm['edit'] = Auth::user()->can($permission_edit);
        $perm['destroy'] = Auth::user()->can($permission_destroy);
        $perm['clone'] = Auth::user()->can('accounting.create');

        return Datatables::of($invoice_tours)->addColumn('action', function ($invoice_tours) use ($perm) {
            return $this->getShowButton($invoice_tours->id, false, $invoice_tours, $perm);
        })
            ->addColumn('officeName', function ($invoices_tours) {
				$office_name = "";
                $invoices = Invoices::find($invoices_tours->invoices_id);
				$office = Offices::find($invoices->office_id);
				if(!empty($office)){
					$office_name=	$office->office_name;
				}
				
                return $office_name;
            })
            ->addColumn('invoice_no', function ($invoices_tours) {
                $invoices = Invoices::find($invoices_tours->invoices_id);

                return $invoices->invoice_no;
            })
			 ->addColumn('dueDate', function ($invoices_tours) {
                $invoices = Invoices::find($invoices_tours->invoices_id);

                return $invoices->dueDate;
            })
            ->addColumn('receivedDate', function ($invoices_tours) {
                $invoices = Invoices::find($invoices_tours->invoices_id);

                return $invoices->receivedDate;
            })
            ->addColumn('total_amount', function ($invoices_tours) {
                $invoices = Invoices::find($invoices_tours->invoices_id);
                return $invoices->total_amount;
            })
            ->addColumn('extra_amount', function ($invoices_tours) {
                $invoices = Invoices::find($invoices_tours->invoices_id);

                return $invoices->extra_amount;
            })
            ->addColumn('amount_payable', function ($invoices_tours) {
                $invoices = Invoices::find($invoices_tours->invoices_id);
                return $invoices->amount_payable;
            })
			->addColumn('tour', function ($invoices_tours) {
                $tour = Tour::find($invoices_tours->invoices_tours_id);
                return $tour->name;
            })
            ->addColumn('package', function ($invoices) {
                $tour_package = TourPackage::find($invoices->package_id);
                $package_name = "";
				if(!empty($tour_package)){
					$package_name = $tour_package->name;
				}
                return $package_name;
            })
			->addColumn('status', function ($invoices) {
                $invoices = Invoices::find($invoices->invoices_id);
                $transaction = Transaction::where("invoice_id",$invoices->id)->where("pay_to","Supplier");;
               
                $sum_amount = $transaction->sum("amount");
                $amount = $invoices->total_amount ;
                $remaining_amount =  $amount - $sum_amount;
                if($sum_amount == $amount){
                    $result = "Paid";
                }
                elseif($sum_amount == 0){
                    $result = "You Owe ".$amount;
                }
                else{
                    $result = "You Owe ".$remaining_amount;
                }
               
                return $result;
            })
            ->rawColumns(['select', 'action', 'link'])
            ->make(true);
    }
    public function data(Request $request)
    {

        $invoices_tours = InvoicesTours::all();

        $permission_destroy = PermissionHelper::$relationsPermissionDestroy['App\Invoices'];
        $permission_edit = PermissionHelper::$relationsPermissionEdit['App\Invoices'];
        $permission_show = PermissionHelper::$relationsPermissionShow['App\Invoices'];

        $perm = [];
        $perm['show'] = Auth::user()->can($permission_show);
        $perm['edit'] = Auth::user()->can($permission_edit);
        $perm['destroy'] = Auth::user()->can($permission_destroy);
        $perm['clone'] = Auth::user()->can('accounting.create');

        return Datatables::of($invoices_tours)
            ->addColumn('officeName', function ($invoices_tours) {
                $invoices = Invoices::find($invoices_tours->invoices_id);
               $office_name="";
				if(!empty($invoices)){
					 $office = Offices::find($invoices->office_id);
					$office_name=	$office->office_name;
				}
				
                return $office_name;
            })
            ->addColumn('invoice_no', function ($invoices_tours) {
                $invoices = Invoices::find($invoices_tours->invoices_id);

                return $invoices->invoice_no;
            })
            ->addColumn('dueDate', function ($invoices_tours) {
                $invoices = Invoices::find($invoices_tours->invoices_id);

                return $invoices->dueDate;
            })
            ->addColumn('receivedDate', function ($invoices_tours) {
                $invoices = Invoices::find($invoices_tours->invoices_id);

                return $invoices->receivedDate;
            })
            ->addColumn('total_amount', function ($invoices_tours) {
                $invoices = Invoices::find($invoices_tours->invoices_id);
                return $invoices->total_amount;
            })
            ->addColumn('extra_amount', function ($invoices_tours) {
                $invoices = Invoices::find($invoices_tours->invoices_id);

                return $invoices->extra_amount;
            })
          
            ->addColumn('tour', function ($invoices_tours) {
                $tour = Tour::find($invoices_tours->invoices_tours_id);
                return $tour->name;
            })
            ->addColumn('package', function ($invoices) {
                $tour_package = TourPackage::find($invoices->package_id);
				$package_name = "Extra Cost";
				if(!empty($tour_package)){
					$package_name = $tour_package->name;
				}
                return $package_name;
            })
            ->addColumn('status', function ($invoices) {
                $invoices = Invoices::find($invoices->invoices_id);
                $transaction = Transaction::where("invoice_id",$invoices->id)->where("pay_to","Supplier");;
               
                $sum_amount = $transaction->sum("amount");
                $amount = $invoices->total_amount ;
                $remaining_amount =  $amount - $sum_amount;
                if($sum_amount == $amount){
                    $result = "Paid";
                }
                elseif($sum_amount == 0){
                    $result = "You Owe ".$amount;
                }
                else{
                    $result = "You Owe ".$remaining_amount;
                }
               
                return $result;
            })
            ->addColumn('action', function ($invoices) use ($perm) {
                return $this->getButton($invoices->id, false, $invoices, $perm);
            })
            ->rawColumns(['select', 'action', 'link'])
            ->make(true);
    }

    public function create(Request $request)
    {
        $offices = Offices::all();
        $tours = Tour::all();
        $clients = Client::all();
       
        return view('invoices.create', compact('offices', 'tours', 'clients'));
    }
    public function store(Request $request)
    {
      
        $this->validateInvoice($request);
		
		$currentDate = Carbon::now();

        // $request->invoice_no  = (string) \Illuminate\Support\Str::uuid();
        $payment_methods = $request->payment_method;
        $payment_date = $request->paymentdate;
        $paid_amount = $request->paid_amount;
        if(!empty($paid_amount)){
            $payment_amount = 0;
            foreach ($paid_amount as $paidamount) {
                $payment_amount += $paidamount;
            }
         
            if( $request->total_amount< $payment_amount){
                LaravelFlashSessionHelper::setFlashMessage("Payments Cannot be  Greather than total Amount", 'error');
                $data = ['route' => route('invoices.create')];
                return response()->json($data);
             
            }
        }
   
        $invoice = new Invoices();
        $invoice->office_id    = $request->office_id;
        $invoice->dueDate   = $currentDate->addWeek();
        $invoice->receivedDate   = $currentDate;
        $invoice->invoice_no   = $request->invoice_no;
        $invoice->total_amount   = $request->total_amount;
		$invoice->extra_amount   = $request->extra_amount;
		$invoice->note   = $request->note;
        $invoice->save();
		
        $this->addFile($request, $invoice);
		 
        foreach ($request->tour_id as $tour_id) {
            $test = "package_id" . $tour_id;
            $package_ids =  $request->$test;

			if(!is_null($package_ids)){
            foreach ($package_ids as $package_id) {
				/*
                if ($invoice->payment == "Final Payment") {
                    $tour_package = TourPackage::find($package_id);
                    $tour_package->paid = 1;
                    $tour_package->save();
                }*/
				InvoicesTours::create([
						
					"invoices_id"    => $invoice->id,					
					"invoices_tours_id"    => $tour_id,
					"package_id"   => $package_id,
					]);
                //$invoice->tours()->attach($tour_id, ['package_id' => $package_id]);
            }
			}else{
				InvoicesTours::create([
						
					"invoices_id"    => $invoice->id,					
					"invoices_tours_id"    => $tour_id,
					]);
			}
        }

       
        $i = 0;
		
		
        if(!empty($payment_methods)){
        foreach ($payment_methods as $payment_method) {
            // dd($payment_method == "null");
            
               // dd($payment_method);
			
                $transaction = Transaction::create([
                    "date" =>  $payment_date[$i],
                    "trans_no" => 'TXN-' . uniqid(),
                    "amount" => $paid_amount[$i],
                    "pay_to" => "Supplier",
                    "invoice_id" => $invoice->id,
                    "payment_method" => $payment_method,
                ]);
            
            $i += 1;
        }
        }
        LaravelFlashSessionHelper::setFlashMessage("Invoice $invoice->invoice_no created", 'success');
        $data = ['route' => route('invoices.index')];
        return response()->json($data);
        return view('invoices.index');
    }
    public function validateInvoice(Request $request)
    {
        $this->validate($request, [
            'office_id'     => 'required',
            'tour_id'     => 'required',
            'invoice_no'    =>  'required',
            'total_amount'    => 'required',

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
                dd("ok");
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
                dd("ok");
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
