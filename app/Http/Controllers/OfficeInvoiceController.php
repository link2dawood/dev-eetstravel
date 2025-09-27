<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Redirect;
use Illuminate\Http\Request;
use Image;
use DB;
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
use App\Offices;
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
use PDF;
class OfficeInvoiceController extends Controller {

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
        $url = array('show'       => route('office_invoices_detail.show', ['officeinvoice_dataId' => $id]),
                     'edit'       => route('office_earning.edit', ['officeinvoice_dataId' => $id]),
                     'delete_msg' => "/office_earning/{$id}/deleteMsg",
                     'id'         => $id);

        return DatatablesHelperController::getActionButtonTours($url, $isQuotation, $perm);
//        return DatatablesHelperController::getActionButton($url, $isQuotation, $tour);
    }
    
 	public function getShowButton($id, $isQuotation = false, $tour, array $perm)
    {
       $url = array('show'       => route('office_invoices_detail.show', ['officeinvoice_dataId' => $id]),
                     'edit'       => route('office_earning.edit', ['officeinvoice_dataId' => $id]),
                     'delete_msg' => "/office_earning/{$id}/deleteMsg",
                     'id'         => $id);

        return DatatablesHelperController::getShowButton($url);
//        return DatatablesHelperController::getActionButton($url, $isQuotation, $tour);
    }

    public function create($id,Request $request)
    {
		$office_from = Offices::find($id);
		$offices_to = Offices::all();

        return view('office.office_invoices.create',compact("office_from","offices_to"));
    }
	
public function store(Request $request)
{

    $data = $request->input('data');

    // Create an officeinvoice_data record
    try {
        $officeinvoiceData = DB::table('officeinvoice_data')->insertGetId([
			'from_office' => $request->input('from_office'),
			'to_office' => $request->input('to_office'),
            'invoice_no' => $request->input('invoiceno'),
            'date' => $request->input('dateoffice'),
        ]);
    } catch (\Exception $e) {
        dd($e->getMessage());
    }

    foreach ($data as $row) {
        try {
            DB::table('office_invoices')->insert([
                'officeinvoice_dataId' => $officeinvoiceData,
                'officeinvoice_date' => $row['officeinvoice_date'],
                'officeinvoice_item' => $row['office_item'],
                'des' => $row['des'],
                'officeinvoice_code' => $row['officeinvoice_code'],
                'officeinvoice_amount' => $row['officeinvoice_amount'],
            ]);
        } catch (\Exception $e) {
            dd($e->getMessage());
        }
    }

    // Redirect or return response
    return redirect()->back()->with('success', 'Office invoices saved successfully.');
}
	
	// get invoice data in table SQL Innerjoins ---//
public function data($id)
{

    
	     $result = DB::select('select * from officeinvoice_data');
	$result = DB::table('officeinvoice_data')
    ->where('from_office', '=', $id)
    ->get();
	
		 $permission_destroy = PermissionHelper::$relationsPermissionDestroy['App\Invoices'];
         $permission_edit = PermissionHelper::$relationsPermissionEdit['App\Invoices'];
         $permission_show = PermissionHelper::$relationsPermissionShow['App\Invoices'];
         
         $perm = [];        
         $perm['show'] = Auth::user()->can($permission_show);        
         $perm['edit'] = Auth::user()->can($permission_edit);
         $perm['destroy'] = Auth::user()->can($permission_destroy);
         $perm['clone'] = Auth::user()->can('accounting.create');

	 return Datatables::of($result)->addColumn('action', function ($result) use($perm) {
		 
                return $this->getShowButton($result->officeinvoice_dataId  , false, $result, $perm);
            })
		 	 ->addColumn('officeName',function($result){
            $office = Offices::find($result->to_office);
            return $office->office_name;
        })
            ->rawColumns(['select', 'action', 'link'])
            ->make(true);
		// the result in 'data' key
      //  return response()->json(['data' => $result]); // the result in 'data' key
   
}
	
	 public function office_invoice_details(Request $request , $id)
    {
       // $invoice_detail = DB::table('office_invoices')>where('officeinvoice_dataId', '=', $id)
   // ->get();
		 
		 $officeinvoice_dataId = $id;
        return view('office.office_invoices.office_invoice_detail',compact('officeinvoice_dataId'));
    }
	//------ get invoice details ------>
public function getOfficeInvoicesdeatailsdata($id)
{

        $result = DB::table('office_invoices')
            ->where('officeinvoice_dataId', $id)
            ->get();
	 
		 $permission_destroy = PermissionHelper::$relationsPermissionDestroy['App\Invoices'];
         $permission_edit = PermissionHelper::$relationsPermissionEdit['App\Invoices'];
         $permission_show = PermissionHelper::$relationsPermissionShow['App\Invoices'];
         
         $perm = [];        
         $perm['show'] = Auth::user()->can($permission_show);        
         $perm['edit'] = Auth::user()->can($permission_edit);
         $perm['destroy'] = Auth::user()->can($permission_destroy);
         $perm['clone'] = Auth::user()->can('accounting.create');

	 return Datatables::of($result)->addColumn('action', function ($result) use($perm) {
		 
                return $this->getShowButton($result->officeinvoice_dataId  , false, $result, $perm);
            })
            ->rawColumns(['select', 'action', 'link'])
            ->make(true);
		// the result in 'data' key
      //  return response()->json(['data' => $result]); // the result in 'data' key
   
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
	public function pdfExport(Request $request, $id){
  $officeinvoice_dataId = DB::table('officeinvoice_data')
    ->where('officeinvoice_dataId', $id)
    ->first();
		$invoice_items = DB::table('office_invoices')
    ->where('officeinvoice_dataId', $id)->get();
		$from_office = Offices::find($officeinvoice_dataId->from_office);
		$to_office = Offices::find($officeinvoice_dataId->to_office);

    view()->share([
        'officeinvoice_dataId' => $officeinvoice_dataId,
		'invoice_items' => $invoice_items,
		'from_office' => $from_office,
		'to_office' => $to_office,
      
    ]);
    PDF::setOptions(['isHtml5ParserEnabled' => true,'defaultPaperSize' =>'a3']);
    $pdf = PDF::loadView('export.office_invoices.officeInvoicesPdf');
    $pdfName = 'office_invoice.pdf';
    return $pdf->download(str_replace(" ","_",$pdfName));
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
