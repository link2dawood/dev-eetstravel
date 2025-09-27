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
use App\Offices;
use App\Client;
use App\Hotel;
use App\Event;
use App\Restaurant;
use App\Guide;
use App\Bus;
use App\Office_Tours;
use App\Office_Utility_Expenses;
use App\Office_Earnings;
use App\Office_Employes_Salary;
use App\Office_Balance;
use App\Invoices;
use App\ClientInvoices;
use Yajra\Datatables\Datatables;
use App\Helper\ExportTrait;
use Carbon\Carbon;
use App\Helper\HelperTrait;
use App\Helper\LaravelFlashSessionHelper;
use App\Helper\PermissionHelper;
use Auth;
use View;
use URL;
use Amranidev\Ajaxis\Ajaxis;

class OfficeController extends Controller {

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

     public function __construct()
    {
        $this->middleware('permissions.required');
        $this->middleware('preventBackHistory', ['except' => 'landingPage']);
        $this->middleware('auth', ['except' => 'landingPage']);
    }

    /**
     * get action buttons
     * @param  $id offices id
     * @return mixed
     */
    public function index() {

        // Get all offices data (same as the AJAX data method)
        $offices = Offices::distinct()->get();
        $permission_destroy = PermissionHelper::$relationsPermissionDestroy['App\Offices'];
        $permission_edit = PermissionHelper::$relationsPermissionEdit['App\Offices'];
        $permission_show = PermissionHelper::$relationsPermissionShow['App\Offices'];

        $perm = [];
        $perm['show'] = Auth::user()->can($permission_show);
        $perm['edit'] = Auth::user()->can($permission_edit);
        $perm['destroy'] = Auth::user()->can($permission_destroy);
        $perm['clone'] = Auth::user()->can('office.create');

        // Add action buttons to each office
        $officesData = $offices->map(function ($office) use ($perm) {
            $office->action_buttons = $this->getButton($office->id, false, $office, $perm);
            return $office;
        });

        return view('office.index',compact('offices', 'officesData'));
    }
    public function getButton($id, $isQuotation = false, $tour, array $perm)
    {
        $url = array('show'       => route('office.show', ['office' => $id]),
                     'edit'       => route('office.edit', ['office' => $id]),
                     'delete_msg' => "/office/{$id}/deleteMsg",
                     'id'         => $id);

        return DatatablesHelperController::getActionButtonTours($url, $isQuotation, $perm);
//        return DatatablesHelperController::getActionButton($url, $isQuotation, $tour);
    }

    public function data(Request $request)
    {
         $offices = Offices::distinct()->get();
         $permission_destroy = PermissionHelper::$relationsPermissionDestroy['App\Offices'];
         $permission_edit = PermissionHelper::$relationsPermissionEdit['App\Offices'];
         $permission_show = PermissionHelper::$relationsPermissionShow['App\Offices'];
         
         $perm = [];        
         $perm['show'] = Auth::user()->can($permission_show);        
         $perm['edit'] = Auth::user()->can($permission_edit);
         $perm['destroy'] = Auth::user()->can($permission_destroy);
         $perm['clone'] = Auth::user()->can('office.create');
         
        return Datatables::of($offices)->addColumn('action', function ($offices) use($perm) {
                return $this->getButton($offices->id, false, $offices, $perm);
            })
            ->rawColumns(['select', 'action', 'link'])
            ->make(true);
    }

    public function create(Request $request)
    {
        $tours = Tour::all();
        $clients = Client::all();
        $hotels = Hotel::all();
        $events = Event::all();
        $buses = Bus::all();
        $restaurants = Restaurant::all();

        $options = array("Event","Restaurant");
        return view('office.create', compact('tours','clients','hotels','options','events','buses','restaurants'));
    }
    public function store(Request $request){
        offices::create($request->except(["attach"]));
        return view('office.index');
    }
    public function edit($id, Request $request){
		
		$offices = Offices::find($id);
        $options = array("Event","Restaurant");
		return view('office.edit', compact('offices'));
	}
	public function update($id,Request $request){
        $offices = Offices::find($id);
        $offices->update($request->except(["attach"]));
		return redirect(route('office.index'));
	}
    public function show($id, Request $request)
    {
  
        $title = 'Show - Offices';

        if ($request->ajax()) {
            return URL::to('office/' . $id);
        }

        $offices =Offices::find($id);
        
        if($offices == null){
            return abort(404);
        }
		$total_tour_expense = 0 ;
		$total_utility_expense = 0 ;
		$total_employee_salary = 0 ;
		$total_office_earning = 0 ;
		$total_balance = 0 ;
		
       $tour_expenses = Office_Tours::where("office_id", $offices->id)->get() ;
		foreach($tour_expenses as $tour_expense){
			$total_tour_expense = $total_tour_expense + $tour_expense->tour_expenses;
		}
		
		$utility_expenses = Office_Utility_Expenses::where("office_id", $offices->id)->get() ;
		foreach($utility_expenses as $utility_expense){
			$total_utility_expense = $total_utility_expense + $utility_expense->monthly_expense;
		}
		
		$employee_salaries = Office_Employes_Salary::where("office_id", $offices->id)->get() ;
		foreach($employee_salaries as $employee_salary){
			$total_employee_salary = $total_employee_salary + $employee_salary->employe_salary;
		}
		
		$office_earnings = Office_Earnings::where("office_id", $offices->id)->get() ;
		foreach($office_earnings as $office_earning){
			$total_office_earning = $total_office_earning + $office_earning->profit;
		}
		
		$balances = Office_Balance::where("office_id", $offices->id)->get() ;
		foreach($balances as $balance){
			$total_balance = $total_balance + $balance->	total_amount;
		}
		
        return view('office.show', compact('offices','title','total_tour_expense','total_utility_expense','total_employee_salary','total_office_earning','total_balance'));
    }
	
	public function DeleteMsg($id, Request $request)
    {
        $msg = Ajaxis::BtDeleting('Warning!!', 'Would you like to remove This?', '/office/' . $id . '/delete');

        if ($request->ajax()) {
            return $msg;
        }
    }
	public function destroy($id)
    {

        $offices = Offices::find($id);
		$invoice = Invoices::where('office_id',$id)->get();
		$office_invoice = ClientInvoices::where('office_id',$id)->get();
		if(count($office_invoice) != '0'){
			LaravelFlashSessionHelper::setFlashMessage("Office $offices->office_name can not be delete because it is used in Client Invoice", 'error');
			return URL::to('office');
		}
		if(count($invoice) != '0'){
			LaravelFlashSessionHelper::setFlashMessage("Office $offices->office_name can not be delete because it is used in Supplier Invoice", 'error');
			return URL::to('office');
		}
		if($offices->status == '1'){
			LaravelFlashSessionHelper::setFlashMessage("Office $offices->office_name can not be delete because it is used in tour", 'error');
			return URL::to('office');
		}

		
        $offices->find($id)->delete();
      LaravelFlashSessionHelper::setFlashMessage("Office $offices->office_name deleted", 'success');

        return URL::to('office');
    }
    
    
}
