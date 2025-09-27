<?php

namespace App\Http\Controllers;

use Amranidev\Ajaxis\Ajaxis;
use App\Client;
use App\Http\Requests\StoreClientRequest;
use App\Http\Requests\UpdateClientRequest;
use App\ClientContacts;
use App\Comment;
use App\Helper\CitiesHelper;
use App\Helper\FileTrait;
use App\Helper\LaravelFlashSessionHelper;
use App\Helper\PermissionHelper;
use App\Repository\Contracts\ClientRepository;
use Illuminate\Http\Request;
use URL;
use Illuminate\Support\Facades\View;
use Hash;
use Illuminate\Support\Facades\Crypt;


class ClientController extends Controller
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

    public function getButton($id, $client)
    {
        $url = ['show'       => route('clients.show', ['id' => $id]),
            'edit'       => route('clients.edit', ['id' => $id]),
            'delete_msg' => "/clients/{$id}/deleteMsg"];

        return DatatablesHelperController::getActionButton($url, false, $client);
    }

    public function data(Request $request)
    {
        $query = Client::distinct()
            ->leftJoin('countries', 'countries.alias', '=', 'clients.country')
            ->leftJoin('cities', 'cities.id', '=', 'clients.city')
            ->select([
                'clients.id',
                'clients.name',
                'clients.address',
                'clients.account_no',
                'clients.company_address',
                'clients.invoice_address',
                'clients.work_phone',
                'clients.work_email',
                'clients.work_fax',
                'cities.name as city',
                'countries.name as country'
            ]);

        // Get pagination parameters
        $perPage = $request->get('length', 15);
        $page = $request->get('start', 0) / $perPage + 1;

        // Get total count
        $total = $query->count();

        // Apply pagination
        $clients = $query->skip(($page - 1) * $perPage)->take($perPage)->get();

        // Process each client
        foreach($clients as $client) {
            $client->action = $this->getButton($client->id, $client);
        }

        return response()->json([
            'data' => $clients,
            'recordsTotal' => $total,
            'recordsFiltered' => $total
        ]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return  \Illuminate\Http\Response
     */
    public function index()
    {
        $title = 'Index - client';
        $clients = Client::leftJoin('countries', 'countries.alias', '=', 'clients.country')
            ->leftJoin('cities', 'cities.id', '=', 'clients.city')
            ->select([
                'clients.*',
                'countries.name as country_name',
                'cities.name as city_name'
            ])
            ->paginate(15);

        return view('clients.index', compact('clients', 'title'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return  \Illuminate\Http\Response
     */
    public function create()
    {
        $title = 'Create - client';
    
   
        return view('clients.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param    \Illuminate\Http\Request $request
     * @return  \Illuminate\Http\Response
     */
    public function store(StoreClientRequest $request)
    {
        $request = CitiesHelper::setCityGeneral($request);
		$request->password =  Crypt::encryptString($request->password);


        $client = $this->clients->create($request->except(['attach']));
		$client->password = $request->password;
		$client->save();

        LaravelFlashSessionHelper::setFlashMessage("Client $client->name created", 'success');

        $this->addFile($request, $client);
        $data = ['route' => route('clients.index')];
		return redirect()->back();
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
        $title = 'Show - client';

        if ($request->ajax()) {
            return URL::to('clients/' . $id);
        }

        $client = $this->clients->getById($id);

        if($client == null){
            return abort(404);
        }

        $files = $this->parseAttach($client);
		$contacts = ClientContacts::query()->where('client_id', $id)->get();
        return view('clients.show', compact('client', 'files', 'title', 'contacts'));
    }

    /**
     * Show the form for editing the specified resource.
     * @param    \Illuminate\Http\Request $request
     * @param    int $id
     * @return  \Illuminate\Http\Response
     */
    public function edit($id, Request $request)
    {
        $title = 'Edit - client';
        if ($request->ajax()) {
            return URL::to('clients/' . $id . '/edit');
        }

        $client = $this->clients->getById($id);

        $files = $this->parseAttach($client);

        return view('clients.edit', compact('client', 'files', 'title'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param    \Illuminate\Http\Request $request
     * @param    int $id
     * @return  \Illuminate\Http\Response
     */
    public function update($id, UpdateClientRequest $request)
    {
		$request->password =  Crypt::encryptString($request->password);
        $request = CitiesHelper::setCityGeneral($request);
		$contacts = $request->get('contacts');
		

        $this->clients->updateById($id, $request->except(['attach','contacts']));
		$client = Client::find($id);
		$client->password = $request->password;
		$client->save();
        $client = $this->clients->getById($id);

		
		if($contacts){
            $data = ['hotelContacts' => true, 'fullNameErrorValidate' => trans('main.ContactsshouldnothaveanemptyFullName')];
            foreach ($contacts as $itemContact){
                if(!$itemContact['contact_full_name']){
					return redirect()->back();
                    return response()->json($data);
                }
            }
        }

		ClientContacts::query()->where('client_id', $id)->delete();

        if($contacts){
            foreach ($contacts as $contact) {
                $clientContact = new ClientContacts();
                $clientContact->full_name = $contact['contact_full_name'];
                $clientContact->mobile_phone = $contact['contact_mobile_phone'];
                $clientContact->work_phone = $contact['contact_work_phone'];
                $clientContact->email = $contact['contact_email'];
                $clientContact->client_id = $id;
                $clientContact->save();
            }
        }

		LaravelFlashSessionHelper::setFlashMessage("Client $client->name edited", 'success');

        $this->addFile($request, $client);
        $data = ['route' => route('clients.index')];
		return redirect()->back();
        return response()->json($data);
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
