<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Redirect;
use Illuminate\Http\Request;
use Image;
use DB;
use App\Offices;
use App\Office_Employes_Salary;
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
class EmployesSalaryController extends Controller {

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
     * @param  $id Office_Employes_Salary id
     * @return mixed
     */
    public function index() {
        
        return view('office.index');
    }
    public function getButton($id, $isQuotation = false, $tour, array $perm)
    {
        $url = array('show'       => route('office.show', ['id' => $id]),
                     'edit'       => route('employes-salary.edit', ['id' => $id]),
                     'delete_msg' => "/employes-salary/{$id}/deleteMsg",
                     'id'         => $id);

        $action =
            "<a class='delete btn btn-danger btn-sm' href=\"{$url['delete_msg']}\" data-link=\"{$url['delete_msg']}\"><i class='fa fa-trash-o'></i></a>";
        $action .= "</div>";
        return  DatatablesHelperController::getEditButton($url, $isQuotation, $perm).$action;
//        return DatatablesHelperController::getActionButton($url, $isQuotation, $tour);
    }
    public function data(Request $request, $id)
    {
        
         $office_employes_salary = Office_Employes_Salary::where("office_id", $id)->get();
         $permission_destroy = PermissionHelper::$relationsPermissionDestroy['App\Offices'];
         $permission_edit = PermissionHelper::$relationsPermissionEdit['App\Offices'];
         $permission_show = PermissionHelper::$relationsPermissionShow['App\Offices'];
         
         $perm = [];        
         $perm['show'] = Auth::user()->can($permission_show);        
         $perm['edit'] = Auth::user()->can($permission_edit);
         $perm['destroy'] = Auth::user()->can($permission_destroy);
         $perm['clone'] = Auth::user()->can('office.create');
         
        return Datatables::of($office_employes_salary)->addColumn('action', function ($office_employes_salary) use($perm) {
                return $this->getButton($office_employes_salary->id, false, $office_employes_salary, $perm);
            })
            ->rawColumns(['select', 'action', 'link'])
            ->make(true);
    }

    public function create(Request $request, $id)
    {
        $options = array("Event","Restaurant");
		$office = Offices::find($id);
        return view('office.employe_salary.create', compact('office'));
    }
    public function store(Request $request){
		$this->validateData($request);
        Office_Employes_Salary::create($request->except(["attach"]));
        return redirect()->back();
    }
	public function validateData(Request $request){
	
		$this->validate($request, [
            'employe_name'  => 'required',
            'employe_salary'     => 'required',
            'month'     => 'required',
			'bonuses'     => 'required',
        ]);
	}
    public function edit($id, Request $request){
		$office_employes_salary = Office_Employes_Salary::find($id);
       
		return view('office.employe_salary.edit', compact('office_employes_salary'));
	}
	public function update($id,Request $request){
        $office_employes_salary = Office_Employes_Salary::find($id);
        $office_employes_salary->update($request->except(["attach"]));
		return redirect()->back();
	}
     
	public function DeleteMsg($id, Request $request)
    {
		
        $msg = Ajaxis::BtDeleting('Warning!!', 'Would you like to remove This?', '/employes-salary/' . $id . '/delete');
        if ($request->ajax()) {
            return $msg;
        }
    }
	public function destroy($id)
    {

        $office_employes_salary = Office_Employes_Salary::find($id);
        $office_employes_salary->find($id)->delete();
      LaravelFlashSessionHelper::setFlashMessage("Employee Salary $office_employes_salary->employe_name deleted", 'success');

        return URL::to('office');
    }
    
    
}
