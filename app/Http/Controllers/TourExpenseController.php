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
    public function data(Request $request,$id)
    {
        
         $office_tours = Office_Tours::where("office_id", $id)->get();
         $permission_destroy = PermissionHelper::$relationsPermissionDestroy['App\Offices'];
         $permission_edit = PermissionHelper::$relationsPermissionEdit['App\Offices'];
         $permission_show = PermissionHelper::$relationsPermissionShow['App\Offices'];

         $perm = [];        
         $perm['show'] = Auth::user()->can($permission_show);        
         $perm['edit'] = Auth::user()->can($permission_edit);
         $perm['destroy'] = Auth::user()->can($permission_destroy);
         $perm['clone'] = Auth::user()->can('office.create');
        
        return Datatables::of($office_tours)->addColumn('action', function ($office_tours) use($perm) {
                return $this->getButton($office_tours->id, false, $office_tours, $perm);
            })
            ->rawColumns(['select', 'action', 'link'])
            ->make(true);
    }

    public function create(Request $request, $id)
    {
    	
        $tours = Tour::all();
        $clients = Client::all();
        $hotels = Hotel::all();
        $events = Event::all();
        $buses = Bus::all();
        $restaurants = Restaurant::all();

        $options = array("Event","Restaurant");
		
		$office = Offices::find($id);
        return view('office.tour_expenses.create', compact('tours','clients','hotels','options','events','buses','restaurants','office'));
    }
    public function store(Request $request){
		$this->validateData($request);
        Office_Tours::create($request->except(["attach"]));
        return redirect()->back();
    }
	public function validateData(Request $request){
	
		$this->validate($request, [
            'tour_name'  => 'required',
            'tour_expenses'     => 'required',
            'tour_departure_date'     => 'required',
			'tour_return_date'     => 'required',
        ]);
	}
    public function edit($id, Request $request){
		$office_tours = Office_Tours::find($id);
		$tours = Tour::all();
       
        $options = array("Event","Restaurant");
		return view('office.tour_expenses.edit', compact('tours','office_tours'));
	}
	public function update($id,Request $request){
        $office_tours = Office_Tours::find($id);
        $office_tours->update($request->except(["attach"]));
		return redirect()->back();
	}
    public function show($id, Request $request)
    {
  
        $title = 'Show - Payments';

        if ($request->ajax()) {
            return URL::to('office/' . $id);
        }

        $office_Tours =Office_Tours::find($id);

        if($office_Tours == null){
            return abort(404);
        }

      
        return view('office.show', compact('office_Tours','title'));
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

        $office_tours = Office_Tours::find($id);
        $office_tours->find($id)->delete();
      LaravelFlashSessionHelper::setFlashMessage("Tour Expense $office_tours->tour_name deleted", 'success');

        return URL::to('office');
    }
    
}
