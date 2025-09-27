<?php

namespace App\Http\Controllers;


use Amranidev\Ajaxis\Ajaxis;
use App\Driver;
use App\Helper\FileTrait;
use App\Helper\LaravelFlashSessionHelper;
use App\Helper\PermissionHelper;
use App\Repository\Contracts\DriverRepository;
use App\Transfer;
use Illuminate\Http\Request;
use URL;
use Illuminate\Support\Facades\Session;
use App\Library\Services\DeleteModel;

class DriverController extends Controller
{

    use FileTrait;

    /**
     * @var DriverRepository
     */
    private $driverRepository;

    public function getButton($id, $driver)
	{
		$url = array('show'       => route('driver.show', ['id' => $id]),
		             'edit'       => route('driver.edit', ['id' => $id]),
		             'delete_msg' => "/driver/{$id}/delete_msg");

		return DatatablesHelperController::getActionButton($url, false, $driver);
	}

	public function data(Request $request)
	{
		$query = Driver::leftJoin('transfers', 'transfers.id', '=', 'drivers.transfer_id')
			->select([
				'drivers.id',
				'drivers.name',
				'drivers.phone',
				'drivers.email',
				'transfers.name as transfer_name'
			]);

		// Get pagination parameters
		$perPage = $request->get('length', 15);
		$page = $request->get('start', 0) / $perPage + 1;

		// Get total count
		$total = $query->count();

		// Apply pagination
		$drivers = $query->skip(($page - 1) * $perPage)->take($perPage)->get();

		// Process each driver
		foreach($drivers as $driver) {
			$driver->action = $this->getButton($driver->id, $driver);
		}

		return response()->json([
			'data' => $drivers,
			'recordsTotal' => $total,
			'recordsFiltered' => $total
		]);
	}

    /**
     * TaskController constructor.
     * @param DriverRepository $driverRepository
     */
	public function __construct(DriverRepository $driverRepository)
	{
		$this->middleware('permissions.required');
        $this->driverRepository = $driverRepository;
        $this->middleware('preventBackHistory');
        $this->middleware('auth');
    }

	/**
	 * Display a listing of the resource.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function index()
	{
		$title = 'Index - Driver';
		$drivers = Driver::leftJoin('transfers', 'transfers.id', '=', 'drivers.transfer_id')
			->select([
				'drivers.id',
				'drivers.name',
				'drivers.phone',
				'drivers.email',
				'transfers.name as transfer_name'
			])
			->paginate(15);
		return view('driver.index', compact('drivers', 'title'));
	}

	/**
	 * Show the form for creating a new resource.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function create()
	{
		$title = 'Create - Driver';
		$transfers = Transfer::all()->pluck('name', 'id')->toArray();

		return view('driver.create', compact('title', 'transfers'));
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @return \Illuminate\Http\Response
	 */
	public function store(Request $request)
	{
		$this->validateDriver($request);

		$driver = $this->driverRepository->create($request->except('attach'));
        $this->addFile($request, $driver);

        $data = ['route' => route('driver.index')];

        LaravelFlashSessionHelper::setFlashMessage("Driver $driver->name created", 'success');

        return response()->json($data);
	}

	/**
	 * Display the specified resource.
	 *
	 * @param  int  $id
	 * @return \Illuminate\Http\Response
	 */
	public function show($id, Request $request)
	{
		$title = 'Show - Driver';

		if ($request->ajax()) {
			return URL::to('driver/' . $id);
		}

		$driver = Driver::findOrfail($id);
		if($driver == null){
			return abort(404);
		}

        $files = $this->parseAttach($driver);

		return view('driver.show', compact('title', 'driver', 'files'));
	}

	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param  int  $id
	 * @return \Illuminate\Http\Response
	 */
	public function edit($id, Request $request)
	{
		$title = 'Edit - Driver';
		if ($request->ajax()) {
			return URL::to('driver/' . $id . '/edit');
		}

		$driver = Driver::findOrfail($id);
        $files = $this->parseAttach($driver);
		$transfers = Transfer::all()->pluck('name', 'id')->toArray();
		return view('driver.edit', compact('title', 'driver', 'transfers', 'files'));
	}

	/**
	 * Update the specified resource in storage.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @param  int  $id
	 * @return \Illuminate\Http\Response
	 */
	public function update(Request $request, $id)
	{
		$this->validateDriver($request);

		$this->driverRepository->updateById($id, $request->except('attach'));
        $driver = $this->driverRepository->byId($id);
        $this->addFile($request, $driver);

        LaravelFlashSessionHelper::setFlashMessage("Driver $driver->name edited", 'success');

        $data = ['route' => route('driver.index')];
        return response()->json($data);
	}


	public function deleteMsg($id, Request $request)
	{
//		$msg = Ajaxis::BtDeleting('Warning!!', 'Would you like to remove This?', '/driver/' . $id . '/delete');
		$msg = Ajaxis::BtDeleting(trans('main.Warning').'!!',trans('main.WouldyouliketoremoveThis').'?', '/driver/' . $id . '/delete');

		if ($request->ajax()) {
			return $msg;
		}
	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  int  $id
	 * @return string
     */
	public function destroy($id, DeleteModel $deleteModel)
	{
        $driver = $this->driverRepository->byId($id);
        $message = $deleteModel->check($driver, 'Driver');
        if ($message){
            Session::flash('message', $message);
        } else {
            $this->removeFile($driver);
            $this->driverRepository->deleteById($id);

            LaravelFlashSessionHelper::setFlashMessage("Driver $driver->name deleted", 'success');
        }

		return URL::to(route('driver.index'));
	}

	public function validateDriver($request){

		$this->validate($request, [
			'name'    => 'required',
			'phone'    => 'required',
            'email' => 'required|email'
		]);
	}
}
