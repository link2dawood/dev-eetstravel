<?php

namespace App\Http\Controllers\ScaffoldInterface;

use App\Announcement;
use App\Bus;
use App\Chat;
use App\ChatMessage;
use App\Comment;
use App\Country;
use App\EmailFolder;
use App\Helper\EmailCharsetDecorator;
use App\Http\Controllers\Controller;
use App\Status;
use App\Task;
use App\User;
use Ddeboer\Imap\Exception\Exception;
use Ddeboer\Imap\Server;
use Illuminate\Http\Request;
use App\Tour;
use Illuminate\Support\Facades\Auth;
use Spatie\Activitylog\Models\Activity;
use App\Repository\Contracts\TourRepository;
use Carbon\Carbon;
use App\Repository\Contracts\TaskRepository;
use App\RoomTypes;
use Illuminate\Http\Response;
use Symfony\Component\HttpFoundation\Cookie;

/**
 * Class AppController.
 *
 * @author Amrani Houssain <amranidev@gmail.com>
 */
class AppController extends Controller
{
    private $tourRepository;
    private $taskRepository;

    const TASKS_COUNT = 15;

    public $client;

    public $folders;

    public $inbox;

    protected $user;

    const INBOX_FOLDER = 'INBOX';
    const TRASH_FOLDER = 'INBOX.Trash';
    const PER_PAGE_MAILS_COUNT = 10;


    public function __construct(TourRepository $repository, TaskRepository $taskRepository)
    {
        $this->middleware('permissions.required');
        $this->tourRepository = $repository;
        $this->taskRepository = $taskRepository;
    }


    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function dashboard()
    {
        $this->folders = EmailFolder::where('user_login', Auth::user()->email_login)->get();
        $chat = Chat::where(['type' => Chat::CHAT_TYPE_ALL])->first();
        $chatUsers = User::all()->except(\Auth::id());
        $user = Auth::user();
        $room_typesDvo = RoomTypes::query()->orderBy('sort_order', 'ASC')->get();
        $room_types = array();

        foreach ($room_typesDvo as $room_type) {
            $room_type['count_room'] = null;
            $room_type['price_room'] = null;
            $room_types[] = $room_type;
			
        }
	

        $check_email_server = $user->email_server == 0 ? false : true;


        // Get announcements data directly
        $announcements = \App\Announcement::leftJoin('users', 'users.id', '=', 'announcements.author')
            ->select('announcements.*', 'users.name as sender')
            ->where('announcements.parent_id', null)->orderBy('announcements.created_at', 'desc')->limit(5)->get();

        foreach ($announcements as $announcement) {
            $routes['show'] = route('announcements.show', ['announcement' => $announcement->id]);
            $routes['edit'] = route('announcements.edit', ['announcement' => $announcement->id]);
            $routes['delete_msg'] = "/announcement/$announcement->id/delete_msg";
            $announcement->routes = $routes;
            $announcement->action_buttons = $this->generateActionButtons($announcement, $routes);
        }

        // Get tasks data directly
        $tasks = \App\Task::where('assign', $user->id)
            ->where('status', '!=', 7)
            ->orderBy('dead_line', 'asc')
            ->limit(5)
            ->get();

        $taskStatuses = \App\Status::query()->orderBy('sort_order', 'asc')->where('type', 'task')->get();

        foreach ($tasks as $task) {
            $task->tour_name = $task->tourName();
            $task->show_assigned_users = $task->showAssignedUsers();
            $task->task_type = \App\Task::$taskTypes[$task->task_type];
            $task->tour_link_show = $task->tourLinkShow();
            $task->data_update_link = route('task.update', ['task' => $task->id]);

            $routes['show'] = route('task.show', ['task' => $task->id]);
            $routes['edit'] = route('task.edit', ['task' => $task->id]);
            $routes['delete_msg'] = "/task/$task->id/deleteMsg";
            $task->routes = $routes;
            $task->action_buttons = $this->generateActionButtons($task, $routes);
        }

        return view('scaffold-interface.dashboard.dashboard',
            [
                'main_chat' => $chat,
                'room_types' => $room_types,
                'chatUsers' => $chatUsers,
                'imapConnected' => $check_email_server,
                'announcements' => $announcements,
                'tasks' => $tasks,
                'taskStatuses' => $taskStatuses
            ]);
    }


    /**
     * @param Request $request
     *
     * @return string
     */
    public function getBusesForCalendar(Request $request)
    {
        $user = Auth::user();

        if ($request->id) {
            $user = User::where('id', $request->id)->first();
        }

        $tourEvents = [];

        // місяць поточний місяц
        $startDate = date('Y-10-01');
        $endDate = date('Y-10-t');
        $tours = $user->getToursAttachedToUserForCalendar($startDate, $endDate, $user->id);
        foreach ($tours as $tour) {
            $segment = [];
            $startDate = Carbon::parse($tour->created_at);
            $endDate = Carbon::parse($tour->retirement_date)->addDay(1);
            $colors = ["#46615e", "#727d6f", "#8dc49f"];
            $segment[] = ["start" => $startDate->format('Y-m-d'), "end" => $endDate->format('Y-m-d'), "color" => $colors[rand(0, 2)], "task" => $tour->name, "id" => $tour->id];
            $tourEvents[] = [
                'category' => $tour->name,
                'segments' => $segment
            ];
        }

        return json_encode($tourEvents);
    }

    /**
     * @param Request $request
     *
     * @return string
     */
    public function getToursForCalendar(Request $request)
    {
        $user = Auth::user();

        $tourEvents = [];
        $startDate = (new Carbon())->now()->subMonth()->startOfMonth()->format('Y-m-d H:i:s');
        $endDate = (new Carbon())->now()->addMonth()->endOfMonth()->format('Y-m-d H:i:s');
        if ($request->id) {
            $user = User::findOrFail($request->id);
            $tours = $user ->tours()->whereColumn('departure_date', '<=', 'retirement_date')
                ->where(
                    function($query) {
                        $query->where('is_quotation', '!=',true)->orWhereNull('is_quotation');
                    }
                )->get();
        }else{
            $tours = Tour::query()

                ->whereColumn('departure_date', '<=', 'retirement_date')
                ->where(
                    function($query) {
                        $query->where('is_quotation', '!=',true)->orWhereNull('is_quotation');
                    }
                )
                ->get();

        }


        foreach ($tours as $tour) {

            $is_by_user_tour = false;

            if (Auth::user()->hasRole('admin')) {
                $segment = [];
                $startDate = Carbon::parse($tour->created_at);
                $depDate = Carbon::parse($tour->departure_date);
                $endDate = Carbon::parse($tour->retirement_date)->addDay();
                $edepDate = Carbon::parse($tour->retirement_date);

                $segment[] = ["start" => $startDate->format('Y-m-d'), "end" => $endDate->format('Y-m-d'), "dep" => $depDate->format('Y-m-d'), "endp" => $edepDate->format('Y-m-d'), "color" => $this->getTourBackgroundColor($tour), "task" => $tour->name, "id" => $tour->id];
                $tourEvents[] = [
                    'category' => $tour->name,
                    'segments' => $segment
                ];
            }else
            if($tour->isUserTour($user)){
                $segment = [];
                $startDate = Carbon::parse($tour->created_at);
                $depDate = Carbon::parse($tour->departure_date);
                $endDate = Carbon::parse($tour->retirement_date)->addDay();
                $edepDate = Carbon::parse($tour->retirement_date);

                $segment[] = ["start" => $startDate->format('Y-m-d'), "end" => $endDate->format('Y-m-d'), "dep" => $depDate->format('Y-m-d'), "endp" => $edepDate->format('Y-m-d'), "color" => $this->getTourBackgroundColor($tour), "task" => $tour->name, "id" => $tour->id];

                $tourEvents[] = [
                    'category' => $tour->name,
                    'segments' => $segment
                ];
            }
        }


        return json_encode($tourEvents);
    }

    /**
     * @param Request $request
     *
     * @return string
     */
    public function getToursTasksForCalendar(Request $request)
    {
        $user = Auth::user();
        $tourTasksEvents = array();
        $startDate = $request->get('start');
        $endDate = $request->get('end');

        $tourTasks = $user->getTasksAttachedToUserForCalendar($startDate, $endDate);

        foreach ($tourTasks as $tourTask) {
            $endDate = Carbon::parse($tourTask->dead_line);
            $tourTasksEvents[] = [
                'title' => $tourTask->content,
                'id' => $tourTask->id,
                'date' => $endDate->format('Y-m-d') . 'T' . $endDate->format('H:i:s'),
                'allDay' => false,
                'c_type' => "month",
                'backgroundColor' => $this->getTaskBackgroundColor($tourTask)
            ];
        }


        foreach (\App\Holidaycalendarday::where('user_id', $user->id)->get() as $day) {
            $endDate = Carbon::parse($day->start_time);
            array_push($tourTasksEvents, [
                'title' => $day->name,
                'id' => 'Holiday',
                'date' => $endDate->format('Y-m-d'),
                'allDay' => true,
                'c_type' => "month",
                'backgroundColor' => $day->backgroundcolor
            ]);
        }

        return response()->json($tourTasksEvents);
    }

    /**
     * Quick create calendar event (task/tour/meeting)
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function quickCreateCalendarEvent(Request $request)
    {
        try {
            $request->validate([
                'title' => 'required|string|max:255',
                'type' => 'required|in:task,tour,meeting',
                'date' => 'required|date',
                'description' => 'nullable|string|max:500'
            ]);

            $user = Auth::user();
            $type = $request->get('type');
            $title = $request->get('title');
            $date = $request->get('date');
            $description = $request->get('description', '');

            switch ($type) {
                case 'task':
                    $task = new Task();
                    $task->content = $title;
                    $task->description = $description;
                    $task->dead_line = Carbon::parse($date)->format('Y-m-d H:i:s');
                    $task->status = 2; // Pending status
                    $task->save();

                    // Attach task to current user
                    $task->users()->attach($user->id);

                    $eventData = [
                        'id' => $task->id,
                        'title' => $task->content,
                        'start' => $task->dead_line,
                        'backgroundColor' => $this->getTaskBackgroundColor($task),
                        'type' => 'task'
                    ];
                    break;

                case 'tour':
                    // Create a basic tour entry (you might want to redirect to full tour creation)
                    $tour = new Tour();
                    $tour->name = $title;
                    $tour->remark = $description;
                    $tour->departure_date = $date;
                    $tour->retirement_date = $date;
                    $tour->pax = 1;
                    $tour->status = 46; // Requested status
                    $tour->is_quotation = 1;
                    $tour->responsible = $user->id;
                    $tour->save();

                    $eventData = [
                        'id' => $tour->id,
                        'title' => $tour->name,
                        'start' => $tour->departure_date,
                        'backgroundColor' => $this->getTourBackgroundColor($tour),
                        'type' => 'tour'
                    ];
                    break;

                case 'meeting':
                    // Create as a task with meeting type
                    $task = new Task();
                    $task->content = $title . ' (Meeting)';
                    $task->description = $description;
                    $task->dead_line = Carbon::parse($date)->format('Y-m-d H:i:s');
                    $task->status = 2; // Pending status
                    $task->save();

                    // Attach task to current user
                    $task->users()->attach($user->id);

                    $eventData = [
                        'id' => $task->id,
                        'title' => $task->content,
                        'start' => $task->dead_line,
                        'backgroundColor' => '#6f42c1', // Purple for meetings
                        'type' => 'meeting'
                    ];
                    break;

                default:
                    return response()->json([
                        'success' => false,
                        'message' => 'Invalid event type'
                    ], 400);
            }

            return response()->json([
                'success' => true,
                'message' => ucfirst($type) . ' created successfully!',
                'event' => $eventData
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error creating ' . $request->get('type', 'event') . ': ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * @param Task $task
     *
     * @return string
     */
    private function getTaskBackgroundColor(Task $task)
    {
        //f (!$task->tour) {
        //    return '#d73925'; // red
        //}
        switch ($task->status) {
            case 2: // Pending
                return '#d73925';
            case 8: // Abort
                return '#3a87ad';
            default:
                return '#ccc';
        }

    }

    /**
     * @param Tour $tour
     *
     * @return string
     */
    private function getTourBackgroundColor(Tour $tour)
    {
        switch ($tour->status) {
            case 1: // Pending
                return '#d73925';
            case 4: // Go ahead
                return '#3a87ad';
            default:
                return '#ccc';
        }
    }

//    public function

    public function getToursByCountries(Request $request)
    {
        $toursArray = [];
        $tours = Tour::select(\DB::raw('count(*) as count, country_begin'))->groupBy('country_begin')
            ->get()->toArray();
        foreach ($tours as $tour) {
            $country = Country::where('alias', '=', $tour['country_begin']);
            if ($country) {
                $toursArray[$tour['country_begin']] = $tour['count'];
            }
        }

        return json_encode($toursArray);
    }

    public function getTasksBlock()
    {
		    $user = Auth::user();
      
       //$tasks = task::where("assign",$user->id)->get();
		$tasks = task::where('assign', $user->id)
    ->where('status', '!=', 7)
    ->orderBy('dead_line', 'asc') // Order by deadline in ascending order
    ->limit(5);
		
       
        return view('scaffold-interface.dashboard.components.tasks_list',compact('tasks'));
    }

    public function getAllHollydayCalendars(Request $request)
    {
        if ($request->cookie('calendarIds') != null) {
            $calendarIds = unserialize($request->cookie('calendarIds'));
        } else {
            $calendarIds = [];
            \App\Hollydaycalendar::where('checked', 1)->update(['checked' => 'false']);
        }

        $calendars = \App\Hollydaycalendar::all()->toArray();

        foreach ($calendarIds as $calendarId) {
            $key = array_search($calendarId, array_column($calendars, 'id'));
            $calendars[$key]['checked'] = 1;
        }

        return response()->json(json_encode($calendars))->withCookie('calendarIds', serialize($calendarIds), 2147483647);
    }

    public function checkHollydayCalendarById($id, Request $request)
    {
        if ($request->cookie('calendarIds') != null) {
            $calendarIds = unserialize($request->cookie('calendarIds'));
        } else {
            $calendarIds = [];
        }

        $isChecked = $request->checked;
        if ($id == 'all') {
            if ($isChecked == '1') {
                $calendarIds = \App\Hollydaycalendar::all()->pluck('id')->toArray();
            } else {
                $calendarIds = [];
            }
            return response()->json(json_encode('Success! id=' . $id))->withCookie('calendarIds', serialize($calendarIds), 2147483647);
        }

        if ($isChecked) {
            if (!in_array($id, $calendarIds)) {
                array_push($calendarIds, $id);
            }
        } else {
            if (in_array($id, $calendarIds)) {
                $calendarIds = array_diff($calendarIds, [$id]);
            }
        }

        return response()->json(json_encode('Success! id=' . $id))->withCookie('calendarIds', serialize($calendarIds), 2147483647);

    }

    private function generateActionButtons($model, $routes)
    {
        $user = Auth::user();
        $modelClass = get_class($model);

        $buttons = '<div style="width:150px; text-align: center;" class="buttons_margin">';

        // Show button
        if ($user->can(strtolower(class_basename($modelClass)) . '.show')) {
            $buttons .= '<a href="' . $routes['show'] . '" class="btn btn-warning btn-sm show-button"><i class="fa fa-info-circle"></i></a>';
        }

        // Edit button
        if ($user->can(strtolower(class_basename($modelClass)) . '.edit')) {
            $buttons .= '<a href="' . $routes['edit'] . '" class="btn btn-primary btn-sm"><i class="fa fa-pencil-square-o"></i></a>';
        }

        // Delete button
        if ($user->can(strtolower(class_basename($modelClass)) . '.destroy')) {
            $buttons .= '<a data-link="' . $routes['delete_msg'] . '" data-toggle="modal" data-target="#myModal" class="btn btn-danger btn-sm delete"><i class="fa fa-trash-o"></i></a>';
        }

        $buttons .= '</div>';

        return $buttons;
    }

}
