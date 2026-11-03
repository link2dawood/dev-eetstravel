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
use DB;
use App\Services\ValidationService;
use Illuminate\Validation\Rule;

class DriverController extends Controller
{

    use FileTrait;

    /**
     * @var DriverRepository
     */
    private $driverRepository;

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
    public function index(Request $request)
    {
        $title = 'Index - Drivers';
        $search = trim((string) $request->input('search'));
        $drivers = Driver::query()
            ->leftJoin('transfers', 'transfers.id', '=', 'drivers.transfer_id')
            ->select([
                'drivers.*',
                'transfers.name as transfer_name'
            ])
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('drivers.name', 'like', "%{$search}%")
                        ->orWhere('drivers.phone', 'like', "%{$search}%")
                        ->orWhere('drivers.email', 'like', "%{$search}%")
                        ->orWhere('transfers.name', 'like', "%{$search}%");
                });
            })
            ->orderBy('drivers.name', 'asc')
            ->paginate(15)
            ->withQueryString();
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
        $this->validate($request, $this->validationRules());

        $driver = DB::transaction(function () use ($request) {
            $driver = $this->driverRepository->create($this->driverData($request));
            $this->addFile($request, $driver);
            return $driver;
        });

        $data = ['route' => route('driver.index')];
        LaravelFlashSessionHelper::setFlashMessage("Driver $driver->name created", 'success');
        return $request->expectsJson() ? response()->json($data) : redirect()->route('driver.index');
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

        $driver = Driver::query()->where('id', $id)->first();

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

        $driver = Driver::query()->where('id', $id)->first();

        if($driver == null){
            return abort(404);
        }

        $transfers = Transfer::all()->pluck('name', 'id')->toArray();

        $files = $this->parseAttach($driver);

        return view('driver.edit', compact('title', 'driver', 'files', 'transfers'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    /**
     * Phone and email are optional in the form but NOT NULL in the table:
     * an empty field (converted to null) used to fail with a server error.
     */
    private function driverData(Request $request)
    {
        $data = $request->only('name', 'phone', 'email', 'transfer_id');
        $data['phone'] = $data['phone'] ?? '';
        $data['email'] = $data['email'] ?? '';

        return $data;
    }

    public function validationRules($id = null)
    {
        return [
            'name' => ValidationService::nameRules(),
            'phone' => ValidationService::phoneRules(false),
            'email' => array_merge(ValidationService::emailRules(false), [Rule::unique('drivers', 'email')->ignore($id)]),
            'transfer_id' => ['nullable', 'integer', Rule::exists('transfers', 'id')->whereNull('deleted_at')],
            'attach' => 'nullable|array',
            'attach.*' => 'file|max:10240|mimes:jpeg,jpg,png,gif,pdf,doc,docx,xls,xlsx',
            'attach_bus' => 'prohibited',
        ];
    }

    public function update(Request $request, $id)
    {
        $this->driverRepository->byId($id);
        $this->validate($request, $this->validationRules($id));

        $driver = DB::transaction(function () use ($request, $id) {
            $this->driverRepository->updateById($id, $this->driverData($request));
            $driver = $this->driverRepository->byId($id);
            $this->addFile($request, $driver);
            return $driver;
        });

        LaravelFlashSessionHelper::setFlashMessage("Driver $driver->name edited", 'success');
        $data = ['route' => route('driver.index')];
        return $request->expectsJson() ? response()->json($data) : redirect()->route('driver.index');
    }

    public function deleteMsg($id, Request $request)
    {
        $this->driverRepository->byId($id);
        $msg = Ajaxis::BtDeleting(trans('main.Warning').'!!',trans('main.WouldyouliketoremoveThis').'?', '/driver/' . $id . '/delete');

        if ($request->ajax()) {
            return $msg;
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id, DeleteModel $deleteModel)
    {
        $message = DB::transaction(function () use ($id, $deleteModel) {
            $driver = Driver::query()->lockForUpdate()->findOrFail($id);
            $message = $deleteModel->check($driver, 'Driver');
            if ($message) return $message;
            // Drivers have no comment reference type. Do not delete unrelated comments.
            $this->removeFile($driver);
            $this->driverRepository->deleteById($id);
            LaravelFlashSessionHelper::setFlashMessage("Driver $driver->name deleted", 'success');
            return null;
        });
        if ($message){
            Session::flash('message', $message);
            if (request()->expectsJson()) return response()->json(['success' => false, 'message' => $message], 422);
        }

        return request()->expectsJson()
            ? response()->json(['route' => route('driver.index')])
            : redirect()->route('driver.index');
    }

    public function getButton($id, $driver)
	{
		$url = array('show'       => route('driver.show', ['driver' => $id]),
		             'edit'       => route('driver.edit', ['driver' => $id]),
		             'delete_msg' => "/driver/{$id}/delete_msg");

		return DatatablesHelperController::getActionButton($url, false, $driver);
	}

}
