<?php

namespace App\Http\Controllers;

use Amranidev\Ajaxis\Ajaxis;
use App\Comment;
use App\Helper\FileTrait;
use App\Helper\LaravelFlashSessionHelper;
use App\Helper\PermissionHelper;
use App\Notification;
use App\Status;
use App\Task;
use App\Tour;
use App\User;
use Auth;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Pagination\Paginator;
use Illuminate\Pagination\LengthAwarePaginator;
use URL;
use View;

class TaskController extends Controller
{
    use FileTrait;

    private $tourRepository;

    /**
     * AUDIT.md CC9 — TaskRepository was injected but never used at
     * runtime (every reference to $this->repository was commented out).
     * Removed. TourRepository stays because TaskController::create()
     * legitimately calls $this->tourRepository->all().
     */
    public function __construct(\App\Repository\Contracts\TourRepository $tourRepository)
    {
        $this->middleware('permissions.required');
        $this->tourRepository = $tourRepository;
        $this->middleware('preventBackHistory');
        $this->middleware('auth');
    }

   public function getButton($id, $task)
    {
        $url = [
            'show'       => route('task.show', ['id' => $id]),
            'edit'       => route('task.edit', ['id' => $id]),
            'delete_msg' => "/task/{$id}/deleteMsg"
        ];

        return DatatablesHelperController::getActionButton($url, false, $task);
    }

    /**
     * ✅ UPDATED INDEX METHOD - SYNCS ALL TASKS
     * Displays all tasks grouped by status
     * - To-Do (pending tasks)
     * - Completed (completed tasks)
     * - Aborted (aborted tasks)
     */
    public function index(Request $request)
    {
        $title = 'Index - task';

        // ✅ SYNC: Fetch ALL tasks from database with proper relationships
        
        // Todo Tasks (default page param) - All non-completed, non-aborted tasks
        $todoTasks = Task::with(['status', 'assignedTo', 'tour', 'epic', 'assigned_users'])
            ->whereHas('status', function ($query) {
                $query->where('is_completed', false)
                    ->where('is_aborted', false);
            })
            ->orderBy('created_at', 'desc') 
            ->paginate(10, ['*'], 'todo_page');

        // Completed Tasks (custom page param)
        $completedTasks = Task::with(['status', 'assignedTo', 'tour', 'epic', 'assigned_users'])
            ->whereHas('status', function ($query) {
                $query->where('is_completed', true);
            })
            ->orderBy('created_at', 'desc')
            ->paginate(10, ['*'], 'completed_page');

        // Aborted Tasks (custom page param)
        $abortedTasks = Task::with(['status', 'assignedTo', 'tour', 'epic', 'assigned_users'])
            ->whereHas('status', function ($query) {
                $query->where('is_aborted', true);
            })
            ->orderBy('created_at', 'desc')
            ->paginate(10, ['*'], 'aborted_page');

        $task_types = json_encode(array_flip(Task::$taskTypes));
        
        // Get all statuses for the dropdown
        $statuses = Status::query()
            ->orderBy('sort_order', 'asc')
            ->where('type', 'task')
            ->get();

        return view('task.index_monday', compact(
            'title',
            'todoTasks',
            'completedTasks',
            'abortedTasks',
            'task_types',
            'statuses'
        ));
    }

    /**
     * Show the form for creating a new resource.
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
     */
    public function store(Request $request)
    {
        $assigned_user = [];
        if (!is_array($request->assigned_user) && $request->assigned_user != null) {
            $a_users = explode(',', $request->assigned_user);
            if ($a_users[0] != 'null') {
                foreach ($a_users as $item) {
                    $assigned_user[] = (int)$item;
                }
            } else {
                $assigned_user = null;
            }
        } else {
            $assigned_user = $request->get('assigned_user', null);
        }

        $this->validateTask($request);
        $task = new Task();
        $task->content = $request->get('content');
        $task->start_time = $request->get('end_date') . ' ' . $request->get('end_time');
        $task->dead_line = $request->get('end_date') . ' ' . $request->get('end_time');
        $task->tour = $request->get('tour', null);
        $task->assign = Auth::user()->id;
        $task->task_type = $request->get('task_type');
        $task->status = $request->get('status');
        $task->priority = $request->get('priority') ?? 0;
        $task->epic_id = $request->get('epic_id') ?? null; // ✅ Added epic support
        $task->story_points = $request->get('story_points') ?? null; // ✅ Added story points
        $task->estimated_sp = $request->get('estimated_sp') ?? null; // ✅ Added estimated SP
        $task->save();

        if ($assigned_user) {
            $assigned_user = array_unique($assigned_user);
            $task->assigned_users()->sync($assigned_user);
            foreach ($assigned_user as $user_id) {
                $notification = Notification::query()
                    ->create(['content' => "New task {$task->content}", 'link' => '/task/' . $task->id]);
                $user = User::findOrFail($user_id);
                $user->notifications()->attach($notification);
            }
        }

        $this->addFile($request, $task);
        LaravelFlashSessionHelper::setFlashMessage("Task $task->content created", 'success');

        if ($request->get('modal_create') == 1) {
            return redirect(route('dashboard_main'));
        } 
        
        if ($request->get('redirect_tour') != null) {
            $data = ['route' => route('tour.show', ['tour' => $request->get('redirect_tour')])];
            return response()->json($data);
        } 

        // ✅ Always redirect back to task list
        return redirect(route('task.index'));
    }

    /**
     * Display the specified resource.
     */
    public function show($id, Request $request)
    {
        if ($request->notification_click) {
            $notification = Notification::find($request->notification_click);
            $notification->click = true;
            $notification->save();
        }

        $title = 'Show - task';
        if ($request->ajax()) {
            return URL::to('task/' . $id);
        }

        $task = Task::query()->find($id);
        if ($task == null) {
            return abort(404);
        }

        $status = Status::where('id', $task->status)->first();
        $task->dead_line = (new Carbon($task->dead_line))->format('Y-m-d H:i');
        $files = $this->parseAttach($task);

        return view('task.show', compact('title', 'task', 'status', 'files'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id, Request $request)
    {
        $title = 'Edit - task';
        if ($request->ajax()) {
            return route('task.edit', ['id' => $id]);
        }

        $task = Task::leftJoin('tours', 'tours.id', '=', 'tasks.tour')
            ->select('tasks.*', 'tours.name as tour_name', 'tours.id as tour_id')
            ->where('tasks.id', $id)->first();

        if (!$task) {
            return abort(404);
        }

        $task['end_date'] = (new Carbon($task->dead_line ?? ""))->toDateString();
        $task['end_time'] = (new Carbon($task->dead_line ?? ""))->toTimeString();

        $tours = Tour::orderBy('name')->get();
        $users = User::orderBy('name')->get();
        foreach ($users as $user) {
            if (!empty($task->assigned_users)) {
                foreach ($task->assigned_users as $t_user) {
                    if ($user->id == $t_user->id) $user->selected = true;
                }
            }
        }
        $statuses = Status::query()->orderBy('sort_order', 'asc')->where('type', 'task')->get();
        $calendar_edit = $request->get('calendar_edit') ?? null;
        $files = $this->parseAttach($task);

        return view('task.edit', compact('title', 'task', 'tours', 'users', 'statuses', 'calendar_edit', 'files'));
    }

    /**
     * Update the specified resource.
     */
    public function update(Request $request, $id)
    {
        $assigned_user = [];

        if (!is_array($request->assigned_user) && $request->assigned_user != null) {
            $a_users = explode(',', $request->assigned_user);
            if ($a_users[0] != 'null') {
                foreach ($a_users as $item) {
                    $assigned_user[] = $item;
                }
            } else {
                $assigned_user = null;
            }
        } else {
            $assigned_user = $request->get('assigned_user', null);
        }

        if ($request->ajax() && $request->newStatus) {
            $task = Task::findOrFail($id);
            $task->status = $request->newStatus;
            $task->save();
            return response('done');
        }

        $this->validateTask($request);
        $task = Task::findOrFail($id);
        $task->content = $request->get('content');
        $task->start_time = $request->get('end_date') . ' ' . $request->get('end_time');
        $task->dead_line = $request->get('end_date') . ' ' . $request->get('end_time');
        $task->tour = $request->get('tour', null);
        $task->task_type = $request->get('task_type');
        $task->status = $request->get('status');
        $task->priority = $request->get('priority') ?? 0;
        $task->epic_id = $request->get('epic_id') ?? null; // ✅ Added epic support
        $task->story_points = $request->get('story_points') ?? null; // ✅ Added story points
        $task->estimated_sp = $request->get('estimated_sp') ?? null; // ✅ Added estimated SP
        $task->save();
        $task->assigned_users()->sync($assigned_user);
        $this->addFile($request, $task);

        if ($request->get('calendar_edit') == 1) {
            $data = ['route' => url('home')];
        } else if ($request->get('tab')) {
            $data = ['route' => url('profile?' . $request->get('tab'))];
        } else {
            // ✅ Default redirect to task index (the list)
            $data = ['route' => url('task')];
        }

        LaravelFlashSessionHelper::setFlashMessage("Task $task->content edited", 'success');
        return response()->json($data);
    }

    public function updateCalendarTask(Request $request, $id)
    {
        $date = $request->get('date');
        $date_time = $request->get('date_time');

        Task::find($id)->update([
            'start_time' => $date . ' ' . $date_time,
            'dead_line' => $date . ' ' . $date_time,
        ]);

        return response()->json(true);
    }

    public function DeleteMsg($id, Request $request, $tab = null)
    {
        $tab_url = ($tab) ? '/' . $tab : '';
        $msg = Ajaxis::BtDeleting(trans('main.Warning') . '!!', trans('main.WouldyouliketoremoveThis') . '?', '/task/' . $id . '/delete' . $tab_url);
        if ($request->ajax()) {
            return $msg;
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id, $tab = null)
    {
        $task = Task::findOrFail($id);
        $task->delete();
        Comment::query()->where('reference_type', Comment::$services['task'])->where('reference_id', $id)->delete();

        LaravelFlashSessionHelper::setFlashMessage("Task {$task->content} deleted", 'success');

        // ✅ Return JSON for AJAX/Fetch requests
        return response()->json([
            'success' => true,
            'message' => 'Task deleted successfully.'
        ]);
    }

    public function validateTask(Request $request)
    {
        $messages = [
            'end_date.required' => 'The Date field is required.',
            'end_time.required' => 'The Time field is required.'
        ];

        $this->validate($request, [
            'content' => 'required',
            'end_date' => 'required',
            'end_time' => 'required',
            'task_type' => 'required',
            'status' => 'required'
        ], $messages);
    }

    public function statusesList(Request $request)
    {
        $task = Task::find($request->taskId);
        $taskStatuses = Status::query()->orderBy('sort_order', 'asc')->where('type', 'task')->get();
        return view('component.task_datatables_status_update', ['taskStatuses' => $taskStatuses, 'task' => $task]);
    }

    /**
     * ✅ NEW: Update individual task fields (used by inline editing)
     */
    public function updateField(Request $request, $id)
    {
        $task = Task::findOrFail($id);
        $field = $request->input('field');
        $value = $request->input('value');

        $allowedFields = ['content', 'description', 'story_points', 'estimated_sp', 'priority', 'sort_order', 'status'];

        if (!in_array($field, $allowedFields)) {
            return response()->json(['error' => 'Invalid field'], 400);
        }

        $task->$field = $value;
        $task->save();

        // Return status details if status was updated
        if ($field === 'status') {
            $status = Status::find($value);
            return response()->json([
                'success' => true,
                'message' => 'Field updated successfully',
                'status' => [
                    'name' => $status->name ?? 'Unknown',
                    'color' => $status->color ?? '#6c757d'
                ]
            ]);
        }

        return response()->json(['success' => true, 'message' => 'Field updated successfully']);
    }
}