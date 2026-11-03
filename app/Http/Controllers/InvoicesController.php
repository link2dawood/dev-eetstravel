<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Redirect;
use Illuminate\Http\Request;
use Image;
use App\Services\InvoicePayments;
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
use Illuminate\Validation\Rule;
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
    public function index(Request $request)
    {
        $permission_destroy = PermissionHelper::$relationsPermissionDestroy['App\Invoices'];
        $permission_edit = PermissionHelper::$relationsPermissionEdit['App\Invoices'];
        $permission_show = PermissionHelper::$relationsPermissionShow['App\Invoices'];

        $perm = [];
        $perm['show'] = Auth::user()->can($permission_show);
        $perm['edit'] = Auth::user()->can($permission_edit);
        $perm['destroy'] = Auth::user()->can($permission_destroy);
        $perm['clone'] = Auth::user()->can('accounting.create');

        $query = Invoices::query()->with(['activeTours.tours', 'activeTours.package', 'office'])
            ->withSum(['transactions as paid_amount' => function ($q) { $q->where('pay_to', 'Supplier'); }], 'amount');
        if ($request->filled('search')) {
            $search = trim($request->input('search'));
            $query->where(function ($q) use ($search) {
                $q->where('invoice_no', 'like', '%' . $search . '%')
                    ->orWhereHas('activeTours.tours', function ($t) use ($search) { $t->where('name', 'like', '%' . $search . '%'); })
                    ->orWhereHas('activeTours.package', function ($p) use ($search) { $p->where('name', 'like', '%' . $search . '%'); });
            });
        }
        $invoicesData = $query->orderByDesc('id')->paginate(25)->withQueryString();
        $invoicesData->getCollection()->transform(function ($invoice) {
            $invoice->tour = $invoice->activeTours->pluck('tours.name')->filter()->unique()->implode(', ');
            $invoice->package = $invoice->activeTours->map(function ($link) { return $link->package->name ?? 'Extra Cost'; })->unique()->implode(', ');
            $invoice->officeName = $invoice->office->office_name ?? '';
            $paid = (float) $invoice->paid_amount;
            $balance = (float) $invoice->total_amount + (float) $invoice->extra_amount - $paid;
            $invoice->status = $balance <= 0 ? 'Paid' : 'You Owe ' . number_format($balance, 2);
            return $invoice;
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

    public function create(Request $request)
    {
        $offices = Offices::all();
        $tours = Tour::all();
        $clients = Client::all();
        $tour_id = $request->input('tour_id', null);
        $received_date = Carbon::today()->toDateString();
        $due_date = Carbon::today()->addWeek()->toDateString();
       
        return view('invoices.create', compact('offices', 'tours', 'clients', 'tour_id', 'received_date', 'due_date'));
    }
    public function store(Request $request)
    {
        $this->validateInvoice($request);

        $rows = InvoicePayments::rows($request);
        InvoicePayments::ensureWithinBalance($rows, $request->total_amount, 0, $request->input('extra_amount', 0));
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

            if ((float) $request->total_amount + (float) $request->input('extra_amount', 0) < $payment_amount) {
                LaravelFlashSessionHelper::setFlashMessage("Payments Cannot be Greater than Total Amount", 'error');
                return redirect()->back()->withInput();
            }
        }

        try {
            DB::beginTransaction();
            Offices::whereKey($request->office_id)->lockForUpdate()->firstOrFail();
            $this->ensureInvoiceNumberAvailable($request->invoice_no, (int) $request->office_id);
            // Create Invoice
            $invoice = new Invoices();
            $invoice->office_id = $request->office_id;
            $invoice->dueDate = $request->input('due_date', $currentDate->copy()->addWeek()->toDateString());
            $invoice->receivedDate = $request->input('received_date', $currentDate->toDateString());
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
                            "package_id" => 0,
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

            DB::commit();
            // Success response - redirect to the related tour view if available, otherwise to invoices index
            LaravelFlashSessionHelper::setFlashMessage("Invoice $invoice->invoice_no created successfully", 'success');

            if ($attachedTourId) {
                return redirect()->route('tour.show', ['tour' => $attachedTourId]);
            }

            return redirect()->route('invoices.index');

        } catch (\Exception $e) {
            DB::rollBack();
            report($e);
            // Error handling
            LaravelFlashSessionHelper::setFlashMessage("The invoice could not be saved. Please check the values and retry.", 'error');
            return redirect()->back()->withInput();
        }
    }
    public function validateInvoice(Request $request)
    {
        $this->validate($request, [
            'office_id' => 'required|integer|exists:office_fees,id',
            'tour_id'     => 'required|array|min:1',
            'tour_id.*'   => 'required|integer|distinct|exists:tours,id',
            'invoice_no' => ['required', 'string', 'max:191', Rule::unique('supplier_invoices', 'invoice_no')->where('office_id', $request->input('office_id'))],
            'total_amount' => 'required|numeric|min:0|max:999999999.99',
            'extra_amount' => 'nullable|numeric|min:0|max:999999999.99',
            'received_date' => 'required|date_format:Y-m-d',
            'due_date' => 'required|date_format:Y-m-d|after_or_equal:received_date',
            'note' => 'nullable|string|max:400',
        ]);
        foreach ($request->input('tour_id', []) as $tourId) {
            $key = 'package_id' . $tourId;
            $packageIds = $request->input($key, []);
            $request->validate([$key => 'nullable|array', $key . '.*' => ['required', 'integer', 'distinct', Rule::exists('tour_packages', 'id')->whereNull('deleted_at')]]);
            if (TourPackage::whereIn('id', $packageIds)->where('tour_id', '!=', $tourId)->exists()) {
                throw \Illuminate\Validation\ValidationException::withMessages([$key => 'Each service must belong to its selected tour.']);
            }
        }
    }

    private function ensureInvoiceNumberAvailable(string $invoiceNumber, int $officeId, ?int $ignoreId = null): void
    {
        $query = Invoices::where('office_id', $officeId)->where('invoice_no', $invoiceNumber);
        if ($ignoreId !== null) $query->where('id', '!=', $ignoreId);
        if ($query->exists()) {
            throw \Illuminate\Validation\ValidationException::withMessages(['invoice_no' => 'This invoice number is already in use for the selected office.']);
        }
    }
    public function edit($id, Request $request)
    {
        $invoices = Invoices::with(['activeTours.tours', 'activeTours.package'])->findOrFail($id);
        $offices = Offices::all();
        $tours = Tour::all();
        $clients = Client::all();
		

        return view('invoices.edit', compact('offices','tours', 'clients', 'invoices'));
    }
    public function update($id, Request $request)
    {
        $currentInvoice = Invoices::findOrFail($id);
        $invoiceNumberRules = ['required', 'string', 'max:191'];
        if ($request->input('invoice_no') !== $currentInvoice->invoice_no) {
            $invoiceNumberRules[] = Rule::unique('supplier_invoices', 'invoice_no')->where('office_id', $request->input('office_id'));
        }
        $request->validate([
            'office_id' => 'required|integer|exists:office_fees,id',
            'invoice_no' => $invoiceNumberRules,
            'total_amount' => 'required|numeric|min:0|max:999999999.99',
            'extra_amount' => 'nullable|numeric|min:0|max:999999999.99',
            'received_date' => 'required|date_format:Y-m-d',
            'due_date' => 'required|date_format:Y-m-d|after_or_equal:received_date',
        ]);
        DB::transaction(function () use ($id, $request) {
            $invoice = Invoices::whereKey($id)->lockForUpdate()->firstOrFail();
            Offices::whereKey($request->office_id)->lockForUpdate()->firstOrFail();
            if ($invoice->invoice_no !== $request->invoice_no || (int) $invoice->office_id !== (int) $request->office_id) {
                $this->ensureInvoiceNumberAvailable($request->invoice_no, (int) $request->office_id, $invoice->id);
            }
            $paid = Transaction::where('invoice_id', $invoice->id)->where('pay_to', 'Supplier')->sum('amount');
            InvoicePayments::ensureWithinBalance([], $request->total_amount, $paid, $request->input('extra_amount', 0));
            $invoice->update(['office_id' => $request->office_id, 'invoice_no' => $request->invoice_no,
                'total_amount' => $request->total_amount, 'extra_amount' => $request->input('extra_amount', 0),
                'receivedDate' => $request->received_date, 'dueDate' => $request->due_date]);
        });
        return redirect()->route('invoices.show', $id);
    }
    public function show($id, Request $request)
    {

        $title = 'Show - Payments';

        if ($request->ajax()) {
            return URL::to('invoices/' . $id);
        }

        $invoices = Invoices::with(['activeTours.tours', 'activeTours.package', 'transactions' => function ($q) { $q->where('pay_to', 'Supplier'); }])->findOrFail($id);
        $invoice_tour = $invoices->activeTours->first();
        $office = $invoices->office;

        $files = $this->parseAttach($invoices);

        return view('invoices.show', compact('invoices', 'title', 'office', 'files','invoice_tour'));
    }



    public function DeleteMsg($id, Request $request)
    {

        $msg = Ajaxis::BtDeleting('Warning!!', 'Archive this supplier invoice and its tour links? Financial history will be retained.', '/invoices/' . $id . '/delete');
        if ($request->ajax()) {
            return $msg;
        }
    }
    public function destroy($id)
    {
        DB::transaction(function () use ($id) {
            $invoice = Invoices::whereKey($id)->lockForUpdate()->firstOrFail();
            if (Transaction::where('invoice_id', $invoice->id)->where('pay_to', 'Supplier')->exists()) throw \Illuminate\Validation\ValidationException::withMessages(['invoice' => 'An invoice with payments cannot be deleted.']);
            InvoicesTours::where('invoices_id', $invoice->id)->delete();
            $invoice->deleted_by = Auth::id();
            $invoice->save();
            $invoice->delete();
        });
        return request()->ajax() ? URL::to('invoices') : redirect()->route('invoices.index');
    }
	
	public function supplierdropdown($id, Request $request)
    {
        abort_unless(Auth::user() && Auth::user()->can('invoices.create'), 403);
        $tour = Tour::findOrFail($id);

		$multiple = "";
		$att_name = "";
		
		if($request->multiple == 1){
			
			$multiple = 'multiple="multiple"';
			$att_name =  $tour->id. '[]' ;
		}
        $tourDates = $this->prepareTourPackages($tour, $request)['tourDates'];
	
        $package_name = '<label for="service" id="lable-service' . (int) $tour->id . '">' . e($tour->name) . '</label>
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
                        $package_name .= "<option value=\"" . (int) $package->id . "\">" . e($currentDate . ' : ' . $package->name) . "</option>";


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
		$invoices = Invoices::findOrFail($id);
		
        $offices = Offices::all();
        $tours = Tour::all();
        $clients = Client::all();
		

        return view('invoices.payment_create', compact('offices','tours', 'clients', 'invoices'));
    }
    public function payment_store($id, Request $request)
    {
        $rows = InvoicePayments::rows($request, true);
        DB::transaction(function () use ($id, $rows) {
            $invoice = Invoices::whereKey($id)->lockForUpdate()->firstOrFail();
            $paid = Transaction::where('invoice_id', $id)->where('pay_to', 'Supplier')->sum('amount');
            InvoicePayments::ensureWithinBalance($rows, $invoice->total_amount, $paid, $invoice->extra_amount);
            InvoicePayments::save($rows, $id, 'Supplier');
        });
        return redirect()->route('invoices.show', $id);
    }
}
