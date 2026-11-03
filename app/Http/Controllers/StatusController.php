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
        $url = array('show'       => route('status.show', ['status' => $id]),
            'edit'       => route('status.edit', ['status' => $id]),
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
        $status->is_completed = $request->type === 'task' && $request->boolean('is_completed');
        $status->is_aborted = $request->type === 'task' && $request->boolean('is_aborted');
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

        $status = Status::findOrFail($id);
        if ($request->ajax()) {
            return route('status.show', ['status' => $id]);
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
        Status::findOrFail($id);
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
        DB::transaction(function () use ($request, $id) {
            $status = Status::query()->lockForUpdate()->findOrFail($id);
            $this->validateStatus($request, $status);
            $status->name = $request->get('name');
            $status->type = $request->get('type');
            $status->sort_order = $request->get('sort_order');
            $status->color = $request->get('color', null);
            $status->status = $request->get('status', $status->status);
            $status->is_completed = $request->type === 'task' && $request->boolean('is_completed');
            $status->is_aborted = $request->type === 'task' && $request->boolean('is_aborted');
            $status->save();
            LaravelFlashSessionHelper::setFlashMessage("Status $status->name edited", 'success');
        });

        return redirect(URL::to('status'));
    }


    public function DeleteMsg($id, Request $request)
    {
        Status::findOrFail($id);
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
        return DB::transaction(function () use ($id) {
            $status = Status::query()->lockForUpdate()->findOrFail($id);
            if ($status->isSystem() || $status->isInUse()) {
                $message = "Status {$status->name} is protected or in use and cannot be deleted";
                if (request()->expectsJson()) {
                    return response()->json(['success' => false, 'message' => $message], 422);
                }
                LaravelFlashSessionHelper::setFlashMessage($message, 'warning');
                return redirect('status');
            }
            $status->delete();
            Comment::query()->where('reference_type', Comment::$services['status'])->where('reference_id', $id)->delete();
            LaravelFlashSessionHelper::setFlashMessage("Status $status->name deleted", 'success');
            return request()->expectsJson() ? response()->json(['route' => route('status.index')]) : redirect('status');
        });
    }

    public function validateStatus($request, Status $status = null){
        if ($status) {
            // Older callers omit these fields; only explicit form values change behavior.
            foreach (['is_completed', 'is_aborted'] as $flag) {
                if (!$request->exists($flag)) $request->merge([$flag => $request->type === 'task' ? (int) $status->$flag : 0]);
            }
        }
        $this->validate($request, [
            'name' => array_merge(\App\Services\ValidationService::nameRules(), [\Illuminate\Validation\Rule::unique('status', 'name')->where('type', $request->input('type'))->ignore($status ? $status->id : null)]),
            'type' => 'required|string|exists:status_types,type',
            'sort_order' => 'required|integer|min:0|max:2147483647',
            'color' => ['nullable', 'string', 'regex:/^#(?:[0-9a-fA-F]{3}|[0-9a-fA-F]{6}|[0-9a-fA-F]{8})$/'],
            'status' => 'sometimes|boolean',
            'is_completed' => 'sometimes|boolean',
            'is_aborted' => 'sometimes|boolean',
        ]);
        $errors = [];
        if ($request->boolean('is_completed') && $request->boolean('is_aborted')) $errors['is_aborted'] = 'A task status cannot be both completed and aborted.';
        if ($request->type !== 'task' && ($request->boolean('is_completed') || $request->boolean('is_aborted'))) $errors['type'] = 'Completion and abortion apply only to task statuses.';
        if ($status) {
            if ($request->type !== $status->type && ($status->isSystem() || $status->isInUse())) $errors['type'] = 'A protected or used status cannot change type.';
            if ($request->name !== $status->name && ($status->isSystem() || ($status->type === 'offer' && $status->isInUse()))) $errors['name'] = 'A system status or used offer status cannot be renamed.';
            if ($status->isSystem()) {
                foreach (['is_completed', 'is_aborted'] as $flag) {
                    if ($request->boolean($flag) !== (bool) $status->$flag) $errors[$flag] = 'System status behavior cannot be changed.';
                }
            }
        }
        if ($errors) throw \Illuminate\Validation\ValidationException::withMessages($errors);
    }
}
