<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Redirect;
use Illuminate\Http\Request;
use Image;
use DB;
use App\Offices;
use App\Office_Tours;
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
class TourExpenseController extends Controller {

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
     * @param  $id Office_Tours id
     * @return mixed
     */
    
    public function getButton($id, $isQuotation = false, $tour, array $perm)
    {
        $url = array('show'       => route('office.show', ['id' => $id]),
                     'edit'       => route('tour_expenses.edit', ['id' => $id]),
                     'delete_msg' => "/tour_expenses/{$id}/deleteMsg",
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
        return view('office.tour_expenses.create', compact('tours','clients','hotels','options','events','buses','restaurants','office'));
    }
    public function store(Request $request){
		$validated = $this->validateData($request);
        Office_Tours::create($validated);
        return redirect()->route('office.show', $request->office_id);
    }
	public function validateData(Request $request){
	
		return $this->validate($request, [
            'office_id' => 'required|integer|exists:office_fees,id',
            'tour_name'  => 'required|string|max:200',
            'tour_expenses'     => 'required|numeric|min:0|max:999999999.99',
            'tour_departure_date' => 'required|date',
			'tour_return_date' => 'required|date|after_or_equal:tour_departure_date',
        ]);
	}
    public function edit($id, Request $request){
		$office_tours = Office_Tours::findOrFail($id);
		$tours = Tour::all();
       
        $options = array("Event","Restaurant");
		return view('office.tour_expenses.edit', compact('tours','office_tours'));
	}
	public function update($id,Request $request){
        $validated = $this->validateData($request);
        $office_tours = Office_Tours::findOrFail($id);
        $office_tours->update($validated);
		return redirect()->route('office.show', $request->office_id);
	}
    public function show($id, Request $request)
    {
        $record = Office_Tours::findOrFail($id);
        return redirect()->route('office.show', $record->office_id);
    }
     
     public function DeleteMsg($id, Request $request)
    {
		
        $msg = Ajaxis::BtDeleting('Warning!!', 'Would you like to remove This?', '/tour_expenses/' . $id . '/delete');
        if ($request->ajax()) {
            return $msg;
        }
    }
	public function destroy($id)
    {

        $office_tours = Office_Tours::findOrFail($id);
        $office_tours->find($id)->delete();
      LaravelFlashSessionHelper::setFlashMessage("Tour Expense $office_tours->tour_name deleted", 'success');

        return request()->ajax() ? URL::to('office') : redirect()->route('office.index');
    }
    
    public function index() { return redirect()->route('office.index'); }
}
