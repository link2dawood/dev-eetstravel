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
    public function create(Request $request, $id = null)
    {
        if (!$id) return redirect()->route('office.index')->with('info', 'Choose an office before adding financial records.');
    	
        $tours = Tour::all();
        $clients = Client::all();
        $hotels = Hotel::all();
        $events = Event::all();
        $buses = Bus::all();
        $restaurants = Restaurant::all();

        $options = array("Event","Restaurant");
		
		$office = Offices::findOrFail($id);
		
        return view('office.utility_expenses.create', compact('tours','clients','hotels','options','events','buses','restaurants','office'));
    }
    public function store(Request $request){
		$validated = $this->validateData($request);
        Office_Utility_Expenses::create($validated);
        return redirect()->route('office.show', $request->office_id);
    }
	public function validateData(Request $request){
	
		return $this->validate($request, [
            'office_id' => 'required|integer|exists:office_fees,id',
            'subject_of_expense'  => 'required|string|max:200',
            'month'     => 'required|date',
            'monthly_expense'     => 'required|numeric|min:0|max:999999999.99',
        ]);
	}
    public function edit($id, Request $request){
		$utility_expenses = Office_Utility_Expenses::findOrFail($id);
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
        $validated = $this->validateData($request);
        $utility_expenses = Office_Utility_Expenses::findOrFail($id);
        $utility_expenses->update($validated);
		return redirect()->route('office.show', $request->office_id);
	}
    public function show($id, Request $request)
    {
        $record = Office_Utility_Expenses::findOrFail($id);
        return redirect()->route('office.show', $record->office_id);
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

        $utility_expenses = Office_Utility_Expenses::findOrFail($id);
        $utility_expenses->find($id)->delete();
      LaravelFlashSessionHelper::setFlashMessage("Utility Expense $utility_expenses->subject_of_expense	 deleted", 'success');

        return request()->ajax() ? URL::to('office') : redirect()->route('office.index');
    }
}
