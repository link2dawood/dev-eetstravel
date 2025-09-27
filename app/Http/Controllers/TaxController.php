<?php

namespace App\Http\Controllers;

use Amranidev\Ajaxis\Ajaxis;
use App\Tax;
use App\ClientContacts;
use App\Comment;
use App\Helper\CitiesHelper;
use App\Helper\FileTrait;
use App\Helper\LaravelFlashSessionHelper;
use App\Helper\PermissionHelper;
use App\Repository\Contracts\ClientRepository;
use DB;
use Illuminate\Http\Request;
use URL;
use Yajra\Datatables\Datatables;
use Illuminate\Support\Facades\View;
use Hash;
use Auth;
class TaxController extends Controller
{
    use FileTrait;

    protected $clients;


    public function __construct(ClientRepository $clientRepository)
    {
        $this->middleware('permissions.required');
        $this->clients = $clientRepository;
        $this->middleware('preventBackHistory');
        $this->middleware('auth');
    }

    public function getShowButton($id, $isQuotation = false, $tour, array $perm)
	{
		$url = array(
			'show'       => route('taxes.show', ['tax' => $id]),
			'edit'       => route('taxes.edit', ['tax' => $id]),
			'delete_msg' => "/taxes/{$id}/deleteMsg",
			'id'         => $id
		);

		return DatatablesHelperController::getShowButton($url) . '<a class="delete btn btn-danger btn-sm" style="margin-right: 5px" data-toggle="modal" data-target="#myModal" data-link="' . $url["delete_msg"] . '"><i class="fa fa-trash-o"></i></a>';
		//        return DatatablesHelperController::getActionButton($url, $isQuotation, $tour);
	}

    public function data(Request $request)
	{

		$taxes = Tax::all();

		$permission_destroy = PermissionHelper::$relationsPermissionDestroy['App\Invoices'];
		$permission_edit = PermissionHelper::$relationsPermissionEdit['App\Invoices'];
		$permission_show = PermissionHelper::$relationsPermissionShow['App\Invoices'];

		$perm = [];
		$perm['show'] = Auth::user()->can($permission_show);
		$perm['edit'] = Auth::user()->can($permission_edit);
		$perm['destroy'] = Auth::user()->can($permission_destroy);
		$perm['clone'] = Auth::user()->can('accounting.create');

		return Datatables::of($taxes)


			

        ->addColumn('action', function ($taxes) use ($perm) {

            return $this->getShowButton($taxes->id, false, $taxes, $perm);
        })
			->rawColumns(['select', 'action', 'link'])
			->make(true);
	}

    /**
     * Display a listing of the resource.
     *
     * @return  \Illuminate\Http\Response
     */
    public function index()
    {
        // Get all taxes data (same as the AJAX data method)
        $taxes = Tax::all();

        $permission_destroy = PermissionHelper::$relationsPermissionDestroy['App\Invoices'];
        $permission_edit = PermissionHelper::$relationsPermissionEdit['App\Invoices'];
        $permission_show = PermissionHelper::$relationsPermissionShow['App\Invoices'];

        $perm = [];
        $perm['show'] = Auth::user()->can($permission_show);
        $perm['edit'] = Auth::user()->can($permission_edit);
        $perm['destroy'] = Auth::user()->can($permission_destroy);
        $perm['clone'] = Auth::user()->can('accounting.create');

        // Add action buttons to each tax
        $taxesData = $taxes->map(function ($tax) use ($perm) {
            $tax->action_buttons = $this->getShowButton($tax->id, false, $tax, $perm);
            return $tax;
        });

        return view('taxes.index', compact('taxesData'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return  \Illuminate\Http\Response
     */
    public function create()
    {
        $title = 'Create - Taxes';

        return view('taxes.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param    \Illuminate\Http\Request $request
     * @return  \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validateTax($request);
       
        $tax = DB::table("invoice_taxes")->insert([
            "name" => $request->name ,
            "value" => $request->percentageInput ,
    ]);


        LaravelFlashSessionHelper::setFlashMessage("Tax $request->name  created", 'success');

        $data = ['route' => route('taxes.index')];
		return redirect()->route('taxes.index');
        //return response()->json($data);
    }

    /**
     * Display the specified resource.
     *
     * @param    \Illuminate\Http\Request $request
     * @param    int $id
     * @return  \Illuminate\Http\Response
     */
    public function show($id, Request $request)
    {
        $title = 'Show - Taxes';

        if ($request->ajax()) {
            return URL::to('taxes/' . $id);
        }

        $tax = Tax::find($id);

        if($tax == null){
            return abort(404);
        }

         return view('taxes.show', compact('tax'));
    }

    /**
     * Show the form for editing the specified resource.
     * @param    \Illuminate\Http\Request $request
     * @param    int $id
     * @return  \Illuminate\Http\Response
     */
    public function edit($id, Request $request)
    {
        $title = 'Edit - Tax';
        if ($request->ajax()) {
            return URL::to('taxes/' . $id . '/edit');
        }

        $tax = Tax::find($id);

        return view('taxes.edit', compact('tax'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param    \Illuminate\Http\Request $request
     * @param    int $id
     * @return  \Illuminate\Http\Response
     */
    public function update($id, Request $request)
    {
        
        $this->validateTax($request);
       
        $tax = Tax::find($id)->update([
            "name" => $request->name,
            "value" => $request->percentageInput,
        ]);
        $tax =Tax::find($id);


		LaravelFlashSessionHelper::setFlashMessage("Client $tax->name edited", 'success');

       
		return redirect()->back();
       
    }

    /**
     * Delete confirmation message by Ajaxis.
     *
     * @link      https://github.com/amranidev/ajaxis
     * @param    \Illuminate\Http\Request $request
     * @return  String
     */
    public function DeleteMsg($id, Request $request)
    {
        $msg = Ajaxis::BtDeleting('Warning!!', 'Would you like to remove This?', '/clients/' . $id . '/delete');

        if ($request->ajax()) {
            return $msg;
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param    int $id
     * @return  \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $client = $this->clients->getById($id);
        $this->removeFile($client);
        $this->clients->deleteById($id);
        Comment::query()->where('reference_type', Comment::$services['client'])->where('reference_id', $id)->delete();
        LaravelFlashSessionHelper::setFlashMessage("Client $client->name deleted", 'success');

        return URL::to('clients');
    }

    public function validateTax(Request $request)
    {
        $this->validate($request, [
            'name'  => 'required',
            'percentageInput'     => 'required',
        ]);
    }
	
	
public function getClientContacts(Request $request){
        $count = $request->get('itemCount');
        $clientId = $request->get('clientId');

        $clientContacts = ClientContacts::query()->where('client_id', $clientId)->get();

        foreach ($clientContacts as $clientContact){
            $clientContact['count'] = $count + 1;
            $count++;
        }
		$hotelContacts = $clientContacts ;
        $view = View::make('component.get_hotel_contacts_form', compact('hotelContacts'));

        $data = ['content' => $view->render(), 'count' => $count];
        return $data;
    }
}
