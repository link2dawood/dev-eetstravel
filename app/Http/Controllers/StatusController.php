<?php

namespace App\Http\Controllers;

use Amranidev\Ajaxis\Ajaxis;
use App\Comment;
use App\Helper\FileTrait;
use App\Helper\LaravelFlashSessionHelper;
use App\Helper\PermissionHelper;
use App\Status;
use App\StatusType;
use App\TourPackage;
use Illuminate\Http\Request;
use URL;
use View;

class StatusController extends Controller
{
    use FileTrait;

    public $relation_statuses = [
        'tour' => 'Tour',
        'bus' => 'Bus',
        'task' => 'Task',
        'hotel' => 'TourPackage',
        'service_in_tour' => 'TourPackage',
    ];

    public function getButton($id, $status)
    {
        $url = array('show'       => route('status.show', ['id' => $id]),
            'edit'       => route('status.edit', ['id' => $id]),
            'delete_msg' => "/status/{$id}/deleteMsg");

        return DatatablesHelperController::getActionButton($url, false, $status);
    }

    public function data(Request $request)
    {
        $query = Status::leftJoin('status_types', 'status_types.type', '=', 'status.type')
            ->select('status.id', 'status.name', 'status.color', 'status.sort_order', 'status_types.name as status_type');

        // Get pagination parameters
        $perPage = $request->get('length', 15);
        $page = $request->get('start', 0) / $perPage + 1;

        // Get total count
        $total = $query->count();

        // Apply pagination
        $statuses = $query->skip(($page - 1) * $perPage)->take($perPage)->get();

        // Process each status
        foreach($statuses as $status) {
            $status->action = $this->getButton($status->id, $status);
        }

        return response()->json([
            'data' => $statuses,
            'recordsTotal' => $total,
            'recordsFiltered' => $total
        ]);
    }

    /**
     * TaskController constructor.
     */
    public function __construct()
    {
        $this->middleware('preventBackHistory');
        $this->middleware('auth');
        $this->middleware('permissions.required');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $title = 'Index - task';
        $status = Status::leftJoin('status_types', 'status_types.type', '=', 'status.type')
            ->select('status.id', 'status.name', 'status.color', 'status.sort_order', 'status_types.name as status_type')
            ->orderBy('sort_order', 'asc')
            ->paginate(15);
        return view('status.index', compact('status', 'title'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $title = 'Create - status';
        $status_types = StatusType::all();
        return view('status.create', compact('title', 'status_types'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

        $this->validateStatus($request);
        $status = new Status();
        $status->name= $request->get('name');
        $status->type = $request->get('type');
        $status->sort_order = $request->get('sort_order');
        $status->color = $request->get('color', null);
        $status->status = $request->get('status', 0);
        $status->save();

        LaravelFlashSessionHelper::setFlashMessage("Status $status->name created", 'success');

        return $request->get('modal_create') == 1 ?  redirect(URL::to('home')) : redirect(URL::to('status'));
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id, Request $request)
    {
        $title = 'Show - status';

        if ($request->ajax()) {
            return URL::to('task/' . $id);
        }

        $status = Status::findOrfail($id);
        if($status == null){
            return abort(404);
        }
        $status_type = StatusType::where('type', $status->type)->first();

        return view('status.show', compact('title', 'status', 'status_type'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id, Request $request)
    {
        $title = 'Edit - status';
        if ($request->ajax()) {
            return URL::to('status/' . $id . '/edit');
        }

        $status = Status::findOrfail($id);
        $status_types = StatusType::all();
        return view('status.edit', compact('title', 'status', 'status_types'));
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
        $this->validateStatus($request);

        $task = Status::findOrfail($id);
        $task->name = $request->get('name');
        $task->type = $request->get('type');
        $task->sort_order = $request->get('sort_order');
        $task->color = $request->get('color', null);
        $task->status = $request->get('status', 0);
        $task->save();

        LaravelFlashSessionHelper::setFlashMessage("Status $task->name edited", 'success');

        return redirect(URL::to('status'));
    }


    public function DeleteMsg($id, Request $request)
    {
//        $msg = Ajaxis::BtDeleting('Warning!!', 'Would you like to remove This?', '/status/' . $id . '/delete');
        $msg = Ajaxis::BtDeleting(trans('main.Warning').'!!',trans('main.WouldyouliketoremoveThis').'?', '/status/' . $id . '/delete');

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
    public function destroy($id)
    {
        $status = Status::find($id);
        $status_type = $status->type;



        if($status_type){
            $namespace = $this->relation_statuses[$status_type];
            $elements = [];

            if($namespace == 'TourPackage'){
                $elements = TourPackage::query()
                    ->where('status', $id)
                    ->get();
            }else{
                $namespace = 'App\\' . $namespace;

                $elements = $namespace::where('status', $id)->get();
            }

            if($elements->isNotEmpty()){

                LaravelFlashSessionHelper::setFlashMessage("Status $status->name is used", 'warning');
//                $data = [
//                    'message' => 'This status is used by the system!',
//                    'error' => true
//                ];
//                return response($data);
                return URL::to('status');
            }else{
                LaravelFlashSessionHelper::setFlashMessage("Status $status->name deleted", 'success');
                $status->delete();
                Comment::query()->where('reference_type', Comment::$services['status'])->where('reference_id', $id)->delete();
                return URL::to('status');
            }
        }else{
            LaravelFlashSessionHelper::setFlashMessage("Status $status->name deleted", 'success');
            $status->delete();
            Comment::query()->where('reference_type', Comment::$services['status'])->where('reference_id', $id)->delete();
            return URL::to('status');
        }
    }

    public function validateStatus($request){

        $this->validate($request, [
            'name'    => 'required',
            'type' => 'required',
            'sort_order'  => 'required|numeric'
        ]);
    }
}
