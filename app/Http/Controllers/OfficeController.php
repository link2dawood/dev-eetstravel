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

        // Action buttons will be generated in the view using the action_buttons component
        $officesData = $offices;

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
    public function store(Request $request)
    {
        Offices::create(array_map(fn ($value) => $value ?? '', $this->validateOffice($request)) + array_fill_keys(['bank_name','account_no','swift_code','tel','fax'], ''));
        return redirect()->route('office.index');
    }

    private function validateOffice(Request $request)
    {
        return $request->validate([
            'office_name' => 'required|string|max:191',
            'office_address' => 'required|string|max:250',
            'bank_name' => 'nullable|string|max:191',
            'account_no' => 'nullable|string|max:191',
            'swift_code' => 'nullable|string|max:191',
            'tel' => 'nullable|string|max:191',
            'fax' => 'nullable|string|max:191',
        ]);
    }
    public function edit($id, Request $request){
		
		$offices = Offices::findOrFail($id);
        $options = array("Event","Restaurant");
		return view('office.edit', compact('offices'));
	}
    public function update($id, Request $request)
    {
        Offices::findOrFail($id)->update(array_map(fn ($value) => $value ?? '', $this->validateOffice($request)));
        return redirect()->route('office.index');
    }
    public function show($id, Request $request)
    {

        $title = 'Show - Offices';

        if ($request->ajax()) {
            return URL::to('office/' . $id);
        }

        $offices =Offices::findOrFail($id);

        if($offices == null){
            return abort(404);
        }
		$total_tour_expense = 0 ;
		$total_utility_expense = 0 ;
		$total_employee_salary = 0 ;
		$total_office_earning = 0 ;
		$total_balance = 0 ;

		// Get permissions for action buttons
		$permission_destroy = PermissionHelper::$relationsPermissionDestroy['App\Offices'];
		$permission_edit = PermissionHelper::$relationsPermissionEdit['App\Offices'];
		$permission_show = PermissionHelper::$relationsPermissionShow['App\Offices'];

		$perm = [];
		$perm['show'] = Auth::user()->can($permission_show);
		$perm['edit'] = Auth::user()->can($permission_edit);
		$perm['destroy'] = Auth::user()->can($permission_destroy);
		$perm['clone'] = Auth::user()->can('office.create');

       $tour_expenses = Office_Tours::where("office_id", $offices->id)->get() ;
		foreach($tour_expenses as $tour_expense){
			$total_tour_expense = $total_tour_expense + $tour_expense->tour_expenses;
			// Add action buttons
			$url = ['edit' => route('tour_expenses.edit', $tour_expense->id), 'delete_msg' => "/tour_expenses/{$tour_expense->id}/deleteMsg"];
			$tour_expense->action_buttons = DatatablesHelperController::getEditButton($url, false, $perm) . "<a class='delete btn btn-danger btn-sm' href=\"{$url['delete_msg']}\" data-link=\"{$url['delete_msg']}\"><i class='fa fa-trash-o'></i></a>";
		}

		$utility_expenses = Office_Utility_Expenses::where("office_id", $offices->id)->get() ;
		foreach($utility_expenses as $utility_expense){
			$total_utility_expense = $total_utility_expense + $utility_expense->monthly_expense;
			// Add action buttons
			$url = ['edit' => route('utility_expenses.edit', $utility_expense->id), 'delete_msg' => "/utility_expenses/{$utility_expense->id}/deleteMsg"];
			$utility_expense->action_buttons = DatatablesHelperController::getEditButton($url, false, $perm) . "<a class='delete btn btn-danger btn-sm' href=\"{$url['delete_msg']}\" data-link=\"{$url['delete_msg']}\"><i class='fa fa-trash-o'></i></a>";
		}

		$employee_salaries = Office_Employes_Salary::where("office_id", $offices->id)->get() ;
		foreach($employee_salaries as $employee_salary){
			$total_employee_salary = $total_employee_salary + $employee_salary->employe_salary;
			// Add action buttons
			$url = ['edit' => route('employes-salary.edit', $employee_salary->id), 'delete_msg' => "/employes-salary/{$employee_salary->id}/deleteMsg"];
			$employee_salary->action_buttons = DatatablesHelperController::getEditButton($url, false, $perm) . "<a class='delete btn btn-danger btn-sm' href=\"{$url['delete_msg']}\" data-link=\"{$url['delete_msg']}\"><i class='fa fa-trash-o'></i></a>";
		}

		$office_earnings = Office_Earnings::where("office_id", $offices->id)->get() ;
		foreach($office_earnings as $office_earning){
			$total_office_earning = $total_office_earning + $office_earning->profit;
			// Add action buttons
			$url = ['edit' => route('office_earning.edit', $office_earning->id), 'delete_msg' => "/office_earning/{$office_earning->id}/deleteMsg"];
			$office_earning->action_buttons = DatatablesHelperController::getEditButton($url, false, $perm) . "<a class='delete btn btn-danger btn-sm' href=\"{$url['delete_msg']}\" data-link=\"{$url['delete_msg']}\"><i class='fa fa-trash-o'></i></a>";
		}

		$balances = Office_Balance::where("office_id", $offices->id)->get() ;
		foreach($balances as $balance){
			$total_balance = $total_balance + $balance->total_amount;
			// Add action buttons
			$url = ['edit' => route('office_balance.edit', $balance->id), 'delete_msg' => "/office_balance/{$balance->id}/deleteMsg"];
			$balance->action_buttons = DatatablesHelperController::getEditButton($url, false, $perm) . "<a class='delete btn btn-danger btn-sm' href=\"{$url['delete_msg']}\" data-link=\"{$url['delete_msg']}\"><i class='fa fa-trash-o'></i></a>";
		}

		// Get office invoices data
		$office_invoices = DB::table('officeinvoice_data')->where('from_office', '=', $id)->get();
		foreach($office_invoices as $office_invoice){
			$to_office = Offices::find($office_invoice->to_office);
			$office_invoice->officeName = $to_office->office_name ?? '';
			// Add action buttons
			$url = ['show' => route('office_invoices_detail.show', $office_invoice->officeinvoice_dataId)];
			$office_invoice->action_buttons = DatatablesHelperController::getShowButton($url);
		}

        return view('office.show', compact('offices','title','total_tour_expense','total_utility_expense','total_employee_salary','total_office_earning','total_balance','tour_expenses','utility_expenses','employee_salaries','office_earnings','balances','office_invoices'));
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

        $offices = Offices::findOrFail($id);
        foreach ([Office_Tours::class, Office_Utility_Expenses::class, Office_Employes_Salary::class, Office_Earnings::class, Office_Balance::class] as $model) {
            if ($model::where('office_id', $id)->exists()) {
                LaravelFlashSessionHelper::setFlashMessage('Office has linked financial records and cannot be deleted.', 'error');
                return request()->ajax() ? URL::to('office') : redirect()->route('office.index');
            }
        }
        if (DB::table('officeinvoice_data')->where('from_office', $id)->orWhere('to_office', $id)->exists()) {
            LaravelFlashSessionHelper::setFlashMessage('Office has linked invoices and cannot be deleted.', 'error');
            return request()->ajax() ? URL::to('office') : redirect()->route('office.index');
        }
		$invoice = Invoices::where('office_id',$id)->get();
		$office_invoice = ClientInvoices::where('office_id',$id)->get();
		if(count($office_invoice) != '0'){
			LaravelFlashSessionHelper::setFlashMessage("Office $offices->office_name can not be delete because it is used in Client Invoice", 'error');
			return request()->ajax() ? URL::to('office') : redirect()->route('office.index');
		}
		if(count($invoice) != '0'){
			LaravelFlashSessionHelper::setFlashMessage("Office $offices->office_name can not be delete because it is used in Supplier Invoice", 'error');
			return request()->ajax() ? URL::to('office') : redirect()->route('office.index');
		}
		if($offices->status == '1'){
			LaravelFlashSessionHelper::setFlashMessage("Office $offices->office_name can not be delete because it is used in tour", 'error');
			return request()->ajax() ? URL::to('office') : redirect()->route('office.index');
		}

		
        $offices->find($id)->delete();
      LaravelFlashSessionHelper::setFlashMessage("Office $offices->office_name deleted", 'success');

        return request()->ajax() ? URL::to('office') : redirect()->route('office.index');
    }
    
    
}
