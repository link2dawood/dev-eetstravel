<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Redirect;
use Illuminate\Http\Request;
use Image;
use DB;
use App\Offices;
use App\Office_Utility_Expenses;
use App\Attachment;
use App\User;
use App\Status;
use App\RoomTypes;
use App\Tour;
use App\Client;
use App\Hotel;
use App\Event;
use App\Restaurant;
use App\Guide;
use App\Bus;
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
class UtilityExpenseController extends Controller {

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
     * @param  $id Office_Utility_Expenses id
     * @return mixed
     */
    public function index() {
        
        return view('office.index');
    }
    public function getButton($id, $isQuotation = false, $tour, array $perm)
    {
        $url = array('show'       => route('office.show', ['id' => $id]),
                     'edit'       => route('utility_expenses.edit', ['id' => $id]),
                     'delete_msg' => "/utility_expenses/{$id}/deleteMsg",
                     'id'         => $id);

        $action =
            "<a class='delete btn btn-danger btn-sm' href=\"{$url['delete_msg']}\" data-link=\"{$url['delete_msg']}\"><i class='fa fa-trash-o'></i></a>";
        $action .= "</div>";
        return  DatatablesHelperController::getEditButton($url, $isQuotation, $perm).$action;
//        return DatatablesHelperController::getActionButton($url, $isQuotation, $tour);
    }
    public function data(Request $request, $id)
    {
        
         $office_utility_expenses = Office_Utility_Expenses::where("office_id", $id)->get();;
         $permission_destroy = PermissionHelper::$relationsPermissionDestroy['App\Offices'];
         $permission_edit = PermissionHelper::$relationsPermissionEdit['App\Offices'];
         $permission_show = PermissionHelper::$relationsPermissionShow['App\Offices'];
         
         $perm = [];        
         $perm['show'] = Auth::user()->can($permission_show);        
         $perm['edit'] = Auth::user()->can($permission_edit);
         $perm['destroy'] = Auth::user()->can($permission_destroy);
         $perm['clone'] = Auth::user()->can('office.create');
        
        return Datatables::of($office_utility_expenses)->addColumn('action', function ($office_utility_expenses) use($perm) {
                return $this->getButton($office_utility_expenses->id, false, $office_utility_expenses, $perm);
            })
            ->rawColumns(['select', 'action', 'link'])
            ->make(true);
    }

    public function create(Request $request , $id)
    {
    	
        $tours = Tour::all();
        $clients = Client::all();
        $hotels = Hotel::all();
        $events = Event::all();
        $buses = Bus::all();
        $restaurants = Restaurant::all();

        $options = array("Event","Restaurant");
		
		$office = Offices::find($id);
		
        return view('office.utility_expenses.create', compact('tours','clients','hotels','options','events','buses','restaurants','office'));
    }
    public function store(Request $request){
		$this->validateData($request);
        Office_Utility_Expenses::create($request->except(["attach"]));
        return view('office.create');
    }
	public function validateData(Request $request){
	
		$this->validate($request, [
            'subject_of_expense'  => 'required',
            'month'     => 'required',
            'monthly_expense'     => 'required',
        ]);
	}
    public function edit($id, Request $request){
		$utility_expenses = Office_Utility_Expenses::find($id);
		$tours = Tour::all();
        $clients = Client::all();
        $hotels = Hotel::all();
        $events = Event::all();
        $buses = Bus::all();
        $restaurants = Restaurant::all();

        $options = array("Event","Restaurant");
		return view('office.utility_expenses.edit', compact('tours','clients','hotels','options','events','buses','restaurants','utility_expenses'));
	}
	public function update($id,Request $request){
        $utility_expenses = Office_Utility_Expenses::find($id);
        $utility_expenses->update($request->except(["attach"]));
		return redirect()->back();
	}
    public function show($id, Request $request)
    {
  
        $title = 'Show - Payments';

        if ($request->ajax()) {
            return URL::to('office/' . $id);
        }

        $office_Utility_Expenses =Office_Utility_Expenses::find($id);

        if($office_Utility_Expenses == null){
            return abort(404);
        }

      
        return view('office.show', compact('office_Utility_Expenses','title'));
    }
     
           
    public function DeleteMsg($id, Request $request)
    {
		
        $msg = Ajaxis::BtDeleting('Warning!!', 'Would you like to remove This?', '/utility_expenses/' . $id . '/delete');
        if ($request->ajax()) {
            return $msg;
        }
    }
	public function destroy($id)
    {

        $utility_expenses = Office_Utility_Expenses::find($id);
        $utility_expenses->find($id)->delete();
      LaravelFlashSessionHelper::setFlashMessage("Utility Expense $utility_expenses->subject_of_expense	 deleted", 'success');

        return URL::to('office');
    }
}
