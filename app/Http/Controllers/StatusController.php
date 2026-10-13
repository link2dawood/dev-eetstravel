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
use Illuminate\Support\Facades\DB;
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
    public function index(Request $request)
    {
        $title = 'Index - task';
        $search = trim((string) $request->input('search'));
        $status = Status::leftJoin('status_types', 'status_types.type', '=', 'status.type')
            ->select('status.id', 'status.name', 'status.color', 'status.sort_order', 'status_types.name as status_type')
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('status.name', 'like', "%{$search}%")->orWhere('status_types.name', 'like', "%{$search}%");
                });
            })
            ->orderBy('status_types.name', 'asc')
            ->orderBy('status.sort_order', 'asc')
            ->paginate(15)
            ->withQueryString();
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
        $status = Status::findOrFail($id);

        // where each status type is stored (bus statuses live on bus days, offer statuses on hotel offers)
        $usage = [
            'tour' => [['tours', 'status']],
            'task' => [['tasks', 'status']],
            'hotel' => [['tour_packages', 'status']],
            'service_in_tour' => [['tour_packages', 'status']],
            'bus' => [['bus_days', 'status_id'], ['tour_packages', 'status']],
            'offer' => [['hotel_offers', 'tms_status']],
        ];

        $used = false;
        foreach ($usage[$status->type] ?? [] as [$table, $column]) {
            if (DB::table($table)->where($column, $id)->exists()) {
                $used = true;
                break;
            }
        }

        if ($used) {
            $message = "Status {$status->name} is in use and cannot be deleted";
            if (request()->ajax()) {
                return response()->json(['error' => true, 'message' => $message]);
            }
            LaravelFlashSessionHelper::setFlashMessage($message, 'warning');
            return redirect('status');
        }

        LaravelFlashSessionHelper::setFlashMessage("Status $status->name deleted", 'success');
        $status->delete();
        Comment::query()->where('reference_type', Comment::$services['status'])->where('reference_id', $id)->delete();

        return request()->ajax() ? URL::to('status') : redirect('status');
    }

    public function validateStatus($request){

        $this->validate($request, [
            'name'    => 'required',
            'type' => 'required',
            'sort_order'  => 'required|numeric'
        ]);
    }
}
