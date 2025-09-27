<?php
/**
 * Created by PhpStorm.
 * User: alexandr
 * Date: 22.08.17
 * Time: 13:52
 */

namespace App\Http\Controllers;


use App\Repository\Contracts\TaskRepository;
use App\Status;
use App\Task;
use App\Tour;
use App\TourDay;
use App\User;
use Auth;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Activitylog\Models\Activity;

class ProfileController extends Controller
{

    public $taskRepository;

    /**
     * ProfileController constructor.
     * @param $taskRepository
     */
    public function __construct(TaskRepository $taskRepository)
    {
        $this->middleware('permissions.required');
        $this->taskRepository = $taskRepository;
    }


    public function getButtonForTasks($id, $task)
    {
        $url = array('show'       => route('task.show', ['task' => $id, 'tab' => 'history-tasks-tab']),
            'edit'       => route('task.edit', ['task' => $id,'tab' => 'history-tasks-tab']),
            'delete_msg' => "/task/{$id}/deleteMsg/history-tasks-tab");

        return DatatablesHelperController::getActionButton($url, false, $task);
    }

    public function getButtonForNotifications($id, $link, $notification)
    {
        $url = array(
            'show' => $link, //url($link)
            'delete_msg' => '/notifications/'.$id.'/deleteMsg'
            );

        return DatatablesHelperController::getActionButtonForProfileNotification($url, $notification);
    }

    public function getButtonForTours($id, $tour)
    {
        $url = array('show'       => route('tour.show', ['tour' => $id, 'tab' => 'history-tours-tab']),
            'edit'       => route('tour.edit', ['tour' => $id, 'tab' => 'history-tours-tab']),
            'delete_msg' => "/tour/{$id}/deleteMsg/history-tours-tab",
            'id' => $id);

        return DatatablesHelperController::getActionButton($url, false, $tour);
    }


    public function show(){
        $userId = Auth::user()->id;

        $user = User::findOrFail($userId);

        if(!$user)
            return abort(404);

        $activities = Activity::where('causer_id', $userId)->orderBy('created_at', 'desc')->paginate(10);

        // Get tasks data directly
        $allTasks = $user->getTasksAttachedToUser();
        $tasks = array_slice($allTasks, 0, 50);
        foreach($tasks as $task) {
            $task->start_time = (new Carbon($task->start_time))->format('Y-m-d H:i');
            $task->dead_line = (new Carbon($task->dead_line))->format('Y-m-d H:i');
            $task->priority_text = $task->priority ? 'Yes' : 'No';
            $task->action_buttons = $this->getButtonForTasks($task->id, $task);

            // Get tour info
            $tour = $task->tourModel;
            if(!empty($tour)){
                $link = route('tour.show', ['tour' => $tour->id]);
                $task->tour_link = "<span data-tour-link='$link' style='color: blue; text-decoration: underline; cursor: pointer'>{$tour->name}</span>";
            } else {
                $task->tour_link = '';
            }

            // Get status
            $link = route('task.update', ['task' => $task->id]);
            $taskStatus = Status::query()->where('type', 'task')->where('id', $task->status)->select('name')->first();
            $task->status_display = "<span class='task-data' data-link-update='{$link}' data-task-id='{$task->id}'>{$taskStatus->name}</span>";
        }

        // Get tours data directly
        $allTours = $user->getToursAttachedToUser();
        $tours = array_slice($allTours, 0, 50);
        foreach($tours as $tour) {
            $tour->action_buttons = $this->getButtonForTours($tour->id, $tour);
            $tour->status_display = \View::make('component.tour_status_for_datatable', ['status' => $tour->getStatusName(), 'color' => $tour->getStatusColor()])->render();

            // Add package button
            $tourDay = TourDay::where('tour', $tour->id)->first();
            if($tourDay){
                $link = route( 'tour_package.store' );
                $tour->package_button = "<button data-link='$link' class='btn btn-default tour_package_add' data-tourDayId='{$tourDay->id}' data-tour_id='{$tour->id}'" .
                       " data-departure_date='{$tour->departure_date}' data-retirement_date='{$tour->retirement_date}'>+</button>";
            } else {
                $tour->package_button = '';
            }
        }

        // Get notifications data directly
        $notifications = $user->notifications()->latest()->take(50)->get();
        foreach($notifications as $notification) {
            $notification->action_buttons = $this->getButtonForNotifications($notification->id, $notification->link, $notification);
        }

        return view('profile.show', [
            'user' => $user,
            'activities' => $activities,
            'tasks' => $tasks,
            'tours' => $tours,
            'notifications' => $notifications
        ]);
    }

    public function dataTasks(Request $request){
        $user = Auth::user();
        $allTasks = $user->getTasksAttachedToUser();

        // Apply pagination
        $perPage = $request->get('length', 15);
        $page = $request->get('start', 0) / $perPage + 1;
        $total = count($allTasks);
        $tasks = array_slice($allTasks, ($page - 1) * $perPage, $perPage);

        // Process each task
        foreach($tasks as $task) {
            $task->start_time = (new Carbon($task->start_time))->format('Y-m-d H:i');
            $task->dead_line = (new Carbon($task->dead_line))->format('Y-m-d H:i');
            $task->priority = $task->priority ? 'Yes' : 'No';
            $task->action = $this->getButtonForTasks($task->id, $task);

            // Get tour info
            $tour = $task->tourModel;
            if(!empty($tour)){
                $link = route('tour.show', ['tour' => $tour->id]);
                $task->tour = "<span data-tour-link='$link' style='color: blue; text-decoration: underline; cursor: pointer'>{$tour->name}</span>";
            } else {
                $task->tour = '';
            }

            // Get status
            $link = route('task.update', ['task' => $task->id]);
            $taskStatus = Status::query()->where('type', 'task')->where('id', $task->status)->select('name')->first();
            $task->status_name = "<span class='task-data' data-link-update='{$link}' data-task-id='{$task->id}'>{$taskStatus->name}</span>";
        }

        return response()->json([
            'data' => $tasks,
            'recordsTotal' => $total,
            'recordsFiltered' => $total
        ]);
    }

    public function dataTours(Request $request){
        $user = Auth::user();
        $allTours = $user->getToursAttachedToUser();

        // Apply pagination
        $perPage = $request->get('length', 15);
        $page = $request->get('start', 0) / $perPage + 1;
        $total = count($allTours);
        $tours = array_slice($allTours, ($page - 1) * $perPage, $perPage);

        // Process each tour
        foreach($tours as $tour) {
            $tour->action = $this->getButtonForTours($tour->id, $tour);
            $tour->status_name = \View::make('component.tour_status_for_datatable', ['status' => $tour->getStatusName(), 'color' => $tour->getStatusColor()])->render();
            $tour->select = DatatablesHelperController::getSelectButton($tour->id, $tour->name);

            // Add package button
            $tourDay = TourDay::where('tour', $tour->id)->first();
            if($tourDay){
                $link = route( 'tour_package.store' );
                $tour->link = "<button data-link='$link' class='btn btn-default tour_package_add' data-tourDayId='{$tourDay->id}' data-tour_id='{$tour->id}'" .
                           " data-departure_date='{$tour->departure_date}' data-retirement_date='{$tour->retirement_date}'>+</button>";
            } else {
                $tour->link = '';
            }
        }

        return response()->json([
            'data' => $tours,
            'recordsTotal' => $total,
            'recordsFiltered' => $total
        ]);
    }


    public function dataNotifications(Request $request){
        $user = Auth::user();
        $allNotifications = $user->notifications()->latest()->get();

        // Apply pagination
        $perPage = $request->get('length', 15);
        $page = $request->get('start', 0) / $perPage + 1;
        $total = $allNotifications->count();
        $notifications = $allNotifications->slice(($page - 1) * $perPage, $perPage)->values();

        // Process each notification
        foreach($notifications as $notification) {
            $notification->action = $this->getButtonForNotifications($notification->id, $notification->link, $notification);
        }

        return response()->json([
            'data' => $notifications,
            'recordsTotal' => $total,
            'recordsFiltered' => $total
        ]);
    }

    public function edit(){
        $userId = Auth::user()->id;

        $user = User::findOrFail($userId);

        if(!$user)
            return abort(404);

        $roles = Role::all()->pluck('name', 'id');
        $permissions = Permission::all()->pluck('alias', 'name');
        $userRoles = $user->roles->pluck('name', 'id');
        $userPermissions = $user->permissions->pluck('alias', 'name');
        $permissions = $permissions->diff($userPermissions);
        $roles = $roles->diff($userRoles);

        return view('profile.edit', compact('user', 'roles', 'permissions', 'userRoles', 'userPermissions'));
    }
	public function notification_show(){
        $userId = Auth::user()->id;

        $user = User::findOrFail($userId);

        if(!$user)
            return abort(404);

        $activities = Activity::where('causer_id', $userId)->orderBy('created_at', 'desc')->paginate(10);
		$notifications = $user->notifications();
        // foreach ($activities as $activity) {
        //     $activity->description = ltrim(strstr($activity->description, ':'), ':');
        // }
        return view('profile.notification', ['user' => $user, 'activities' => $activities,'notifications' => $notifications]);
    }

}