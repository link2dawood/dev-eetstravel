<?php

namespace App\Http\Controllers;

use Amranidev\Ajaxis\Ajaxis;
use App\Comment;
use App\Helper\FileTrait;
use App\Helper\LaravelFlashSessionHelper;
use App\Helper\PermissionHelper;
use App\Notification;
use App\Repository\Contracts\TourRepository;
use App\Status;
use App\Task;
use App\Tour;
use App\User;
use Auth;
use Carbon\Carbon;
use Illuminate\Http\Request;
use URL;
use View;
use Yajra\Datatables\Datatables;
use App\Repository\Contracts\TaskRepository;

class TaskController extends Controller
{
    use FileTrait;

    private $repository;

    private $tourRepository;

	/**
	 * TaskController constructor.
	 */
	public function __construct(TaskRepository $repository, TourRepository $tourRepository)
	{
		$this->middleware('permissions.required');
		$this->repository = $repository;
		$this->tourRepository = $tourRepository;
        $this->middleware('preventBackHistory');
        $this->middleware('auth');
	}


    public function getButton($id, $task)
    {
        $url = array('show'       => route('task.show', ['task' => $id]),
                     'edit'       => route('task.edit', ['id' => $id]),
                     'delete_msg' => "/task/{$id}/deleteMsg");

        return DatatablesHelperController::getActionButton($url, false, $task);
    }

    public function data(Request $request)
    {
        if (Auth::user()->hasRole('admin')){
            $tasks = $this->repository->all();
        } else {
            $tasks = $this->repository->allForAssigned(Auth::user()->id);
        }

        return Datatables::of($tasks)
            ->addColumn('action', function ($task) {
                return $this->getButton($task->id, $task);
            })
            ->addColumn('assign', function ($task) {
                return $task->showAssignedUsers();
            })
            ->addColumn('start_time', function ($task) {
                return $task->start_time = (new Carbon($task->start_time))->format('Y-m-d H:i');
            })
            ->addColumn('tour', function ($task) {
                $tour = $task->tourModel;

                if(empty($tour)){
                    return '';
                }
                $tour_id = $tour->id;
                $tour_name = $tour->name;

                $link = route('tour.show', ['tour' => $tour_id]);

                return "<span data-tour-link='$link' style='color: blue; text-decoration: underline; cursor: pointer'>$tour_name</span>";
            })
	        ->addColumn('priority', function ($task) {
		        return $task->priority ? 'Yes' : 'No';
	        })
            ->addColumn('dead_line', function ($task) {
                return $task->dead_line = (new Carbon($task->dead_line))->format('Y-m-d H:i');
            })
            ->addColumn('status_name', function ($task){
                // return View::make('component.tour_status_for_datatable', ['status' => $task->status_name, 'color' => $task->status_color]);
                $link = route('task.update', ['id' => $task->id]);
                // $taskStatuses = Status::where('type', 'task')->get();
                // return view('component.task_datatables_status_update', ['taskStatuses' => $taskStatuses, 'task' => $task]);
                $taskStatus = Status::query()
                    ->where('type', 'task')
                    ->where('id', $task->status)
                    ->select('name')
                    ->first();
                $statuts_name = $taskStatus ? $taskStatus->name : ' ';

                return "<span class='task-data' data-link-update='{$link}' data-task-id='{$task->id}'>{$statuts_name}</span>";
            })
            ->addColumn('task_type', function ($task) {
                return Task::$taskTypes[$task->task_type];
            })
            ->rawColumns(['action', 'status_name', 'tour'])
            ->make(true);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $title = 'Index - task';
        $tasks = Task::with(['status', 'assignedTo', 'tour'])->paginate(10);


        $task_types = json_encode(array_flip(Task::$taskTypes));
        $statuses = Status::query()->orderBy('sort_order', 'asc')->where('type', 'task')->select('id', 'name')->get();
        return view('task.index', compact('tasks', 'title', 'task_types', 'statuses'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        $tour_default = $request->get('tour');

        $title = 'Create - task';
        $tours = $this->tourRepository->all();
        $users = User::orderBy('name')->get();
        $statuses = Status::query()->orderBy('sort_order', 'asc')->where('type', 'task')->get();

        return view('task.create', compact('title', 'tours', 'users', 'tour_default', 'statuses'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $assigned_user = [];
        if(!is_array($request->assigned_user) && $request->assigned_user != null){
            $a_users = explode(',', $request->assigned_user);
            if($a_users[0] != 'null'){
                foreach ($a_users as $item){
                    $assigned_user[] = (int) $item;
                }
            }else{
                $assigned_user = null;
            }
        }else{
            $assigned_user = $request->get('assigned_user', null);
        }

        $tour = Tour::all()->pluck('name', 'id')->toArray();
        $user = User::all()->pluck('name', 'id')->toArray();
        $this->validateTask($request);
        $task = new Task();
        $task->content = $request->get('content');
        $task->start_time = $request->get('end_date') . ' ' . $request->get('end_time');
        $task->dead_line = $request->get('end_date') . ' ' . $request->get('end_time');
        $task->tour = $request->get('tour', null);
        // $task->assign = $user[$request->get('assign')];
        $task->assign = Auth::user()->id;
        $task->task_type = $request->get('task_type');
        $task->status = $request->get('status');
	    $task->priority = $request->get('priority') ?? 0;

	    $tour = Tour::find($request->get('tour'));
	    if($tour) {
//		    $assigned_user = array_merge($tour->users->pluck('id')->toArray(), (array)$assigned_user);
	    }

	    $task->save();
        if ($assigned_user){
            $assigned_user = array_unique($assigned_user);
            $task->assigned_users()->sync($assigned_user);
            foreach ($assigned_user as $user_id) {
                $notification = Notification::query()
                    ->create(['content' => "New task {$task->content}", 'link' => '/task/'.$task->id]);
                $user = User::findOrFail($user_id);
                $user->notifications()->attach($notification);
            }
        }


	    $this->addFile($request, $task);
        LaravelFlashSessionHelper::setFlashMessage("Task $task->content created", 'success');

	    if($request->get('modal_create') == 1){
		    return redirect(route('dashboard_main'));
        }elseif($request->get('redirect_tour') != null){
		    $data = ['route' => route('tour.show', ['tour' => $request->get('redirect_tour')])];
        }else{
		    $data = ['route' => route('task.index')];
        }

	    // return  json_encode($request);
	    return response()->json($data);
    }

    /**
     * Display the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id, Request $request)
    {
	    if ($request->notification_click){
		    $notification = Notification::find($request->notification_click);
		    $notification->click = true;
		    $notification->save();
	    }
        $title = 'Show - task';

        if ($request->ajax()) {
            return URL::to('task/' . $id);
        }

        // $task = Task::leftJoin('tours', 'tours.id', '=', 'tasks.tour')
        //     ->select('tasks.*', 'tours.name as tour_name')
        //     ->where('tasks.id', $id)->first();
        $task = Task::query()->find($id);

        if($task == null){
            return abort(404);
        }
        $status = Status::where('id', $task->status)->first();

        $task->dead_line = (new Carbon($task->dead_line))->format('Y-m-d H:i');
	    $files = $this->parseAttach($task);


        return view('task.show', compact('title', 'task', 'status', 'files'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id, Request $request)
    {
        $title = 'Edit - task';
        if ($request->ajax()) {
            return URL::to('task/' . $id . '/edit');
        }

        $task = Task::leftJoin('tours', 'tours.id', '=', 'tasks.tour')
            ->select('tasks.*', 'tours.name as tour_name', 'tours.id as tour_id')
            ->where('tasks.id', $id)->first();


        $task['end_date'] = (new Carbon($task->dead_line??""))->toDateString();
        $task['end_time'] = (new Carbon($task->dead_line??""))->toTimeString();

        $tours = Tour::orderBy('name')->get();
        $users = User::orderBy('name')->get();
        foreach ($users as $user) {
			if(!empty($task->assigned_users)){
                foreach ($task->assigned_users as $t_user) {
                    if ($user->id == $t_user->id) $user->selected = true;
                }
			}
        }
        $statuses = Status::query()->orderBy('sort_order', 'asc')->where('type', 'task')->get();

        $calendar_edit = null;
		
	    $files = $this->parseAttach($task);
        if($request->has('calendar_edit')){
            $calendar_edit = $request->get('calendar_edit');
        }
        return view('task.edit', compact('title', 'task', 'tours', 'users', 'statuses', 'calendar_edit', 'files'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $assigned_user = [];

        if(!is_array($request->assigned_user) && $request->assigned_user != null){
            $a_users = explode(',', $request->assigned_user);
            if($a_users[0] != 'null'){
                foreach ($a_users as $item){
                    $assigned_user[] = $item;
                }
            }else{
                $assigned_user = null;
            }
        }else{
            $assigned_user = $request->get('assigned_user', null);
        }

        if ($request->ajax() && $request->newStatus){
            $task = Task::findOrfail($id);
            $task->status = $request->newStatus;
            $task->save();
            return response('done');
        }

        $this->validateTask($request);
        $tour = Tour::all()->pluck('name', 'id')->toArray();
        $user = User::all()->pluck('name', 'id')->toArray();

        $task = Task::findOrfail($id);
        $task->content = $request->get('content');
        $task->start_time = $request->get('end_date') . ' ' . $request->get('end_time');
        $task->dead_line = $request->get('end_date') . ' ' . $request->get('end_time');
        $task->tour = $request->get('tour', null);
        // $task->assign = $user[$request->get('assign')];
        $task->task_type = $request->get('task_type');
        $task->status = $request->get('status');
        $task->priority = $request->get('priority') ?? 0;
        $task->save();
        $task->assigned_users()->sync($assigned_user);
	    $this->addFile($request, $task);
        if($request->get('calendar_edit') == 1){
	        $data = ['route' => url('home')];
        }else if($request->get('tab')){
	        $data = ['route' => url('profile?'.$request->get('tab'))];
        }else{
            $data = ['route' => url('task')];
        }
        LaravelFlashSessionHelper::setFlashMessage("Task $task->content edited", 'success');

	    return response()->json($data);
    }

    public function updateCalendarTask(Request $request, $id){
        $date = $request->get('date');
        $date_time = $request->get('date_time');

        Task::find($id)->update([
                'start_time' => $date . ' ' . $date_time,
                'dead_line' => $date . ' ' . $date_time,
            ]);

        return response()->json(true);
    }

    /**
     * Delete confirmation message by Ajaxis.
     *
     * @link      https://github.com/amranidev/ajaxis
     * @param    \Illuminate\Http\Request $request
     * @return  String
     */
    public function DeleteMsg($id, Request $request, $tab = null )
    {
        $tab_url = ($tab) ? '/'. $tab : '';

//        $msg = Ajaxis::BtDeleting('Warning!!', 'Would you like to remove This?', '/task/' . $id . '/delete'. $tab_url);
        $msg = Ajaxis::BtDeleting(trans('main.Warning').'!!',trans('main.WouldyouliketoremoveThis').'?', '/task/' . $id . '/delete'. $tab_url);

        if ($request->ajax()) {
            return $msg;
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id, $tab = null)
    {
        $task = Task::findOrfail($id);

        $task->delete();
        Comment::query()->where('reference_type', Comment::$services['task'])->where('reference_id', $id)->delete();
        LaravelFlashSessionHelper::setFlashMessage("Task {$task->content} deleted", 'success');
        if($tab) {
            if (!is_numeric($tab)) {
                return URL::to('profile?tab=history-tasks-tab');
            } else {
                return URL::to('tour/' . $tab);
            }
        }



        return URL::to('task');

    }

    public function validateTask(Request $request)
    {
        $messages = [
            'end_date.required'    => 'The Date field is required.',
            'end_time.required'    => 'The Time field is required.'
        ];


        $this->validate($request, [
            'content'    => 'required',
            'end_date' => 'required',
            'end_time' => 'required',
            'task_type'  => 'required',
            'status'     => 'required'
        ], $messages);
    }

    public function statusesList(Request $request)
    {
        $task = Task::find($request->taskId);
        $taskStatuses = Status::query()->orderBy('sort_order', 'asc')->where('type', 'task')->get();
        return view('component.task_datatables_status_update', ['taskStatuses' => $taskStatuses, 'task' => $task]);
    }
}
