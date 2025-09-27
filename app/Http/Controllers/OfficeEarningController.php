<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Redirect;
use Illuminate\Http\Request;
use Image;
use DB;
use App\Offices;
use App\Office_Earnings;
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
use Amranidev\Ajaxis\Ajaxis;
use URL;
class OfficeEarningController extends Controller {

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
     * @param  $id Office_Earnings id
     * @return mixed
     */
 
    public function getButton($id, $isQuotation = false, $tour, array $perm)
    {
        $url = array('show'       => route('office.show', ['id' => $id]),
                     'edit'       => route('office_earning.edit', ['id' => $id]),
                     'delete_msg' => "/office_earning/{$id}/deleteMsg",
                     'id'         => $id);
		$action =
            "<a class='delete btn btn-danger btn-sm' href=\"{$url['delete_msg']}\" data-link=\"{$url['delete_msg']}\"><i class='fa fa-trash-o'></i></a>";
        $action .= "</div>";
        return  DatatablesHelperController::getEditButton($url, $isQuotation, $perm).$action;
//        return DatatablesHelperController::getActionButton($url, $isQuotation, $tour);
    }
    
    public function data(Request $request, $id)
    {
        
         $office_earnings = Office_Earnings::where("office_id", $id)->get();
         $permission_destroy = PermissionHelper::$relationsPermissionDestroy['App\Offices'];
         $permission_edit = PermissionHelper::$relationsPermissionEdit['App\Offices'];
         $permission_show = PermissionHelper::$relationsPermissionShow['App\Offices'];
         
         $perm = [];        
         $perm['show'] = Auth::user()->can($permission_show);        
         $perm['edit'] = Auth::user()->can($permission_edit);
         $perm['destroy'] = Auth::user()->can($permission_destroy);
         $perm['clone'] = Auth::user()->can('office.create');
         
        return Datatables::of($office_earnings)->addColumn('action', function ($office_earnings) use($perm) {
                return $this->getButton($office_earnings->id, false, $office_earnings, $perm);
            })
            ->rawColumns(['select', 'action', 'link'])
            ->make(true);
    }

    public function create(Request $request, $id)
    {
		$office = Offices::find($id);
        return view('office.office_earning.create', compact('office'));
    }
    public function store(Request $request){
		$this->validateData($request);
        Office_Earnings::create($request->except(["attach"]));
        return redirect()->back();
    }
	public function validateData(Request $request){
	
		$this->validate($request, [
            'month'  => 'required',
            'revenue'     => 'required',
            'profit'     => 'required',
        ]);
	}
    public function edit($id, Request $request){
		$office_earnings = Office_Earnings::find($id);
		return view('office.office_earning.edit', compact('office_earnings'));
	}
	public function update($id,Request $request){
        $office_earnings = Office_Earnings::find($id);
        $office_earnings->update($request->except(["attach"]));
		return redirect()->back();
	}
    public function DeleteMsg($id, Request $request)
    {
        $msg = Ajaxis::BtDeleting('Warning!!', 'Would you like to remove This?', '/office_earning/' . $id . '/delete');

        if ($request->ajax()) {
            return $msg;
        }
    }
	public function destroy($id)
    {

        $office_earnings = Office_Earnings::find($id);
        $office_earnings->find($id)->delete();
      LaravelFlashSessionHelper::setFlashMessage("Office $office_earnings->month deleted", 'success');

        return URL::to('office');
    }
     
    
}
