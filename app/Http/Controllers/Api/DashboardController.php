<?php
/**
 * Created by PhpStorm.
 * User: mmhryshchuk
 * Date: 04.04.18
 * Time: 11:25
 */

namespace App\Http\Controllers\Api;


use App\Announcement;
use App\Chat;
use App\Email;
use App\Helper\PermissionHelper;
use App\Repository\Contracts\TaskRepository;
use App\Repository\Contracts\TourRepository;
use App\RoomTypes;
use App\Status;
use App\Task;
use App\Tour;
use App\User;
use Auth;
use Spatie\Activitylog\Models\Activity;
use Symfony\Component\HttpFoundation\Request;

class DashboardController
{

    const DASHBOARD_TASKS_CALENDAR = 'DASHBOARD_TASKS_CALENDAR';
    const DASHBOARD_INBOX = 'INBOX';
    const DASHBOARD_TOUR_CALENDAR = 'DASHBOARD_TOUR_CALENDAR';
    const DASHBOARD_ANNOUNCEMENTS = 'DASHBOARD_ANNOUNCEMENTS';
    const DASHBOARD_TASKS = 'DASHBOARD_TASKS';
    const DASHBOARD_LATEST_TOURS = 'DASHBOARD_LATEST_TOURS';
    const DASHBOARD_CHAT_GROUPS = 'DASHBOARD_CHAT_GROUPS';
    const DASHBOARD_MAIN_CHAT = 'DASHBOARD_MAIN_CHAT';
    const DASHBOARD_ACTIVITIES = 'DASHBOARD_ACTIVITIES';


    use PermissionHelper;

    private $tourRepository;
    private $taskRepository;

    const TASKS_COUNT = 5;

    public $client;

    public $folders;

    public $inbox;

    protected $user;

    const INBOX_FOLDER = 'INBOX';
    const TRASH_FOLDER = 'INBOX.Trash';
    const PER_PAGE_MAILS_COUNT = 10;


    public function __construct(TourRepository $repository, TaskRepository $taskRepository)
    {
//        $this->middleware('permissions.required');
        $this->tourRepository = $repository;
        $this->taskRepository = $taskRepository;
    }


    public function getAnnouncements(Request $request)
    {
        if ($request->has('userId')) {
            $userId = $request->input('userId');
        } else {
            return;
        }
        $userId = $request->input('userId');
        $announcements = Announcement::leftJoin('users', 'users.id', '=', 'announcements.author')
            ->select('announcements.*', 'users.name as sender')
            ->where('announcements.parent_id', null)->orderBy('announcements.created_at', 'desc')->limit(5)->get();
        foreach ($announcements as $announcement) {
            $routes['show'] = route('announcements.show', ['id' => $announcement->id]);
            $routes['edit'] = route('announcements.edit', ['id' => $announcement->id]);
            $routes['delete_msg'] = "/announcement/$announcement->id/delete_msg";
            $announcement['routes'] = $routes;
        }

        $permissions = $this->getActionPermission(Announcement::class, $userId);
        $data['announcements'] = $announcements;
        return array_merge($data, $permissions);
    }


    public function getInbox(Request $request)
    {

        if ($request->has('userId')) {
            $userId = $request->input('userId');
        } else {
            return;
        }
//        $mails = \DB::table('emails')->where('folder', '=', 'INBOX')
//            ->orderBy('message_id', 'DESC')
//            ->where('user_login', Auth::user()->email_login)
//            ->paginate(self::PER_PAGE_MAILS_COUNT);
        $user = User::findOrFail($userId);
        $mails = Email::where('folder', '=', 'INBOX')
            ->orderBy('message_id', 'DESC')
            ->where('user_login', $user->email_login)
            ->paginate(self::PER_PAGE_MAILS_COUNT);
        $check_email_server = $user->email_server == 0 ? false : true;
        $items = $mails->items();
        foreach ($items as &$mail) {
            $mail->click_redirect = route('email.mail', ['id' => $mail->message_id, 'folder' => self::INBOX_FOLDER]);
            $mail->move_to_form = route('email.getMoveToForm', ['id' => $mail['message_id'], 'folder' => self::INBOX_FOLDER], false);
            $mail->compose_form = route('email.getComposeForm', ['id' => $mail['message_id'], 'folder' => self::INBOX_FOLDER], false);
            $mail->delete_msg = route('email.deleteMsg', ['id' => $mail['message_id'], 'folder' => self::INBOX_FOLDER], false);
        }
        $data = [
            'mails' => $mails,
            'imapConnected' => $check_email_server,
            'currentFolder' => 'INBOX',
            'mailsCount' => count($mails),
        ];
        return $data;
    }

    public function getActivities()
    {
        return Activity::orderBy('created_at', 'desc')->with('causer')->simplePaginate(5);
    }

    public function getTasks(Request $request)
    {
        if ($request->has('userId')) {
            $userId = $request->input('userId');
        } else {
            return;
        }
        $user = User::findOrFail($userId);
        if ($user->hasRole('admin')) {
            $tasks = $this->taskRepository->findPending(self::TASKS_COUNT);
        } else {
            $tasks = $this->taskRepository->getAllTaskForDashboard($user->id, self::TASKS_COUNT);
        }
        $taskStatuses = Status::query()->orderBy('sort_order', 'asc')->where('type', 'task')->get();

        foreach ($tasks as &$task) {
            $task['tour_name'] = $task->tourName();
            $task['show_assigned_users'] = $task->showAssignedUsers();
            $task['task_type'] = Task::$taskTypes[$task->task_type];
            $task['tour_link_show'] = $task->tourLinkShow();
            $task['data_update_link'] = route('task.update', ['id' => $task->id]);
            $routes['show'] = route('task.show', ['task' => $task->id]);
            $routes['edit'] = route('task.edit', ['id' => $task->id]);
            $routes['delete_msg'] = "/task/$task->id/deleteMsg";
            $task['routes'] = $routes;

        }

        $data['tasks'] = $tasks;
        $data['taskStatuses'] = $taskStatuses;
        $permissions = $this->getActionPermission(Task::class, $userId);

        return array_merge($data, $permissions);
    }

    public function getLatestTours(Request $request)
    {
        if ($request->has('userId')) {
            $userId = $request->input('userId');
        } else {
            return;
        }

        $user = User::findOrFail($userId);

        if ($user->hasRole('admin')) {
            $tours = $this->tourRepository->all()->sortByDesc('departure_date')->values()->take(15)->all();
        } else {
            $tours = $this->tourRepository->allForAssignedWithId($userId)->sortByDesc('departure_date')->values()->take(15)->all();
        }

        foreach ($tours as &$tour) {
            $tour->status_name = $tour->getStatusName();
            $tour->status_link = route('tour.update', ['tour' => $tour->id]);

            $routes['show'] = route('tour.show', ['tour' => $tour->id]);
            $routes['edit'] = route('tour.edit', ['tour' => $tour->id]);
            $routes['delete_msg'] = "/tour/$tour->id/deleteMsg";
            $tour->routes = $routes;
        }
        $permissions = $this->getActionPermission(Tour::class, $userId);
        $data['tours'] = $tours;
        return array_merge($data, $permissions);
    }


    public function getTaskCreatePopup(Request $request)
    {
        if ($request->has('userId')) {
            $userId = $request->input('userId');
        } else {
            return;
        }
        $user = User::findOrFail($userId);
        $toursAttachedUser = $this->tourRepository->getToursAttachedToUser($user);
        $users = User::orderBy('name')->get();
        $statuses = Status::query()->where('type', 'task')->orderBy('sort_order', 'asc')->get();
        $data['users'] = $users;
        foreach ($users as &$user) {
            $user->text = $user->name;
        }
        foreach ($toursAttachedUser as &$tour) {
            $tour->text = $tour->name;
        }
        foreach ($statuses as &$status) {
            $status->text = $status->name;
        }
        $taskTypes = [
            ['id' => 1, 'text' => 'Personal'],
            ['id' => 2, 'text' => 'In general']
        ];
        $data['taskTypes'] = $taskTypes;
        $data['toursAttachedUser'] = $toursAttachedUser;
        $data['statuses'] = $statuses;
        return $data;

    }

    public function getModalAddTour()
    {
        $room_typesDvo = RoomTypes::query()->orderBy('sort_order', 'ASC')->get();
        $users = User::orderBy('name')->get();
        $statuses_tour = Status::query()->orderBy('sort_order', 'asc')->where('type', 'tour')->get();

        $room_types = array();

        foreach ($room_typesDvo as $room_type) {
            $room_type['count_room'] = null;
            $room_type['price_room'] = null;
            $room_type->text = $room_type->name;

            $room_types[] = $room_type;
        }
        foreach ($statuses_tour as &$status) {
            $status->text = $status->name;
        }
        foreach ($users as &$user) {
            $user->text = $user->name;
        }

        $data['statuses_tour'] = $statuses_tour;
        $data['room_types'] = $room_types;
        $data['users'] = $users;
        return $data;
    }

    public function getTourUsers()
    {
        $tourUsers = Tour::select(\DB::raw('count(*) as count, users.name, users.id'))->groupBy('assigned_user')
            ->join('users', 'tours.assigned_user', 'users.id')->limit(5)->orderBy('count', 'desc')
            ->get()->toArray();
        return $tourUsers;
    }


}