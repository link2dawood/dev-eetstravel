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
    public function create(Request $request, $id = null)
    {
        if (!$id) return redirect()->route('office.index')->with('info', 'Choose an office before adding financial records.');
        $options = array("Event","Restaurant");
		$office = Offices::findOrFail($id);
        return view('office.employe_salary.create', compact('office'));
    }
    public function store(Request $request){
		$validated = $this->validateData($request);
        Office_Employes_Salary::create($validated);
        return redirect()->route('office.show', $request->office_id);
    }
	public function validateData(Request $request){
	
		return $this->validate($request, [
            'office_id' => 'required|integer|exists:office_fees,id',
            'employe_name'  => 'required|string|max:200',
            'employe_salary'     => 'required|numeric|min:0|max:999999999.99',
            'month'     => 'required|date',
			'bonuses'     => 'required|numeric|min:0|max:999999999.99',
        ]);
	}
    public function edit($id, Request $request){
		$office_employes_salary = Office_Employes_Salary::findOrFail($id);
       
		return view('office.employe_salary.edit', compact('office_employes_salary'));
	}
	public function update($id,Request $request){
        $validated = $this->validateData($request);
        $office_employes_salary = Office_Employes_Salary::findOrFail($id);
        $office_employes_salary->update($validated);
		return redirect()->route('office.show', $request->office_id);
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

        $office_employes_salary = Office_Employes_Salary::findOrFail($id);
        $office_employes_salary->find($id)->delete();
      LaravelFlashSessionHelper::setFlashMessage("Employee Salary $office_employes_salary->employe_name deleted", 'success');

        return request()->ajax() ? URL::to('office') : redirect()->route('office.index');
    }
    
    
    public function show($id, Request $request)
    {
        $record = Office_Employes_Salary::findOrFail($id);
        return redirect()->route('office.show', $record->office_id);
    }
}
