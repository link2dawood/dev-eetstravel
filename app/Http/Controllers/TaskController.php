<?php

namespace App\Http\Controllers;

use Amranidev\Ajaxis\Ajaxis;
use App\Comment;
use App\Helper\FileTrait;
use App\Helper\LaravelFlashSessionHelper;
use App\Helper\PermissionHelper;
use App\Notification;
use App\Repository\Contracts\TourRepository;
use App\Repository\Contracts\TaskRepository;
use App\Status;
use App\Task;
use App\Tour;
use App\User;
use Auth;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Pagination\Paginator;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use URL;
use View;

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
        $url = [
            'show'       => route('task.show', ['id' => $id]),
            'edit'       => route('task.edit', ['id' => $id]),
            'delete_msg' => "/task/{$id}/deleteMsg"
        ];

        return DatatablesHelperController::getActionButton($url, false, $task);
    }

    /**
     * Load a task the current user may see; personal tasks of other users answer 404.
     */
    private function findVisibleTask($id)
    {
        $task = Task::findOrFail($id);
        abort_unless($task->isVisibleTo(Auth::user()), 404);

        return $task;
    }

    /** A status id that belongs to tasks */
    private function taskStatusRule()
    {
        return Rule::exists('status', 'id')->where('type', 'task');
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
        $user = Auth::user();

        // Todo Tasks (default page param) - All non-completed, non-aborted tasks
        // Soonest deadline first; tasks without a deadline last
        $todoTasks = Task::with(['status', 'assignedTo', 'tour', 'epic', 'assigned_users'])
            ->visibleTo($user)
            ->whereHas('status', function ($query) {
                $query->where('is_completed', false)
                    ->where('is_aborted', false);
            })
            ->orderByRaw('dead_line IS NULL')
            ->orderBy('dead_line', 'asc')
            ->paginate(10, ['*'], 'todo_page');

        // Completed Tasks (custom page param)
        $completedTasks = Task::with(['status', 'assignedTo', 'tour', 'epic', 'assigned_users'])
            ->visibleTo($user)
            ->whereHas('status', function ($query) {
                $query->where('is_completed', true);
            })
            ->orderBy('created_at', 'desc')
            ->paginate(10, ['*'], 'completed_page');

        // Aborted Tasks (custom page param)
        $abortedTasks = Task::with(['status', 'assignedTo', 'tour', 'epic', 'assigned_users'])
            ->visibleTo($user)
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

    private function normalizeAssignedUsers($assignedUsers): array
    {
        if ($assignedUsers === null || $assignedUsers === '' || $assignedUsers === 'null') {
            return [];
        }

        if (!is_array($assignedUsers)) {
            $assignedUsers = explode(',', $assignedUsers);
        }

        return collect($assignedUsers)
            ->flatMap(function ($value) {
                return is_array($value) ? $value : explode(',', (string) $value);
            })
            ->map(function ($value) {
                return (int) trim((string) $value);
            })
            ->filter(function ($value) {
                return $value > 0;
            })
            ->unique()
            ->values()
            ->all();
    }

    /**
     * The create form posts low/medium/high, the list toggles 0/1; the column is a 0/1 flag.
     */
    private function normalizePriority($value)
    {
        return in_array(strtolower((string) $value), ['1', 'high'], true) ? 1 : 0;
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        $tour_default = $request->get('tour');
        $title = 'Create - task';
        // The dropdowns only need ids and names
        $tours = Tour::orderBy('name')->get(['id', 'name']);
        $users = User::orderBy('name')->get(['id', 'name']);
        $statuses = Status::query()->orderBy('sort_order', 'asc')->where('type', 'task')->get();

        return view('task.create', compact('title', 'tours', 'users', 'tour_default', 'statuses'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $assigned_user = $this->normalizeAssignedUsers($request->get('assigned_user'));

        // Assignees are checked before anything is saved, so a bad id cannot leave a half-created task
        $this->validateTask($request, $assigned_user);

        $task = DB::transaction(function () use ($request, $assigned_user) {
            $task = new Task();
            $task->content = $request->get('content');
            $task->start_time = $request->get('end_date') . ' ' . $request->get('end_time');
            $task->dead_line = $request->get('end_date') . ' ' . $request->get('end_time');
            $task->tour = $request->get('tour') ?: null;
            $task->assign = Auth::user()->id;
            $task->task_type = $request->get('task_type');
            $task->status = $request->get('status');
            $task->priority = $this->normalizePriority($request->get('priority'));
            $task->epic_id = $request->get('epic_id') ?? null; // ✅ Added epic support
            $task->story_points = $request->get('story_points') ?? null; // ✅ Added story points
            $task->estimated_sp = $request->get('estimated_sp') ?? null; // ✅ Added estimated SP
            $task->save();

            if ($assigned_user) {
                $task->assigned_users()->sync($assigned_user);
                foreach (User::whereIn('id', $assigned_user)->get() as $user) {
                    $notification = Notification::query()
                        ->create(['content' => "New task {$task->content}", 'link' => '/task/' . $task->id]);
                    $user->notifications()->attach($notification);
                }
            }

            return $task;
        });

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
            // Only the current user's own notification may be marked as read
            $notification = Auth::user()->notifications()
                ->where('notifications.id', $request->notification_click)->first();
            if ($notification) {
                $notification->click = true;
                $notification->save();
            }
        }

        $title = 'Show - task';
        if ($request->ajax()) {
            return URL::to('task/' . $id);
        }

        $task = $this->findVisibleTask($id);

        $status = Status::where('id', $task->status)->first();
        $task->dead_line = $task->dead_line ? (new Carbon($task->dead_line))->format('Y-m-d H:i') : null;
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

        $this->findVisibleTask($id);

        $task = Task::leftJoin('tours', 'tours.id', '=', 'tasks.tour')
            ->select('tasks.*', 'tours.name as tour_name', 'tours.id as tour_id')
            ->where('tasks.id', $id)->first();

        if (!$task) {
            return abort(404);
        }

        $task['end_date'] = $task->dead_line ? (new Carbon($task->dead_line))->toDateString() : null;
        $task['end_time'] = $task->dead_line ? (new Carbon($task->dead_line))->format('H:i') : null;

        $tours = Tour::orderBy('name')->get(['id', 'name']);
        $users = User::orderBy('name')->get(['id', 'name']);
        $assignedIds = $task->assigned_users->pluck('id')->all();
        foreach ($users as $user) {
            if (in_array($user->id, $assignedIds)) $user->selected = true;
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
        $assigned_user = $this->normalizeAssignedUsers($request->get('assigned_user'));
        $task = $this->findVisibleTask($id);

        if ($request->ajax() && $request->has('newStatus')) {
            $this->validate($request, ['newStatus' => ['required', 'integer', $this->taskStatusRule()]]);
            $task->status = $request->newStatus;
            $task->save();
            return response('done');
        }

        $this->validateTask($request, $assigned_user);
        $task->content = $request->get('content');
        $task->start_time = $request->get('end_date') . ' ' . $request->get('end_time');
        $task->dead_line = $request->get('end_date') . ' ' . $request->get('end_time');
        $task->tour = $request->get('tour') ?: null;
        $task->task_type = $request->get('task_type');
        $task->status = $request->get('status');
        $task->priority = $this->normalizePriority($request->get('priority'));
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
        $this->validate($request, [
            'date' => 'required|date_format:Y-m-d',
            'date_time' => ['required', 'regex:/^([01]\d|2[0-3]):[0-5]\d(:[0-5]\d)?$/'],
        ]);

        $task = $this->findVisibleTask($id);
        $value = Carbon::parse($request->get('date') . ' ' . $request->get('date_time'))->format('Y-m-d H:i:s');
        $task->update([
            'start_time' => $value,
            'dead_line' => $value,
        ]);

        return response()->json(true);
    }

    public function DeleteMsg($id, Request $request, $tab = null)
    {
        $this->findVisibleTask($id);
        $tab_url = ($tab) ? '/' . $tab : '';
        $msg = Ajaxis::BtDeleting(trans('main.Warning') . '!!', trans('main.WouldyouliketoremoveThis') . '?', '/task/' . $id . '/delete' . $tab_url);
        if ($request->ajax()) {
            return $msg;
        }

        return redirect()->route('task.show', ['id' => $id]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id, $tab = null)
    {
        $task = $this->findVisibleTask($id);
        $task->delete();
        Comment::query()->where('reference_type', Comment::$services['task'])->where('reference_id', $id)->delete();

        // AJAX callers show their own toast; a flash here would reappear on the next page load.
        if (!request()->expectsJson()) {
            LaravelFlashSessionHelper::setFlashMessage("Task {$task->content} deleted", 'success');
        }

        // ✅ Return JSON for AJAX/Fetch requests
        return response()->json([
            'success' => true,
            'message' => 'Task deleted successfully.'
        ]);
    }

    /**
     * Rules shared by create and the full edit form.
     */
    public function validateTask(Request $request, array $assignedUsers = [])
    {
        $messages = [
            'end_date.required' => 'The Date field is required.',
            'end_time.required' => 'The Time field is required.',
            'end_time.regex' => 'The Time must be a valid time (HH:MM).',
            'assigned_users_checked.*.exists' => 'One of the selected users does not exist.',
        ];

        // Validate the normalised assignee ids (the form may post a comma list or an array)
        $request->merge(['assigned_users_checked' => $assignedUsers]);

        $this->validate($request, [
            'content' => 'required|string|max:191',
            'description' => 'nullable|string|max:10000',
            'end_date' => 'required|date_format:Y-m-d',
            'end_time' => ['required', 'regex:/^([01]\d|2[0-3]):[0-5]\d(:[0-5]\d)?$/'],
            'task_type' => ['required', Rule::in(array_keys(Task::$taskTypes))],
            'status' => ['required', 'integer', $this->taskStatusRule()],
            'tour' => 'nullable|integer|exists:tours,id',
            'priority' => 'nullable|in:0,1,low,medium,high',
            'story_points' => 'nullable|integer|min:0|max:1000',
            'estimated_sp' => 'nullable|integer|min:0|max:1000',
            'assigned_users_checked.*' => 'integer|exists:users,id',
        ], $messages, ['task_type' => 'task type']);
    }

    public function statusesList(Request $request)
    {
        $task = $this->findVisibleTask($request->taskId);
        $taskStatuses = Status::query()->orderBy('sort_order', 'asc')->where('type', 'task')->get();
        return view('component.task_datatables_status_update', ['taskStatuses' => $taskStatuses, 'task' => $task]);
    }

    /**
     * ✅ NEW: Update individual task fields (used by inline editing)
     */
    public function updateField(Request $request, $id)
    {
        $task = $this->findVisibleTask($id);
        $field = $request->input('field');

        // Same rules as the full form, per inline-editable field
        $rules = [
            'content' => ['required', 'string', 'max:191'],
            'description' => ['nullable', 'string', 'max:10000'],
            'story_points' => ['nullable', 'integer', 'min:0', 'max:1000'],
            'estimated_sp' => ['nullable', 'integer', 'min:0', 'max:1000'],
            'priority' => ['required', 'in:0,1'],
            'sort_order' => ['required', 'integer', 'min:0'],
            'status' => ['required', 'integer', $this->taskStatusRule()],
        ];

        if (!array_key_exists($field, $rules)) {
            return response()->json(['error' => 'Invalid field'], 400);
        }

        $validator = \Validator::make($request->all(), ['value' => $rules[$field]], [], ['value' => str_replace('_', ' ', $field)]);
        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => $validator->errors()->first('value')], 422);
        }

        $value = $request->input('value');
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
