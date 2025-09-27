<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Redirect;
use Illuminate\Http\Request;
use Image;
use DB;
use App\Offices;
use App\Office_Balance;
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
class BalanceAmountController extends Controller {

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
     * @param  $id Office_Balance id
     * @return mixed
     */
 
    public function getButton($id, $isQuotation = false, $tour, array $perm)
    {
        $url = array('show'       => route('office.show', ['id' => $id]),
                     'edit'       => route('office_balance.edit', ['id' => $id]),
                     'delete_msg' => "/office_balance/{$id}/deleteMsg",
                     'id'         => $id);

        $action =
            "<a class='delete btn btn-danger btn-sm' href=\"{$url['delete_msg']}\" data-link=\"{$url['delete_msg']}\"><i class='fa fa-trash-o'></i></a>";
        $action .= "</div>";
        return  DatatablesHelperController::getEditButton($url, $isQuotation, $perm).$action;
//        return DatatablesHelperController::getActionButton($url, $isQuotation, $tour);
    }
    public function data(Request $request, $id)
    {
        
         $office_balance = Office_Balance::where("office_id", $id)->get();
         $permission_destroy = PermissionHelper::$relationsPermissionDestroy['App\Offices'];
         $permission_edit = PermissionHelper::$relationsPermissionEdit['App\Offices'];
         $permission_show = PermissionHelper::$relationsPermissionShow['App\Offices'];
         
         $perm = [];        
         $perm['show'] = Auth::user()->can($permission_show);        
         $perm['edit'] = Auth::user()->can($permission_edit);
         $perm['destroy'] = Auth::user()->can($permission_destroy);
         $perm['clone'] = Auth::user()->can('office.create');
         
        return Datatables::of($office_balance)->addColumn('action', function ($office_balance) use($perm) {
                return $this->getButton($office_balance->id, false, $office_balance, $perm);
            })
            ->rawColumns(['select', 'action', 'link'])
            ->make(true);
    }

    public function create(Request $request, $id)
    {
		$office = Offices::find($id);
        return view('office.office_balance.create', compact("office"));
    }
	public function validateData(Request $request){
	
		$this->validate($request, [
            'subject_of_balance'  => 'required',
            'month'     => 'required',
            'total_amount'     => 'required',
			'due_date'     => 'required',
        ]);
	}
    public function store(Request $request){
		$this->validateData($request);
        Office_Balance::create($request->except(["attach"]));
        return redirect()->back();
    }
    public function edit($id, Request $request){
		$office_balance = Office_Balance::find($id);
		return view('office.office_balance.edit',compact('office_balance'));
	}
	public function update($id,Request $request){
        $office_balance = Office_Balance::find($id);
        $office_balance->update($request->except(["attach"]));
		return redirect()->back();
	}
	public function DeleteMsg($id, Request $request)
    {
        $msg = Ajaxis::BtDeleting('Warning!!', 'Would you like to remove This?', '/office_balance/' . $id . '/delete');

        if ($request->ajax()) {
            return $msg;
        }
    }
	public function destroy($id)
    {

        $office_balance = Office_Balance::find($id);
        $office_balance->find($id)->delete();
      LaravelFlashSessionHelper::setFlashMessage("Office Balance $office_balance->subject_of_balance deleted", 'success');

        return URL::to('office');
    }
    
     
    
}
