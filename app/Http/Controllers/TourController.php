<?php

namespace App\Http\Controllers;

use App\Offices;
use App\Bus;
use App\BusDay;
use App\Comment;
use App\Driver;
use App\Event;
use App\GooglePlaces;
use App\Guide;
use App\Helper\FileTrait;
use App\Helper\CitiesHelper;
use App\Helper\LaravelFlashSessionHelper;
use App\Helper\PermissionHelper;
use Yajra\Datatables\Datatables;
use App\Hotel;
use App\HotelRoomTypes;
use App\Quotation;
use App\Restaurant;
use App\Status;
use App\Task;
use App\TourDay;
use App\TourPackage;
use App\Helper\TourPackage\TourService;
use App\Transfer;
use App\TransferToBuses;
use App\TransferToDrivers;
use App\User;
use App\Client;
use App\ClientInvoices;
use App\Invoices;
use App\InvoicesTours;
use Auth;
use DateInterval;
use DatePeriod;
use DB;
use Illuminate\Support\Facades\App;
use Illuminate\Http\Request;
use App\Http\Requests\StoreTourRequest;
use App\Http\Requests\UpdateTourRequest;
use App\Http\Controllers\Controller;
use App\Tour;
use Amranidev\Ajaxis\Ajaxis;
use SKAgarwal\GoogleApi\PlacesApi;
use URL;
use View;
use App\Helper\ExportTrait;
use Carbon\Carbon;
use App\Helper\HelperTrait;
use App\Repository\Contracts\TourRepository;
use App\Repository\Contracts\TaskRepository;
use App\Country;
use App\City;
use App\Notification;
use App\TourRoomTypeHotel;
use App\RoomTypes;
use App\Attachment;
use Image;
use App\Comparison;
use App\ComparisonRow;
use App\Childrens;
class TourController extends Controller
{
    use FileTrait;
    use ExportTrait;
    use HelperTrait;
    /**
     * tour repository
     */
    public $repository;
    /**
     * task repository
     */
	private $taskRepository;


    public $tempColorsForTourDay = [
        '0000AA', '00AA00', '00AAAA', 'AA0000', 'AA00AA', 'AAAA00', 'AAAAAA', '0000FF', '00AA55',
        '00AAFF', 'AA0055', 'AA00FF', 'AAAA55', 'AAAAFF', '005500', '0055AA', '00FF00', '00FFAA',
        'AA5500', 'AA55AA', 'AAFF00', 'AAFFAA', '0055FF', '00FF55', '0000FF', 'AAFF66', 'AAFFFF',
        '5500AA', 'FF0000', 'FF00AA', 'FFAA00', 'FFAAAA', '55AAFF', 'FF00FF', 'FFAA55', '55FFAA',
        'FF5500', 'FFFF00', 'FFFFAA', 'FFFFF'
    ];



    public function __construct(TourRepository $repository, TaskRepository $taskRepository)
    {
        $this->middleware('permissions.required');
        $this->repository = $repository;
        $this->taskRepository = $taskRepository;
        $this->middleware('preventBackHistory', ['except' => 'landingPage']);
        $this->middleware('auth', ['except' => 'landingPage']);
    }

    /**
     * get action buttons
     * @param  $id tour id
     * @return mixed
     */
    public function getButton($id, $isQuotation = false, $tour, array $perm)
    {
        $url = array('show'       => route('tour.show', ['tour' => $id]),
                     'edit'       => route('tour.edit', ['tour' => $id]),
                     'delete_msg' => "/tour/{$id}/deleteMsg",
                     'id'         => $id);

        return DatatablesHelperController::getActionButtonTours($url, $isQuotation, $perm);
//        return DatatablesHelperController::getActionButton($url, $isQuotation, $tour);
    }
    
    public function getQuotationButton($id, $isQuotation = false, $tour)
    {
        $url = array('show'       => route('tour.show', ['tour' => $id]),
                     'edit'       => route('tour.edit', ['tour' => $id]),
                     'delete_msg' => "/tour/{$id}/deleteMsg",
                     'id'         => $id);
        return DatatablesHelperController::getActionButton($url, $isQuotation, $tour);
    }

    public function getButtonForTasks($id, $tour, $task)
    {
        $url = array('show'       => route('task.show', ['task' => $id]),
            'edit'       => route('task.edit', ['task' => $id]),
            'delete_msg' => "/task/{$id}/deleteMsg/{$tour}");

        return DatatablesHelperController::getActionButton($url, false, $task);
    }


    /**
     * Get tour-specific extra buttons
     */
    private function getTourExtraButtons($tour)
    {
        $tourDay = TourDay::where('tour', $tour->id)->first();
        if ($tourDay) {
            $link = route('tour_package.store');
            return "<button data-link='$link' class='btn btn-success btn-sm tour_package_add'
                data-tourDayId='{$tourDay->id}' data-tour_id='{$tour->id}'
                data-departure_date='{$tour->departure_date}'
                data-retirement_date='{$tour->retirement_date}' title='Add Package'>
                <i class='fa fa-plus'></i>
            </button>";
        }
        return '';
    }

	 public function tour_achieve_data(Request $request)
    {
        //if (Auth::user()->hasRole('admin')) {
        $currentDate = Carbon::now(); // Get the current date and time
		$oneWeekAgo = $currentDate->copy()->subWeek(); // Subtract one week from the current date
//->where('retirement_date', '<', $oneWeekAgo)
		$tours = Tour::where('status', 6)->orWhere('status',39)
			->get();
        //} else {
            //$tours = $this->repository->allForAssigned();
        //}

        $permission_destroy = PermissionHelper::$relationsPermissionDestroy['App\Tour'];
        $permission_edit = PermissionHelper::$relationsPermissionEdit['App\Tour'];
        $permission_show = PermissionHelper::$relationsPermissionShow['App\Tour'];
        
        $perm = [];        
        $perm['show'] = Auth::user()->can($permission_show);        
        $perm['edit'] = Auth::user()->can($permission_edit);
        $perm['destroy'] = Auth::user()->can($permission_destroy);
        $perm['clone'] = Auth::user()->can('tour.create');
        
        return Datatables::of($tours)->addColumn('action', function ($tour) use($perm) {
                return $this->getButton($tour->id, false, $tour, $perm);
            })
			->addColumn('client_name', function ($tour){
				$client_name = null;
				if($tour->client_id != 0 || !empty($tour->client_id)){
					$client = Client::find($tour->client_id);
					$client_name =  $client->name;
				}
				   
				
				   return $client_name;
			})
            ->addColumn('status_name', function ($tour){
                if(Auth::user()->can('tour.edit')){
                    $status = View::make('component.tour_status_for_datatable', ['status' => $tour->getStatusName(), 'color' => $tour->getStatusColor()]);
                }else{
                    $status = $tour->getStatusName();
                }
                return $status;
            })
            ->addColumn('select', function ($tour) {
                return DatatablesHelperController::getSelectButton($tour->id, $tour->name);
            })
			->addColumn('res_user', function ($tour) {
				
				
                return $tour->getResponsibleUser()->name??"";
            })
            ->addColumn('link', function($tour){
                $tourDay = TourDay::where('tour', $tour->id)->first();
                $link = route('tour_package.store');
                if($tourDay){
                return "<button data-link='$link' class='btn btn-success tour_package_add' data-tourDayId='{$tourDay->id}' data-tour_id='{$tour->id}'" .
                    " data-departure_date='{$tour->departure_date}' data-retirement_date='{$tour->retirement_date}'>+</button>";
                }
            })
            ->rawColumns(['select', 'action', 'link'])
            ->make(true);
    }
	public function monthly_chart_data(Request $request)
    {
        //if (Auth::user()->hasRole('admin')) {
         $tours = Tour::where('status', 4);
        //} else {
            //$tours = $this->repository->allForAssigned();
        //}

        $permission_destroy = PermissionHelper::$relationsPermissionDestroy['App\Tour'];
        $permission_edit = PermissionHelper::$relationsPermissionEdit['App\Tour'];
        $permission_show = PermissionHelper::$relationsPermissionShow['App\Tour'];
        
        $perm = [];        
        $perm['show'] = Auth::user()->can($permission_show);        
        $perm['edit'] = Auth::user()->can($permission_edit);
        $perm['destroy'] = Auth::user()->can($permission_destroy);
        $perm['clone'] = Auth::user()->can('tour.create');
        
        return Datatables::of($tours)
			
			->filter(function ($query) use ($request) {
				$year = $request->input('year');
				$month = $request->input('month');

				if ($year) {
					$query->whereYear('departure_date', $year);
				}

				if ($month) {
					$query->whereMonth('departure_date', $month);
				}
			})
			->addColumn('action', function ($tour) use($perm) {
                return $this->getButton($tour->id, false, $tour, $perm);
            })
            ->addColumn('status_name', function ($tour){
                if(Auth::user()->can('tour.edit')){
                    $status = View::make('component.tour_status_for_datatable', ['status' => $tour->getStatusName(), 'color' => $tour->getStatusColor()]);
                }else{
                    $status = $tour->getStatusName();
                }
                return $status;
            })
            ->addColumn('select', function ($tour) {
                return DatatablesHelperController::getSelectButton($tour->id, $tour->name);
            })
			->addColumn('res_user', function ($tour) {
				
				
                return $tour->getResponsibleUser()->name??"";
            })
            ->addColumn('link', function($tour){
                $tourDay = TourDay::where('tour', $tour->id)->first();
                $link = route('tour_package.store');
                if($tourDay){
                return "<button data-link='$link' class='btn btn-success tour_package_add' data-tourDayId='{$tourDay->id}' data-tour_id='{$tour->id}'" .
                    " data-departure_date='{$tour->departure_date}' data-retirement_date='{$tour->retirement_date}'>+</button>";
                }
            })
            ->rawColumns(['select', 'action', 'link'])
            ->make(true);
    }
		public function cancelled_chart_data(Request $request)
    {
        //if (Auth::user()->hasRole('admin')) {
         $tours = Tour::where('status', 6);
        //} else {
            //$tours = $this->repository->allForAssigned();
        //}

        $permission_destroy = PermissionHelper::$relationsPermissionDestroy['App\Tour'];
        $permission_edit = PermissionHelper::$relationsPermissionEdit['App\Tour'];
        $permission_show = PermissionHelper::$relationsPermissionShow['App\Tour'];
        
        $perm = [];        
        $perm['show'] = Auth::user()->can($permission_show);        
        $perm['edit'] = Auth::user()->can($permission_edit);
        $perm['destroy'] = Auth::user()->can($permission_destroy);
        $perm['clone'] = Auth::user()->can('tour.create');
        
        return Datatables::of($tours)
			
			->filter(function ($query) use ($request) {
				$year = $request->input('year');
				$month = $request->input('month');

				if ($year) {
					$query->whereYear('departure_date', $year);
				}

				if ($month) {
					$query->whereMonth('departure_date', $month);
				}
			})
			->addColumn('action', function ($tour) use($perm) {
                return $this->getButton($tour->id, false, $tour, $perm);
            })
            ->addColumn('status_name', function ($tour){
                if(Auth::user()->can('tour.edit')){
                    $status = View::make('component.tour_status_for_datatable', ['status' => $tour->getStatusName(), 'color' => $tour->getStatusColor()]);
                }else{
                    $status = $tour->getStatusName();
                }
                return $status;
            })
            ->addColumn('select', function ($tour) {
                return DatatablesHelperController::getSelectButton($tour->id, $tour->name);
            })
			->addColumn('res_user', function ($tour) {
				
				
                return $tour->getResponsibleUser()->name??"";
            })
            ->addColumn('link', function($tour){
                $tourDay = TourDay::where('tour', $tour->id)->first();
                $link = route('tour_package.store');
                if($tourDay){
                return "<button data-link='$link' class='btn btn-success tour_package_add' data-tourDayId='{$tourDay->id}' data-tour_id='{$tour->id}'" .
                    " data-departure_date='{$tour->departure_date}' data-retirement_date='{$tour->retirement_date}'>+</button>";
                }
            })
            ->rawColumns(['select', 'action', 'link'])
            ->make(true);
    }
	 public function client_data(Request $request)
    {
        try {
            // Get all tours with "Requested" status (46)
            $tours = Tour::where('status', 46)->get();

            // If no tours found, return empty result to prevent errors
            if ($tours->isEmpty()) {
                return Datatables::of(collect([]))->make(true);
            }

            $permission_destroy = PermissionHelper::$relationsPermissionDestroy['App\Tour'];
            $permission_edit = PermissionHelper::$relationsPermissionEdit['App\Tour'];
            $permission_show = PermissionHelper::$relationsPermissionShow['App\Tour'];
        
        $perm = [];        
        $perm['show'] = Auth::user()->can($permission_show);        
        $perm['edit'] = Auth::user()->can($permission_edit);
        $perm['destroy'] = Auth::user()->can($permission_destroy);
        $perm['clone'] = Auth::user()->can('tour.create');
        
        return Datatables::of($tours)->addColumn('action', function ($tour) use($perm) {
                return $this->getButton($tour->id, false, $tour, $perm);
            })
			->addColumn('client_name', function ($tour){
				$client_name = null;
				if($tour->client_id && $tour->client_id != 0){
					$client = Client::find($tour->client_id);
					$client_name = $client ? $client->name : null;
				}
				return $client_name;
			})
            ->addColumn('status_name', function ($tour){
                if(Auth::user()->can('tour.edit')){
                    $status = View::make('component.tour_status_for_datatable', ['status' => $tour->getStatusName(), 'color' => $tour->getStatusColor()]);
                }else{
                    $status = $tour->getStatusName();
                }
                return $status;
            })
            ->addColumn('select', function ($tour) {
                return DatatablesHelperController::getSelectButton($tour->id, $tour->name);
            })
            ->addColumn('link', function($tour){
                $tourDay = TourDay::where('tour', $tour->id)->first();
                $link = route('tour_package.store');
                if($tourDay){
                return "<button data-link='$link' class='btn btn-success tour_package_add' data-tourDayId='{$tourDay->id}' data-tour_id='{$tour->id}'" .
                    " data-departure_date='{$tour->departure_date}' data-retirement_date='{$tour->retirement_date}'>+</button>";
                }
            })
            ->rawColumns(['select', 'action', 'link'])
            ->make(true);
        } catch (\Exception $e) {
            // Log the error for debugging
            \Log::error('DataTables Ajax Error in client_data: ' . $e->getMessage());

            // Return empty DataTable response
            return Datatables::of(collect([]))->make(true);
        }
    }
	/**
	 * data for datatables
	 * @param  Request $request
	 * @return datatables view
	 */

    public function tasksData(Request $request){
        $idTasks = explode(",", $request->get('idTasks'));
        $tasks = Task::query()
            ->orderBy('priority', 'ASC')
            ->orderBy('dead_line', 'ASC')
            ->whereIn('id', $idTasks)->get();

        return Datatables::of($tasks)
            ->addColumn('action', function ($task) {
                return $this->getButtonForTasks($task->id, $task->tour, $task);
            })
            ->addColumn('task_type', function ($task) {
                return Task::$taskTypes[$task->task_type];
            })
            ->addColumn('assign', function ($task) {
                return $task->showAssignedUsers();
            })
            ->addColumn('priority', function ($task) {
                return $task->priority ? 'Yes' : 'No';
            })
            ->addColumn('dead_line', function ($task) {
                return $task->dead_line = (new Carbon($task->dead_line))->format('Y-m-d H:i');
            })
            ->addColumn('status_name', function ($task){
                $link = route('task.update', ['id' => $task->id]);
                $taskStatuses = Status::query()->orderBy('sort_order', 'asc')
                    ->where('type', 'task')->get();
                return view('component.task_datatables_status_update', ['taskStatuses' => $taskStatuses, 'task' => $task]);
            })
            ->rawColumns(['action', 'status_name'])
            ->make(true);
    }

    /**
     * Get formatted tasks data for direct display (without DataTables)
     */
    private function getFormattedTasksData($idTasks)
    {
        if (empty($idTasks)) {
            return [];
        }

        $tasks = Task::query()
            ->orderBy('priority', 'DESC')
            ->orderBy('dead_line', 'ASC')
            ->whereIn('id', $idTasks)->get();

        $formattedTasks = [];
        $taskStatuses = Status::query()->orderBy('sort_order', 'asc')
            ->where('type', 'task')->get();

        foreach ($tasks as $task) {
            $formattedTask = [
                'id' => $task->id,
                'content' => $task->content,
                'dead_line' => (new Carbon($task->dead_line))->format('Y-m-d H:i'),
                'assign' => $task->showAssignedUsers(),
                'priority' => $task->priority ? 'Yes' : 'No',
                'priority_value' => $task->priority,
                'status_name' => $task->status_name ?? '',
                'status_id' => $task->status,
                'action_buttons' => $this->getButtonForTasks($task->id, $task->tour, $task),
                'task_statuses' => $taskStatuses,
                'task' => $task
            ];
            $formattedTasks[] = $formattedTask;
        }

        return $formattedTasks;
    }

    public function getTourDays(Request $request){
        $tour_id = $request->get('tour_id');

        $tourDates = TourDay::query()->where('tour', $tour_id)->get()->sortBy('date');

        $dates = array();
        foreach ($tourDates as $tourDate){
            $dates[] = $tourDate->date;
        }

        $collectDate = collect($dates);

        $startDate = $collectDate->first();
        $endDate = $collectDate->last();

        return response()->json([
            'startDate' => $startDate,
            'endDate' => $endDate
        ]);

    }

    public function getTourDaysId(Request $request){
        $tour_id = $request->get('tour_id');
        $tour_day_date = $request->get('tour_day_date');

        $tour_day_id = TourDay::query()->where('tour', $tour_id)
            ->whereDate('date', $tour_day_date)->first();

        return response()->json($tour_day_id->id);
    }
    
    public function checkTourDayConfirmedHotel(Request $request){
        $tour_day_id = $request->get('tour_day_id');
        $res = '';
        
        $tourDay = TourDay::find($tour_day_id);
        
        $packagesTourDays = DB::table('packages_tour_days')->where('tour_day_id', $tour_day_id)->get();
        
        foreach($packagesTourDays as $packagesTourDay){
            $tourPackage = TourPackage::find($packagesTourDay->tour_package_id);
            
            if($tourPackage == null){
                continue;
            }
//            if (($tourPackage->status == 23 || $tourPackage->status == 9) && $tourPackage->type == 0){
            if ($tourPackage->type == 0 && $tourPackage->name){
                if(($tourPackage->status == 23 || $tourPackage->status == 9)){
                    $res .= '<b>' . $tourPackage->name . '</b> - confirmed at this day';
                }
            }
        }
           
        return response()->json($res);
    }
    
    /**
     * Display a listing of the resource.
     *
     * @return  \Illuminate\Http\Response
     */
    public function index()
    {
        // Cache key for tour data
        $cacheKey = 'tours_index_data_' . md5(request()->getQueryString());

        // Use cache to improve performance (cache for 5 minutes)
        $data = \Cache::remember($cacheKey, 300, function () {

            // Single optimized query to get all tours with proper eager loading
            $allTours = Tour::with([
                'users:id,name',
                'status:id,name,color',
                'city_begin:id,name',
                'city_end:id,name',
                'client:id,name' // Include client relationship
            ])
            ->select('id', 'name', 'departure_date', 'external_name', 'status', 'responsible', 'client_id', 'city_begin', 'city_end')
            ->orderBy('departure_date', 'desc')
            ->get();

            // Pre-load all responsible users to avoid N+1 queries
            $responsibleUserIds = $allTours->pluck('responsible')->filter()->unique();
            $responsibleUsers = \App\User::whereIn('id', $responsibleUserIds)
                ->select('id', 'name')
                ->get()
                ->keyBy('id');

            // Process tours once and partition into different collections
            $processedTours = $allTours->map(function($tour) use ($responsibleUsers) {
                // Cache responsible user name
                $tour->responsible_user_names = isset($responsibleUsers[$tour->responsible])
                    ? $responsibleUsers[$tour->responsible]->name
                    : '';

                // Cache assigned user names
                $tour->assigned_user_names = $tour->users->pluck('name')->implode(' | ');

                // Cache client name if exists
                $tour->client_name = $tour->client ? $tour->client->name : '';

                return $tour;
            });

            // Partition tours efficiently using collections
            $tours = $processedTours->whereNotIn('status', [46, 6, 39]);
            $clientTours = $processedTours->where('client_id', '!=', null)->where('client_id', '!=', 0);
            $monthlyChartTours = $processedTours->where('status', 4);
            $cancelledChartTours = $processedTours->where('status', 46);
            $archivedTours = $processedTours->whereIn('status', [6, 39]);

            // Get years efficiently
            $years = Tour::selectRaw('YEAR(departure_date) as year')
                ->whereNotNull('departure_date')
                ->distinct()
                ->orderBy('year', 'desc')
                ->pluck('year')
                ->toArray();

            return compact('tours', 'clientTours', 'monthlyChartTours', 'cancelledChartTours', 'archivedTours', 'years');
        });

        // Generate months array (this is fast, no need to cache)
        $months = [];
        for ($month = 1; $month <= 12; $month++) {
            $date = Carbon::create(null, $month, 1);
            $months[$month] = $date->formatLocalized('%B');
        }

        $title = 'Tour';

        return view('tour.index', array_merge($data, compact('title', 'months')));
    }
/**
 * Show the form for creating a new resource.
 *
 * @return \Illuminate\Http\Response
 */
public function create(Request $request)
{
    $title = 'Create Tour';
    $subTitle = 'Add a new tour';
    $isQuotation = $request->get('quotation', false); // Get from request parameter
    $hotels = Hotel::all()->pluck('name', 'id')->toArray();
    $events = Event::all()->pluck('name', 'id')->toArray();
    $restaurants = Restaurant::all()->pluck('name', 'id')->toArray();
    $guides = Guide::all()->pluck('name', 'id')->toArray();
    $users = User::all();
    $statuses = Status::query()->where('type', 'tour')->orderBy('sort_order', 'asc')->get();
    $serviceTypes = [
        0 => 'Hotel',
        1 => 'Restaurant',
        2 => 'Guide',
        3 => 'Event'
    ];

    $room_types = RoomTypes::query()->orderBy('sort_order', 'ASC')->get();
    $options = $this->services;

    // Initialize empty selected_room_types for create form
    $selected_room_types = [];

    return view('tour.create', compact(
        'title',
        'subTitle',
        'isQuotation', // Add this line
        'hotels',
        'events',
        'restaurants',
        'guides',
        'serviceTypes',
        'users',
        'statuses',
        'options',
        'room_types',
        'selected_room_types'
    ));
}
    /**
     * Show the form for creating a new resource.
     *
     * @return  \Illuminate\Http\Response
     */
// Add these methods to your TourController class

/**
 * Handle file upload for tours
 */
private function addFile($request, $tour)
{
    if ($request->hasFile('attach')) {
        $files = $request->file('attach');
        
        if (!is_array($files)) {
            $files = [$files];
        }

        foreach ($files as $file) {
            if ($file->isValid()) {
                $destinationPath = 'uploads/attachments/';
                $fileName = time() . '_' . $file->getClientOriginalName();
                $filePath = $file->move(public_path($destinationPath), $fileName);
                
                $attachment = new Attachment();
                $attachment->url = url($destinationPath . $fileName);
                $attachment->path = $destinationPath . $fileName;
                $attachment->original_name = $file->getClientOriginalName();
                $attachment->save();
                
                $tour->attachments()->save($attachment);
            }
        }
    }
}

/**
 * Handle image upload for landing page
 */
private function handleLandingPageImage($request, $tour)
{
    if ($request->hasFile('files')) {
        $files = $request->file('files');

        if (!is_array($files)) {
            $files = [$files];
        }

        foreach ($files as $file) {
            if ($file && $file->isValid()) {
                // Generate unique filename
                $originalName = $file->getClientOriginalName();
                $extension = $file->getClientOriginalExtension();
                $uniqueName = time() . '_' . uniqid() . '.' . $extension;

                // Store file using Laravel's default storage
                $storedPath = $file->storeAs('uploads/tours', $uniqueName, 'public');

                if ($storedPath) {
                    // Remove existing attachment if any
                    $existingAttachment = $tour->attachments()->first();
                    if ($existingAttachment) {
                        // Delete old file
                        if (file_exists(storage_path('app/public/' . $existingAttachment->path))) {
                            unlink(storage_path('app/public/' . $existingAttachment->path));
                        }
                        $existingAttachment->delete();
                    }

                    // Create new attachment
                    $attachment = new Attachment();
                    $attachment->url = asset('storage/' . $storedPath);
                    $attachment->path = $storedPath;
                    $attachment->original_name = $originalName;
                    $attachment->mime_type = $file->getMimeType();
                    $attachment->size = $file->getSize();
                    $attachment->save();

                    $tour->attachments()->save($attachment);
                }
            }
        }
    }
}

/**
 * Updated store method with proper file handling
 */
public function store(StoreTourRequest $request)
{
    $request['pax'] = $request->get('pax') == null ? 0 : $request->get('pax', 0);
    $request['pax_free'] = $request->get('pax_free') == null ? 0 : $request->get('pax_free', 0);

    // Validation is now handled by StoreTourRequest

    $responsible_user = (int) $request->get('responsible_user', 0);
    $data = ['departure_date' => $request->departure_date, 'retirement_date' => $request->retirement_date];
    $dateRange = $this->findDateRange($data);
    if (!$dateRange) return back();

    if($request->assigned_user == 'null' && !$request->is_quotation){
        $requestData = $request;
        $requestData['assigned_user'] = '';
        $this->validate($requestData, [
            'assigned_user' => 'required'
        ]);
    }

    $request = CitiesHelper::setCityBegin($request);
    $request = CitiesHelper::setCityEnd($request);
    
    $departure_date = $request->departure_date;

    // Validate and parse departure_date safely
    try {
        if (!empty($departure_date) && preg_match('/^\d{4}-\d{2}-\d{2}$/', $departure_date)) {
            $departure_date = Carbon::createFromFormat('Y-m-d', $departure_date);
            $formattedDate = $departure_date->format('md');
        } else {
            // Fallback to current date if departure_date is invalid
            $departure_date = Carbon::now();
            $formattedDate = $departure_date->format('md');
        }
    } catch (Exception $e) {
        // Fallback to current date if Carbon parsing fails
        $departure_date = Carbon::now();
        $formattedDate = $departure_date->format('md');
    }

    $tour_name = $request->name." #".$formattedDate;

    DB::beginTransaction();
    
    try {
        $tour = new Tour();
        $tour->name = $tour_name;
        $tour->overview = $request->overview;
        $tour->remark = $request->remark;
        $tour->departure_date = $request->departure_date;
        $tour->retirement_date = $request->retirement_date;
        $tour->pax = $request->pax;
        $tour->pax_free = $request->pax_free;
        $tour->total_amount = $request->total_amount == null ? 0 : $request->total_amount;
        $tour->price_for_one = $request->price_for_one == null ? 0 : $request->price_for_one;
        $tour->itinerary_tl = $request->itinerary_tl;
        $tour->country_begin = $request->country_begin;
        $tour->city_begin = $request->city_begin;
        $tour->country_end = $request->country_end;
        $tour->city_end = $request->city_end;
        $tour->invoice = $request->invoice;
        $tour->ga = $request->ga;
        $tour->author = Auth::user()->id;
        $tour->status = $request->status;
        $tour->is_quotation = $request->is_quotation;
        $tour->responsible = $responsible_user;
        $tour->phone = $request->phone == null ? '' : $request->phone;
        $tour->save();

        $tour->external_name = $this->generateExternalName($request->country_begin, $tour->id);
        $tour->save();
        
        // Handle children data - only if provided
        $childCount = $request->input('child_count', 0);
        if ($childCount > 0) {
            $ages = $request->input('ages', []);
            $prices = $request->input('prices', []);

            for ($i = 0; $i < $childCount; $i++) {
                $passenger = new Childrens();
                $passenger->age = $ages[$i] ?? 0;
                $passenger->price = $prices[$i] ?? 0;
                $passenger->tour_id = $tour->id;
                $passenger->save();
            }
        }

        // Handle room types - only if provided
        if ($request->get('room_types_qty')) {
            $room_types_count = collect($request->get('room_types_qty'));

            foreach ($room_types_count as $key => $item) {
                if($item) {
                    $create_tour_type = new TourRoomTypeHotel();
                    $create_tour_type->room_type_id = $key;
                    $create_tour_type->tour_id = $tour->id;
                    $create_tour_type->count = $item;
                    $create_tour_type->save();
                }
            }
        }

        // Handle assigned users
        if ($request->assigned_user) {
            $a_users = explode(',',$request->assigned_user);
            $tour->users()->sync($a_users);

            foreach ($a_users as $user) {
                $notification = Notification::create(['content' => "New tour {$tour->name}", 'link' => '/tour/'.$tour->id]);
                $user = User::findOrFail($user);
                $user->notifications()->attach($notification);
            }
        }

        // Create tour dates
        if ($tour) {
            $data = ['departure_date' => $tour->departure_date, 'retirement_date' => $tour->retirement_date];
            $dateRange = $this->findDateRange($data);
            $this->createUpdateTourDates($tour->id, $dateRange);
        }

        // Handle file uploads - only if files are provided
        if ($request->hasFile('attach')) {
            $this->addFile($request, $tour);
        }
        
        if ($request->hasFile('files')) {
            $this->handleLandingPageImage($request, $tour);
        }

        DB::commit();

        LaravelFlashSessionHelper::setFlashMessage("Tour {$tour->name} created", 'success');

        if($request->get('modal_create_tour') == 1) {
            $data = ['route' => url('home')];
        } else {
            $data = ['route' => route('tour.show', ['tour' => $tour->id])];
        }

        return response()->json($data);
        
    } catch (\Exception $e) {
        DB::rollback();
        return response()->json(['error' => 'Failed to create tour: ' . $e->getMessage()], 500);
    }
}

    public function generateExternalName($country_code, $id){
        return 'EETS' . $country_code . (100 + $id);
    }

    /**
     * Display the specified resource.
     *
     * @param    \Illuminate\Http\Request $request
     * @param    int $id
     * @return  \Illuminate\Http\Response
     */
    public function show($tourId, Request $request)
    {
        if ($request->notification_click){
            $notification = Notification::find($request->notification_click);
            $notification->click = true;
            $notification->save();
        }


        if ($request->ajax()) {
            return URL::to('tour/' . $tourId);
        }

        $tour = Tour::findOrfail($tourId);
	    $title = $tour->is_quotation ? 'Quotation' :  'Tour';
        if($tour == null){
            return abort(404);
        }

        $tasksId = '';
//        $tasks = $this->taskRepository->allForAssigned(Auth::user()->getAuthIdentifier());
        $tasks = $this->taskRepository->allForAssignedToTour(Auth::user()->getAuthIdentifier(), $tour->id);

        $tasksData = [];
        if($tasks){
            $listIdTasks = array();

            foreach ($tasks as $task) {
                $listIdTasks[] = $task->id;
            }

            $tasksId = implode(",", $listIdTasks);

            // Get formatted tasks data for direct display
            $tasksData = $this->getFormattedTasksData($listIdTasks);
        }

        $status = Status::where('id', $tour->status)->first();
        if (!$status) {
            // Create a default status object if none found
            $status = new \stdClass();
            $status->name = 'Unknown Status';
        }
        $files = $this->parseAttach($tour);
        $tourDates = $this->prepareTourPackages($tour, $request)['tourDates'];

        $listIdServices = array();
        //$this->services = array("Event","Guide","Hotel","Restaurant","Transfer");
        //$listRoomsHotel = array();

        $listRoomsHotel = TourRoomTypeHotel::where('tour_id', $tour->id )->get();

        foreach ($tourDates as $tourDate){
            foreach ($tourDate->packages as $package){
                /*
                foreach ($package->room_types_hotel as $item){
                    $listRoomsHotel[] = $item;
                }*/
                $listIdServices[] = ['id_service' => $package->reference, 'type_service' => $package->type];
     
            }
        }

               $quotation_id = $tour->quotations()->first()->id??0;
        

       $quotation = Quotation::find($quotation_id);

        $comparison = Comparison::where(['id' => $quotation_id])->first();

        if (!$comparison) {
            $newComparison = new Comparison();
            $newComparison->id = $quotation_id;
            $newComparison->save();
            $comparison = $newComparison;
        }
       $this->syncComparisonRows($comparison);
        
       // dd($comparison->comparisonRowByDate("2018-04-02")->id);

 $select_office=Offices::where('status',1)->first();
		 $offices=Offices::all();

        // Fetch invoices data directly for Bootstrap table
        $invoice_tours = \App\InvoicesTours::where("invoices_tours_id", $tour->id)->get();
        $invoicesData = [];
        foreach ($invoice_tours as $invoice_tour) {
            $invoice = \App\Invoices::find($invoice_tour->invoices_id);
            if ($invoice) {
                $office = \App\Offices::find($invoice->office_id);
                $package = \App\TourPackage::find($invoice_tour->package_id);

                // Calculate invoice status
                $transaction = \App\Transaction::where("invoice_id", $invoice->id)->where("pay_to", "Supplier");
                $sum_amount = $transaction->sum("amount");
                $amount = $invoice->total_amount;
                $remaining_amount = $amount - $sum_amount;
                if ($sum_amount == $amount) {
                    $invoiceStatus = "Paid";
                } elseif ($sum_amount == 0) {
                    $invoiceStatus = "You Owe " . $amount;
                } else {
                    $invoiceStatus = "You Owe " . $remaining_amount;
                }

                $invoicesData[] = [
                    'id' => $invoice->id,
                    'office_name' => $office->office_name ?? '',
                    'invoice_no' => $invoice->invoice_no ?? '',
                    'due_date' => $invoice->dueDate ?? '',
                    'received_date' => $invoice->receivedDate ?? '',
                    'total_amount' => $invoice->total_amount ?? '',
                    'extra_amount' => $invoice->extra_amount ?? '',
                    'amount_payable' => $invoice->amount_payable ?? '',
                    'tour_name' => $tour->name,
                    'package_name' => $package->name ?? 'Extra Cost',
                    'status' => $invoiceStatus
                ];
            }
        }

        // Fetch billing data directly for Bootstrap table
        $transactions = \App\ClientInvoices::where("tour_id", $tour->id)->get();
        $billingData = [];
        foreach ($transactions as $transaction) {
            $office = \App\Offices::find($transaction->office_id);
            $tour_obj = \App\Tour::find($transaction->tour_id);

            // Calculate total amount for this tour
            $transactions_cust = \App\ClientInvoices::where("tour_id", $transaction->tour_id)->get();
            $total = 0;
            foreach ($transactions_cust as $transaction_cust) {
                $total = $transaction_cust->total_amount + $total;
            }

            $billingData[] = [
                'id' => $transaction->id,
                'office_name' => $office->office_name ?? '',
                'tour_name' => $tour_obj->name ?? '',
                'total_amount' => $total,
                'date' => $transaction->created_at ?? $transaction->date ?? now()
            ];
        }

        return view('tour.show', [
			'select_office'=>$select_office,
			'offices'=>$offices,
            'title' => $title,
            'tour' => $tour,
            'files' => $files,
            'status' => $status,
            'listIdTasks' => $tasksId,
            'tasksData' => $tasksData,
            'tourDates' => $tourDates,
            'options' => $this->services,
            'listRoomsHotel' => $listRoomsHotel,
            'dvoTourDates' => empty($locations['dvoTourDates']) ? false : $locations['dvoTourDates'],
            'routes' => empty($locations['routes']) ? false : json_encode($locations['routes']),
            'quotation' => $quotation,
            'comparison'  => $comparison,
            'invoicesData' => $invoicesData,
            'billingData' => $billingData
        ]);
    }


    public function prepareTourPackages($tour, Request $request)
    {
        $tourDates = TourDay::with('packages')->where('tour', $tour->id)->get()->sortBy('date');
        //echo (count($tourDates))."<br>";
        $tourPackageType = TourService::$serviceTypes;
        $last = '';

        foreach ($tourDates as $tourDate) {
            if ($request->pdf_type == 'voucher') $tourDate->packages = $tourDate->packages->where('description_package', null);
            $last_package = $tourDate->packages->last();
            if($last_package) $last = $last_package->id;
            foreach ($tourDate->packages as $package) {
            	if ($package->status) {
		            $package->status = $package->getStatusName();
	            }
                $package->paid = $package->paid ? 'Yes' : 'No';
                // $package->type = $tourPackageType[$package->type];
                $package->issued_by = $request->user()->name;
                // $package->assigned_user = User::findOrFail($tour->assigned_user)->name;
            }
        }
        if($request->input('exclude') > 0 && $request->pdf_type  !== 'voucher' ) {
            foreach ($tourDates as $tourDate) {

                if ($request->pdf_type == 'voucher') $tourDate->packages = $tourDate->packages->where('description_package', null);

                foreach ($tourDate->packages as $id => $package) {
                    if (in_array($package->id, $request->input('exclude'))) {
                        unset($tourDate->packages[$id]);
                    }
                }

            }
        }

        return ['tourDates' => $tourDates, 'tour' => $tour, 'last' => $last];
    }
    /**
     * prepare data for pdf export
     * @param  int $id tour id
     * @return \Illuminate\Http\Response
     */
    public function pdfData($id, Request $request)
    {
        $tour = Tour::findOrfail($id);
        $dayFrom = !empty($tour->departure_date) && preg_match('/^\d{4}-\d{2}-\d{2}$/', $tour->departure_date)
            ? Carbon::createFromFormat('Y-m-d', $tour->departure_date)
            : Carbon::now();
        $dayTo = !empty($tour->retirement_date) && preg_match('/^\d{4}-\d{2}-\d{2}$/', $tour->retirement_date)
            ? Carbon::createFromFormat('Y-m-d', $tour->retirement_date)
            : Carbon::now();
        $diff = $dayFrom->diffInDays($dayTo);
        $tour->tourCode = "$tour->name#$dayFrom->month$dayTo->day-$diff-D";
        $data = $this->prepareTourPackages($tour, $request);
        return response()->json(json_encode($data));
    }
    public function pdfExport(Request $request, $id)
    {
        $tour = Tour::findOrFail($id);
        $dayFrom = !empty($tour->departure_date) && preg_match('/^\d{4}-\d{2}-\d{2}$/', $tour->departure_date)
            ? Carbon::createFromFormat('Y-m-d', $tour->departure_date)
            : Carbon::now();
        $dayTo = !empty($tour->retirement_date) && preg_match('/^\d{4}-\d{2}-\d{2}$/', $tour->retirement_date)
            ? Carbon::createFromFormat('Y-m-d', $tour->retirement_date)
            : Carbon::now();
        $diff = $dayFrom->diffInDays($dayTo);
        $tour->tourCode = "$tour->name#$dayFrom->month$dayTo->day-$diff-D";
        $data = $this->prepareTourPackages($tour, $request);
        if ($request->pdf_type  === 'hotels'){
            return $pdf = exportPdfHotels($tour, $data,$request);
        }
        if ($request->pdf_type === 'hotel'){
            return $this->exportPdfHotels($tour, $data,$request);
        }
        return $pdf = ($request->pdf_type === 'voucher') ?
            $this->exportPdfVoucher($tour, $data,$request) :
            $this->exportPdfShort($tour, $data,$request);
    }

    public function htmlExport(Request $request, $id)
    {
        $tour = Tour::findOrFail($id);
        $dayFrom = !empty($tour->departure_date) && preg_match('/^\d{4}-\d{2}-\d{2}$/', $tour->departure_date)
            ? Carbon::createFromFormat('Y-m-d', $tour->departure_date)
            : Carbon::now();
        $dayTo = !empty($tour->retirement_date) && preg_match('/^\d{4}-\d{2}-\d{2}$/', $tour->retirement_date)
            ? Carbon::createFromFormat('Y-m-d', $tour->retirement_date)
            : Carbon::now();
        $diff = $dayFrom->diffInDays($dayTo);
        $tour->tourCode = "$tour->name#$dayFrom->month$dayTo->day-$diff-D";
        $data = $this->prepareTourPackages($tour, $request);
        return $pdf = $this->exportHtmlShort($tour, $data,$request);
    }

    public function landingPage(Request $request, $id)
    {
		
        $tour = Tour::findOrFail($id);
		
        $dayFrom = !empty($tour->departure_date) && preg_match('/^\d{4}-\d{2}-\d{2}$/', $tour->departure_date)
            ? Carbon::createFromFormat('Y-m-d', $tour->departure_date)
            : Carbon::now();
        $dayTo = !empty($tour->retirement_date) && preg_match('/^\d{4}-\d{2}-\d{2}$/', $tour->retirement_date)
            ? Carbon::createFromFormat('Y-m-d', $tour->retirement_date)
            : Carbon::now();
        $diff = $dayFrom->diffInDays($dayTo);
        $tour->tourCode = "$tour->name#$dayFrom->month$dayTo->day-$diff-D";
//        $data = $this->prepareTourPackages($tour, $request);
        $attachmenttypes = \App\Attachmenttype::all();
        
        $serviceTypes = [
                    'hotel',
                    'event',
                    'guide',
                    'transfer',
                    'restaurant',
                    'tourPackage',
                    'cruise',
                    'flight'
                ];
        $tourDays = TourDay::where('tour', $tour->id)->get()->sortBy('date');
        
        $tourTransfers = $tour->transfers;

        $exclude = [];
        if(!empty($request->input('exclude'))){
        if(count($request->input('exclude')) > 0 ) {
            $exclude = $request->input('exclude');
        } else  {
            $exclude = [];
        }  
        }
		
        $usersResponsible = User::find($tour->responsible);

        $listRoomsHotel = \App\TourRoomTypeHotel::where('tour_id', $tour->id )->get();        
        
        view()->share([ 'tour' => $tour, 
                        'serviceTypes' => $serviceTypes,
                        'tourDays' => $tourDays,
                        'tourTransfers' => $tourTransfers,
                        'usersResponsible' => $usersResponsible,
                        'listRoomsHotel' => $listRoomsHotel,
                        'attachmenttypes' => $attachmenttypes,
                        'exclude' => $exclude ]);
        return view('export.landing_page');
    }

    /**
     * export specific tour for excel or pdf
     * @param  int $id tour id
     * @param  string $export document type
     * @return \Illuminate\Http\Response
     */
    public function export(int $id, string $export, string $type = null, Request $request )
    {
        $tour = Tour::findOrFail($id);

        // dd($tour);
        if ($export == 'csv') {

            $this->csvExport($tour, $type , $request);
        } else $this->prepareExport($tour, $export ,$request);

        return back();
    }

    /**
     * Show the form for editing the specified resource.
     * @param    \Illuminate\Http\Request $request
     * @param    int $id
     * @return  \Illuminate\Http\Response
     */
    public function edit($tour, Request $request)
    {

        $title = 'Edit - tour';
        if ($request->ajax()) {
            return URL::to('tour/' . $tour . '/edit');
        }
        $tour = Tour::findOrfail($tour);

        $hotels = Hotel::all()->pluck('name', 'id')->toArray();
        $events = Event::all()->pluck('name', 'id')->toArray();
        $restaurants = Restaurant::all()->pluck('name', 'id')->toArray();
        $guides = Guide::all()->pluck('name', 'id')->toArray();
        $users = User::all();
        foreach ($users as $user) {
            foreach ($tour->users as $t_user) {
                if ($user->id == $t_user->id) $user->selected = true;
            }
        }
        $statuses = Status::query()->where('type', 'tour')->orderBy('sort_order', 'asc')->get();
        $serviceTypes = [
            0 => 'Hotel',
            1 => 'Restaurant',
            2 => 'Guide',
            3 => 'Event'
        ];

        $calendar_edit = null;
        if($request->has('calendar_edit')){
            $calendar_edit = $request->get('calendar_edit');
        }

        $room_typesDvo = RoomTypes::query()->orderBy('sort_order', 'ASC')->get();
        $room_types = array();

        foreach ($room_typesDvo as $room_type){
            $room_type['count_room'] = null;
            $room_type['price_room'] = null;
            $room_types[] = $room_type;
        }

        $room_types_all = TourRoomTypeHotel::where('tour_id', $tour )->get();

        $selected_room_types = array();

        foreach( $room_types_all as $item){
            $item->room_types['count_room'] = $item->count;
            $item->room_types['price_room'] = $item->price;
            $selected_room_types[] = $item->room_types;
        }

        $options = $this->services;
        return view('tour.edit', compact(
            'title',
            'tour',
            'hotels',
            'events',
            'restaurants',
            'guides',
            'serviceTypes',
            'users',
            'statuses',
            'options',
            'room_types',
            'selected_room_types',
            'calendar_edit'
        ));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param    \Illuminate\Http\Request $request
     * @param    int $id
     * @return array
     */
    public function update($tour, UpdateTourRequest $request)
    {

        if($request->ajax() && $request->cityName){
            $city = City::where('code', $request->fieldValue)->first() ?? City::create([ 'code' => $request->fieldValue, 'name' => $request->cityName, 'country' => $request->countryAlias]);

            $tour_model = Tour::find($tour);
            $tour_model[$request->fieldName] = $city->id;
            $tour_model->save();
			
			
            return response($tour);
        }
        if ($request->ajax() && $request->fieldName){
            $tour_model = Tour::find($tour);
            if ($request->fieldName == 'departure_date' && $tour_model->retirement_date < $request->fieldValue) return response('wrong date');
            if ($request->fieldName == 'retirement_date' && $tour_model->departure_date > $request->fieldValue) return response('wrong date');
            if ($request->fieldName == 'departure_date'){
                $data = $this->findDateRange(['departure_date' => $request->fieldValue, 'retirement_date' => $tour_model->retirement_date]);
                if (!$data) return response('wrong date');
            }
            if ($request->fieldName == 'retirement_date'){
                $data = $this->findDateRange(['departure_date' => $tour_model->departure_date, 'retirement_date' => $request->fieldValue]);
                if (!$data) return response('wrong date');
            }


            if ($request->fieldName == 'status' &&  $request->fieldValue == 39) { //Tour Confirmed

                $tourDatesAll = TourDay::where('tour', $tour)->whereNull('deleted_at')->orderBy('date')->get();
                $count_result = 0;
                $count_transfer_item  = 0;
                $transfers_tour = TourPackage::query()->where('tour_id', $tour)->get();

                //is transfer confirmed
                foreach ($transfers_tour as $item){
                   ($item->status == 17) ? $count_transfer_item++ : $count_transfer_item-- ;
                }

                foreach ($tourDatesAll as $tourDate) {
                        $count_statuses = 0;
                       foreach ($tourDate->packages as $package) {
                           //is confirmed hotel and confirmed service
                         if ($package->status == 23 || $package->status == 9 )  $count_statuses++ ;
                       }

                      if(count($tourDate->packages) == $count_statuses && $count_statuses > 0) $count_result++;

                }

                if (count($tourDatesAll) == $count_result &&  count($transfers_tour) == $count_transfer_item ) {
                    $tour->status = 39;
                    $tour->save();
					
                    $status = Status::query()->where('id', $tour->status)->first();
                    $status_name = $status ? $status->name : '';
                    $url = route('tour.show', ['id' => $tour->id]);
                    $parsingURL = parse_url($url);
                    $uri = $parsingURL['path'];

                    if ($tour->users) {
                        foreach ($tour->users as $user) {
                            $notification = Notification::query()->create(
                                ['content' => "Tour {$tour->name} changed status to {$status_name}",
                                    'link' => $uri]);
                            $user->notifications()->attach($notification);
                        }
                    }

                }else{

                    $data = ['status_error' => 'Not all services are confirmed'];


                    return response()->json($data);
                }

          }else {
                $status = Status::query()->where('id', $request->fieldValue)->first();
                $status_name = $status ? $status->name : '';
                $url = route('tour.show', ['id' => $tour->id]);
                $parsingURL = parse_url($url);
                $uri = $parsingURL['path'];

                if ($tour->users) {
                    foreach ($tour->users as $user) {
                        $notification = Notification::query()->create(
                            ['content' => "Tour {$tour->name} changed status to {$status_name}",
                                'link' => $uri]);
                        $user->notifications()->attach($notification);
                    }
                }

                $tour[$request->fieldName] = $request->fieldValue;
                $tour->save();
				
          }

          return response($tour);
        }

        $transfers_tour = TourPackage::query()->where('tour_id', $tour)->get();
        foreach ($transfers_tour as $item){
            if($item->time_to > (new Carbon($request->retirement_date))->addHours(20)){
                $data = [
                    'transfer_fail' => true,
                    'error_message_transfer' => trans('main.Youcannotchangethedatebecauseyou')
                ];

                return response()->json($data);
            }else{
                continue;
            }
        }
        $request['pax'] = $request->get('pax') == null ? 0 : $request->get('pax', 0);
        $request['pax_free'] = $request->get('pax_free') == null ? 0 : $request->get('pax_free', 0);
   
        if($request->assigned_user == 'null'){
            $requestData = $request;
            $requestData['assigned_user'] = '';
            $this->validate($requestData, [
                'assigned_user' => 'required'
            ]);
        }
   
        $this->validate($request, [
            'name'            => 'required',
            'departure_date'  => 'required|before_or_equal:retirement_date',
            'retirement_date' => 'required',
            'pax'             => 'numeric',

        ]);
        $responsible_user = (int) $request->get('responsible_user', 0);
        $range = $this->findDateRange(['departure_date' => $request->departure_date, 'retirement_date' => $request->retirement_date]);
        if (!$range){

            $data = [
                'range_data' => true,
                'error_message' => trans('main.Perioddontshouldbebiggestthan')
            ];

            return response()->json($data);
        }
        $request = CitiesHelper::setCityBegin($request);
        $request = CitiesHelper::setCityEnd($request);
        DB::beginTransaction();
        $tour_model = Tour::findOrfail($tour);
			$departure_date= $request->departure_date;
        try {
            if (!empty($departure_date) && preg_match('/^\d{4}-\d{2}-\d{2}$/', $departure_date)) {
                $departure_date = Carbon::createFromFormat('Y-m-d', $departure_date);
            } else {
                $departure_date = Carbon::now();
            }
        } catch (Exception $e) {
            $departure_date = Carbon::now();
        }
		$formattedDate = $departure_date->format('md');
		$modifiedString = preg_replace('/#\d+$/', '', $request->name);
		$tour_name = $modifiedString." #".$formattedDate;
        $tour->name = $tour_name;
        $tour->overview = $request->overview;
        $tour->remark = $request->remark;
        $tour->departure_date = $request->departure_date;
        $tour->retirement_date = $request->retirement_date;
        $tour->pax = $request->pax;
        $tour->pax_free = $request->pax_free;
    //    $tour->rooms = $request->rooms;
        $tour->country_begin = $request->country_begin;
        $tour->total_amount = $request->total_amount == null ? 0 : $request->total_amount;
        $tour->price_for_one = $request->price_for_one == null ? 0 : $tour->getPriceForOnePaxInTour();
        $tour->city_begin = $request->city_begin;
        $tour->country_end = $request->country_end;
        $tour->city_end = $request->city_end;
        $tour->invoice = $request->invoice;
        $tour->ga = $request->ga;
        $tour->responsible = $responsible_user;
        $tour->author = Auth::user()->id;
        $tour->phone = $request->phone == null ? '' : $request->phone;
        $tour->itinerary_tl = $request->itinerary_tl;

        if ($request->assigned_user) {
            $a_users = explode(',', $request->assigned_user);
			$tour->tasks()->each(function ($task) use ($a_users) {
				// Sync only the new assigned users for each task
				$task->assigned_users()->sync($a_users);
			});
            $tour->users()->sync($a_users);
        }

        if($request->status != $tour->status){
            $url = route('tour.show', ['id' => $tour->id]);
            $parsingURL = parse_url($url);
            $uri = $parsingURL['path'];
            $status = Status::query()->where('id', $request->status)->first();

            if ($tour->users) {
                foreach ($tour->users as $user) {
                    $notification = Notification::query()->create(
                        ['content' => "Tour {$tour->name} changed status to {$status->name}",
                        'link' => $uri]);
                    $user->notifications()->attach($notification);
                }
            }
        }

        if ($request->status == 39) { //Tour Confirmed
            $tourDatesAll = TourDay::where('tour', $id)->whereNull('deleted_at')->orderBy('date')->get();
            $count_result = 0;
            $count_transfer_item  = 0;
            $transfers_tour = TourPackage::query()->where('tour_id', $id)->get();

            //is transfer confirmed
            foreach ($transfers_tour as $item){
                ($item->status == 17) ? $count_transfer_item++ : $count_transfer_item-- ;
            }

            foreach ($tourDatesAll as $tourDate) {
                $count_statuses = 0;
                foreach ($tourDate->packages as $package) {
                    //is confirmed hotel and confirmed service
                    if ($package->status == 23 || $package->status == 9)  $count_statuses++ ;
                }

                if(count($tourDate->packages) == $count_statuses) $count_result++;

            }

            if (count($tourDatesAll) == $count_result &&  count($transfers_tour) == $count_transfer_item) { $tour->status = $request->status; } else{

                $data = [
                    'range_data' => true,
                    'error_message' => 'Not all services are confirmed !'
                ];

                return response()->json($data);
            }

        } else {
            $tour->status = $request->status;
        }



        $tour->save();
        $tour->external_name = $this->generateExternalName($request->country_begin, $tour->id);
        $tour->save();
        DB::commit();

        if ($request->get('room_types_qty')) {

            TourRoomTypeHotel::query()->where('tour_id', $tour->id)->delete();

            $room_types_count = collect($request->get('room_types_qty'));

            foreach ($room_types_count as $key => $item) {
                if($item) {
                    $create_tour_type = new TourRoomTypeHotel();
                    $create_tour_type->room_type_id = $key;
                    $create_tour_type->tour_id = $tour->id;
                    $create_tour_type->count = $item;
                    $create_tour_type->save();
                }
            }
        }



        $data = ['departure_date' => $request->departure_date, 'retirement_date' => $request->retirement_date];
        $dateRange = $this->findDateRange($data);
        if (!$dateRange) {
            $data = [
                'range_data' => true,
                'error_message' => trans('main.Perioddontshouldbebiggestthan')
            ];

            return response()->json($data);
        }
        $this->createUpdateTourDates($tour->id, $dateRange);

        $this->addFile($request, $tour);
		
		Childrens::where('tour_id',$tour->id)->delete();
					// Retrieve child count, ages, and prices from the request
					
						$childCount = $request->input('child_count');
						$ages = $request->input('ages');
						$prices = $request->input('prices');

						// Loop through each child
						for ($i = 0; $i < $childCount; $i++) {
							// Create a new passenger

							$passenger = new Childrens();
							$passenger->age = $ages[$i]??0;
							$passenger->price = $prices[$i]??0;
							$passenger->tour_id = $tour->id; // Associate passenger with the tour
							$passenger->save();

						}
					

        LaravelFlashSessionHelper::setFlashMessage("Tour {$tour->name} edited", 'success');

        if($request->get('calendar_edit') == 1){
            $data = ['route' => url('home')];
        }else if($request->get('tab')){
            $data = ['route' => url('profile?'.$request->get('tab'))];
        }else{
            $data = ['route' => route('tour.show', [ 'id' => $id ])];
        }
		

//        return response()->json(json_encode($data));
		return redirect()->route('tour.show', [ 'id' => $id ]);
        return response()->json($data);
		
    }

    /**
     * Delete confirmation message by Ajaxis.
     *
     * @link      https://github.com/amranidev/ajaxis
     * @param    \Illuminate\Http\Request $request
     * @return  String
     */
    public function DeleteMsg($id, Request $request , $tab = null )
    {
        $tab_url = ($tab) ? '/'. $tab : '';

//        $msg = Ajaxis::BtDeleting('Warning!!', 'Would you like to remove This?', '/tour/' . $id . '/delete'. $tab_url);
        $msg = Ajaxis::BtDeleting(trans('main.Warning').'!!',trans('main.WouldyouliketoremoveThis').'?', '/tour/' . $id . '/delete'. $tab_url);

        if ($request->ajax()) {
            return $msg;
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param    int $id
     * @return  \Illuminate\Http\Response
     */
    public function destroy($id, $tab = null)
    {
        $tour = Tour::findOrfail($id);

        if($tour){
            if ($tour->users) {
                foreach ($tour->users as $user) {
                    $notification = Notification::query()->create(['content' => "Tour {$tour->name} deleted",
                        'link' => null]);
                    $user->notifications()->attach($notification);
                }
            }
        }


        $this->removeFile($tour);
        $tour->delete();
        BusDay::query()->where('tour_id', $id)->delete();
        TransferToDrivers::query()->where('tour_id', $id)->delete();
        TransferToBuses::query()->where('tour_id', $id)->delete();
        Comment::query()->where('reference_type', Comment::$services['tour'])->where('reference_id', $id)->delete();
        Quotation::query()->where('tour_id', $id)->delete();
		ClientInvoices::query()->where('tour_id', $id)->delete();
		InvoicesTours::query()->where('invoices_tours_id', $id)->delete();
		//InvoicesTours::query()->find( $id)->delete();
        LaravelFlashSessionHelper::setFlashMessage("Tour {$tour->name} deleted", 'success');

        if(URL::previous() == route('quotation.index')){
            return URL::to('quotation');
        }
        if($tab){
            return URL::to('profile?tab=history-tours-tab');
        }

        return URL::to('tour');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @return  array
     */
    public function generatePackages($id, Request $request)
    {
        $data = ['departure_date' => $request->departureDate, 'retirement_date' => $request->retirementDate];
        $tour = Tour::findOrfail($id);
        $dateRange = $this->findDateRange($data);
        if (!$dateRange) return null;
        $this->createUpdateTourDates($id, $dateRange);

        $tourDates = TourDay::get(['id', 'date', 'tour'])->where('tour', $id)->sortBy('date');
		
        $arr = array();
        $i = 0;
        foreach ($tourDates as $tourDate){
            $tour_packages = collect();
            foreach ($tourDate->packages as $package_item){
                $package_item['time_from_new'] = (new Carbon($package_item->time_from))->format('H:i:s');
                $tour_packages->push($package_item);
            }
            // dd($tourDate->packages->sortBy('time_from'));
            $tourDate->packages = $tour_packages->sortBy('time_from_new');
            foreach ($tourDate->packages as $package){
                $arr[] = $package->status;
                $package->time_from = $this->convertDateToHoursMinute($package->time_from);
                $package->time_to = $this->convertDateToHoursMinute($package->time_to);
            }
        }
        $statusPackages = Status::query()->whereIn('id', $arr)->orderBy('sort_order')->get();
        $statusesTransfers = Status::query()->orderBy('sort_order', 'asc')->where('type', 'bus')->orderBy('sort_order')->get();

        $view = View::make('tour.packages',['tourDates'=> $tourDates,'id'=> $id,'statusPackage' => $statusPackages,'statusesTransfers' =>$statusesTransfers,'tour' => $tour]);
		
        $contents = $view->render();
        return $contents;
    }
    /**
     * crud for tour dates
     * @param  int $id        tour id
     * @param  DatePeriod $dateRange 
     * @return void            
     */
    public function createUpdateTourDatesss($id, $dateRange)
    {
        $tourDays = TourDay::where('tour', $id)->pluck('date', 'id')->toArray();
        foreach ($dateRange as $date) {
            if (!in_array($date->format('Y-m-d'), $tourDays)) {
                $tourDay = new TourDay();
                $tourDay->date = $date;
                $tourDay->tour = $id;
                $tourDay->save();
            } else {
                $findedKey = array_search($date->format('Y-m-d'), $tourDays);
                unset($tourDays[$findedKey]);
            }
        }

        foreach ($tourDays as $key => $tourDay) {
            TourDay::destroy($key);
        }
    }
	
	public function createUpdateTourDates($tourId, $dateRange)
{
		
		 // Convert the DatePeriod to an array of Carbon objects
    $dateRange = collect(iterator_to_array($dateRange));

		
      // Get existing tour days
    $existingTourDays = TourDay::where('tour', $tourId)->pluck('date')->toArray();

    // Convert the provided date range to an array of date strings
    $newDateStrings = $dateRange->map(function($date) {
        return $date->format('Y-m-d');
    });

    // Check if the new date range is greater, smaller, or equal to the existing one
    $newDatesCount = $newDateStrings->count();
    $existingDatesCount = count($existingTourDays);

     // Check if the new date range is greater than the existing one
    if (count($newDateStrings) > count($existingTourDays)) {
        // Update existing tour days if the new range is greater
        foreach ($existingTourDays as $key => $tourDay) {
            if ($key < $newDateStrings->count()  && is_object($tourDay)) {
				
                $tourDay->date = $newDateStrings[$key];
                $tourDay->save();
            }
        }
        // Create new tour days for additional dates
        for ($i = count($existingTourDays); $i < count($newDateStrings); $i++) {
            $tourDay = new TourDay();
            $tourDay->date = $newDateStrings[$i];
            $tourDay->tour = $tourId;
            $tourDay->save();
        }
    }elseif ($newDatesCount < $existingDatesCount) {
        // If the new range is smaller, update existing tour days and delete remaining ones
       // Update existing tour days up to the length of the new date range
        foreach ($existingTourDays as $key => $tourDay) {
            if ($key < $newDateStrings->count() && is_object($tourDay)) {
                $tourDay->date = $newDateStrings[$key]??"";
                $tourDay->save();
            }
        }
        // Delete remaining tour days
        TourDay::where('tour', $tourId)
            ->whereNotIn('date', $newDateStrings)
            ->delete();
    
    } else {
        // If the ranges are equal, update existing tour days
        foreach ($existingTourDays as $existingDate) {
            if (!$newDateStrings->contains($existingDate)) {
                TourDay::where('tour', $tourId)
                    ->where('date', $existingDate)
                    ->update(['date' => $newDateStrings[array_search($existingDate, $existingTourDays)]]);
            }
        }
    }
}

    public function cloneTour(Request $request, $id)
    {
        $oldTour = Tour::findOrfail($id);
        $oldDates = TourDay::with('packages')->where('tour', $id)->get();
        $oldTransfer = TourPackage::query()->where('id', $oldTour->transfer_id)->first();

        $tour = Tour::query()->where('id', $id)->first();
        $start_date = $request->departure_date;
        $dtFromOld = Carbon::parse($oldTour->departure_date);
        $dtDiff = $dtFromOld->diffInDays(Carbon::parse($oldTour->retirement_date));
        $end_date = Carbon::parse($start_date)->addDays($dtDiff);


        DB::beginTransaction();
        $newTour = $oldTour->replicate();
        $newTour->transfer_id = null;
        $newTour->departure_date = $request->departure_date;
        $newTour->invoice = null;
        $newTour->ga = null;
        $newTour->status = Status::query()->where('type', 'tour')->where('name', 'Pending')->first()->id;
        $dtFromOld = Carbon::parse($oldTour->departure_date);
        $dtDiff = $dtFromOld->diffInDays(Carbon::parse($oldTour->retirement_date));
        $newTour->retirement_date = Carbon::parse($newTour->departure_date)->addDays($dtDiff);
        $newTour->name = "$oldTour->name (clone)";
        // $newTour->assigned_user = $request->user()->id;
        $newTour->push();
        $newTour->users()->attach($request->user()->id);


        $newTour->external_name = $this->generateExternalName($newTour->getAttributes()['country_begin'], $newTour->id);
        $newTour->save();
        DB::commit();
        $data = ['departure_date' => $newTour->departure_date, 'retirement_date' => $newTour->retirement_date];
        $dateRange = $this->findDateRange($data);
        $this->createUpdateTourDates($newTour->id, $dateRange);
        $this->cloneTourDays($oldDates, $newTour->id);

        if(!empty($oldTransfer)){
            $newTransfer = $oldTransfer->replicate();
            $newTransfer->description = null;
            $newTransfer->paid = 0;
            $newTransfer->save();
            $newTour->transfer_id = $newTransfer->id;
            $newTour->save();
        }


        LaravelFlashSessionHelper::setFlashMessage("Tour {$tour->name} cloned", 'success');

        return redirect(route('tour.edit', ['id' => $newTour->id]));
    }
    /**
     * clone tour days with services
     * @param  $oldDates old tour days
     * @param  int    $tourId   new tour id
     * @return void
     */
    public function cloneTourDays($oldDates, int $tourId)
    {
//        $newDays = TourDay::with('packages')->where('tour', $tourId)->get();
//        $sum = count($oldDates);
//        $status_service = Status::query()
//            ->where('type', 'service_in_tour')
//            ->where('name', 'Requested')->first()->id;
//        
//$parent_id_ = null;            
//        for($i = 0; $i < $sum; $i++){
//            if(!$sum_packages = count($oldDates[$i]['packages'])) continue;
//            // dd($oldDates[$i]['packages']);
//            for($k = 0; $k < $sum_packages; $k++){
//                $package = $oldDates[$i]['packages'][$k];
//                $newPackage = $package->replicate();
//                $newPackage->status = $status_service;
//                $newPackage->description = $package->description_package ? $package->description : null;
//                $newPackage->paid = 0;
//                $newPackage->parent_id = $package->parent_id ? $parent_id_ : null;
//                $serviceDate = Carbon::parse($newDays[$i]["date"])->format('Y-m-d');
//                $serviceTime = Carbon::parse($package['time_from'])->format('H:i:s');
//                $serviceTimeTo = Carbon::parse($package['time_to'])->format('H:i:s');
//                $time_from = "$serviceDate $serviceTime";
//                $time_to = "$serviceDate $serviceTimeTo";
//                $newPackage->time_from = $time_from;
//                $time_to = $this->findNewDate($package['time_from'], $package['time_to'], $time_to);
//                $newPackage->time_to = $time_to;
//                $newPackage->push();
//                $newPackage->assignTourDay($newDays[$i]);
//if ( !$package->parent_id ){
//    $parent_id_ = $newPackage->id;
//}
//            }
//        }
//        
        $newDays = TourDay::with('packages')->where('tour', $tourId)->get();
        $sum = count($oldDates);
        $status_service = Status::query()
            ->where('type', 'service_in_tour')
            ->where('name', 'Requested')->first()->id;
        
        $parent_id_ = [];            
        for($i = 0; $i < $sum; $i++){
            if(!$sum_packages = count($oldDates[$i]['packages'])) continue;
            for($k = 0; $k < $sum_packages; $k++){
                $package = $oldDates[$i]['packages'][$k];
                $newPackage = $package->replicate();

                $newPackage->status = $status_service;
                $newPackage->description = $package->description_package ? $package->description : null;
                $newPackage->paid = 0;
                $serviceDate = Carbon::parse($newDays[$i]["date"])->format('Y-m-d');
                $serviceTime = Carbon::parse($package['time_from'])->format('H:i:s');
                $serviceTimeTo = Carbon::parse($package['time_to'])->format('H:i:s');
                $time_from = "$serviceDate $serviceTime";
                $time_to = "$serviceDate $serviceTimeTo";
                $newPackage->time_from = $time_from;
                $time_to = $this->findNewDate($package['time_from'], $package['time_to'], $time_to);
                $newPackage->time_to = $time_to;
                $newPackage->push();
                $newPackage->assignTourDay($newDays[$i]);
                if($package->parent_id == null){
                    $parent_id_[$package->id] = $newPackage->id;
                } else{
                    if ($newPackage->parent_id) {
                        $newPackage->parent_id = $parent_id_[$newPackage->parent_id];
                        $newPackage->save();
                    }
                }
            }
        }
    }

    public function countryList(Request $request)
    {
        return response()->json(Country::select('name', 'alias', 'id')->get()->toJson());
    }

    public function statusList(Request $request)
    {
        return response()->json(Status::select('name', 'id')->orderBy('sort_order', 'asc')->where('type', 'tour')->get()->toJson());
    }

    public function updateCalendarTour(Request $request, $id){
        $start_date = $request->get('start_date');
        $end_date = $request->get('end_date');

        Tour::query()
            ->where('id', $id)
            ->update([
                'departure_date' => $start_date,
                'retirement_date' => $end_date,
            ]);

        return response()->json(true);
    }

    public function changeTourPrice(Request $request){
        $tour_id = $request->tour_id;
        $total_amount = $request->price_total_amount;
        $price_for_one = $request->price_for_one;

        Tour::query()
            ->where('id', $tour_id)
            ->update([
                'total_amount' => $total_amount,
                'price_for_one' => $price_for_one,
            ]);

        return response()->json([
            'total_amount' => $total_amount,
            'price_for_one' => $price_for_one
        ]);
    }

    /**
     * Validate Tour
     *
     * @param $request
     */
    public function validateTour( $request ) {
        $endDate = '';
        if ($request->departure_date && $request->retirement_date){
            $departure_date = $request->departure_date;
            $retirement_date = $request->retirement_date;
            try {
                $endDate = !empty($departure_date) && preg_match('/^\d{4}-\d{2}-\d{2}$/', $departure_date)
                    ? Carbon::createFromFormat('Y-m-d', $departure_date)->addDays(29)
                    : Carbon::now()->addDays(29);
            } catch (Exception $e) {
                $endDate = Carbon::now()->addDays(29);
            }
        }
        $this->validate( $request, [
            'name'            => 'required',
            'departure_date'  => 'required|before_or_equal:retirement_date',
            'retirement_date' => 'required|before_or_equal:' . $endDate,
            'pax'             => 'required|numeric',
//            'country_begin'   => 'required',
//            'city_begin'      => 'required',
//            'country_end'     => 'required',
//            'city_end'        => 'required',
            'pax_free'        => 'numeric',
        ] );

        if(!$request->is_quotation) {
        	$this->validate($request, [
		        'country_begin'   => 'required',
                'city_begin'      => 'required',
                'country_end'     => 'required',
                'city_end'        => 'required',
                'assigned_user'   => 'required'
	        ]);
        }
    }

    public function convertToTour(Request $request, $id) {
    	$tour = Tour::find($id);
    	if ($tour && $tour->is_quotation) {
    		$tour->is_quotation = false;
			$tour->status = 4;
    		$tour->update();
		    LaravelFlashSessionHelper::setFlashMessage(sprintf(
			    'Quotations Tour %s #%s is moved to tours', $tour->name, $tour->id
		    ));
	    }
	    return redirect(route('tour.show', ['id' => $id]));
    }
	public function convertToQuotation(Request $request, $id) {
    	$tour = Tour::find($id);
    	if ($tour) {
    		$tour->is_quotation = true;
			$tour->status = 1;
    		$tour->update();
		    LaravelFlashSessionHelper::setFlashMessage(sprintf(
			    'Tour Tour %s #%s is moved to Quotation', $tour->name, $tour->id
		    ));
	    }
	    return redirect(route('tour.show', ['id' => $id]));
    }
	
	
public function quotation_data(Request $request)
    {
        //if (Auth::user()->hasRole('admin')) {
         $tours = Tour::where("is_quotation",1)->get();
       
        //} else {
            //$tours = $this->repository->allForAssigned();
        //}

        $permission_destroy = PermissionHelper::$relationsPermissionDestroy['App\Tour'];
        $permission_edit = PermissionHelper::$relationsPermissionEdit['App\Tour'];
        $permission_show = PermissionHelper::$relationsPermissionShow['App\Tour'];
        
        $perm = [];        
        $perm['show'] = Auth::user()->can($permission_show);        
        $perm['edit'] = Auth::user()->can($permission_edit);
        $perm['destroy'] = Auth::user()->can($permission_destroy);
        $perm['clone'] = Auth::user()->can('tour.create');
        
        return Datatables::of($tours)->addColumn('action', function ($tour) use($perm) {
                return $this->getButton($tour->id, false, $tour, $perm);
            })
            ->addColumn('status_name', function ($tour){
                if(Auth::user()->can('tour.edit')){
                    $status = View::make('component.tour_status_for_datatable', ['status' => $tour->getStatusName(), 'color' => $tour->getStatusColor()]);
                }else{
                    $status = $tour->getStatusName();
                }
                return $status;
            })
            ->addColumn('select', function ($tour) {
                return DatatablesHelperController::getSelectButton($tour->id, $tour->name);
            })
            ->addColumn('link', function($tour){
                $tourDay = TourDay::where('tour', $tour->id)->first();
                $link = route('tour_package.store');
                if($tourDay){
                return "<button data-link='$link' class='btn btn-success tour_package_add' data-tourDayId='{$tourDay->id}' data-tour_id='{$tour->id}'" .
                    " data-departure_date='{$tour->departure_date}' data-retirement_date='{$tour->retirement_date}'>+</button>";
                }
            })
            ->rawColumns(['select', 'action', 'link'])
            ->make(true);
    }
	


	
	protected function syncComparisonRows($comparison)
    {
    	$quotation = Quotation::find($comparison->id);
    	if ($quotation) {
		    $tour = $quotation->tour;
		    if ($tour) {
			    foreach ($tour->tour_days as $tourDay) {
					$comparisonRow = ComparisonRow::where(['date' => $tourDay->date, 'comparison_id' => $comparison->id])->first();
					if (!$comparisonRow) {
						$newComparisonRow = new ComparisonRow();
						$newComparisonRow->date = $tourDay->date;
						$newComparisonRow->comparison_id = $comparison->id;
						$newComparisonRow->save();
					}
			    }
		    }
	    }
    }
	
	public function get_cities($country_code){
        $options = City::where("country",$country_code)->get();
        $data = "";
        foreach ($options as $option) {
            $data .= '<option>'.$option->name.'</option>';
        }
        
        return response()->json([$data]);
    }
	
	 public function docExport(Request $request, $id)
	{
		// Get your data to export (replace with your own data retrieval logic)
		$tour = Tour::findOrFail($id);
		$dayFrom = Carbon::createFromFormat('Y-m-d', $tour->departure_date);
		$dayTo = Carbon::createFromFormat('Y-m-d', $tour->retirement_date);
		$diff = $dayFrom->diffInDays($dayTo);
		$tour->tourCode = "$tour->name#$dayFrom->month$dayTo->day-$diff-D";
		$data = $this->prepareTourPackages($tour, $request);
		 if ($request->doc_type  === 'voucher'){
            return $this->exportVoucherdoc($tour, $data,$request);
        }
        if ($request->doc_type === 'hotel'){
            return $this->exportHotelsdoc($tour, $data,$request);
        }
		// Call the exportDocShort method and return the response
		return $this->exportDocShort($tour, $data, $request);
	}
	
	public function update_voucherid(Request $request)
    {
        $tour_package = TourPackage::find($request->id);
        if ($request->value == 1) {
            $tour_package->update([
                "vch" => 1,
            ]);
        } else {
            $tour_package->update([
                "vch" => 0,
            ]);
        }
        return response()->json("success");
    }
    public function update_itnid(Request $request)
    {
        $tour_package = TourPackage::find($request->id);
        if ($request->value == 1) {
            $tour_package->update([
                "itn" => 1,
            ]);
        } else {

            $tour_package->update([
                "itn" => 0,
            ]);
        }
        return response()->json("success");
    }
	public function days_dropdown($tour_id, Request $request)
{
    $tour = Tour::find($tour_id);
    $offer_date = $request->offer_date;
    $option_date = $request->option_date;

    $tourDates = $this->prepareTourPackages($tour, $request)['tourDates'];

    // Define the label and select element for the dropdown
    $package_name = '<label for="service" id="label-service' . $tour->id . '">' . $tour->name . '</label>';
    $package_name .= '<select id="service' . $tour->id . '" name="tourDayId" class="form-control tour_select" required>';

    // Initialize a flag to check if any matching date is found
    $matchingDateFound = false;

    // Check if an option date exists, and if so, match tour dates with it
    if (!empty($option_date)) {
        foreach ($tourDates as $tourDate) {
            if ($tourDate->date == $option_date) {
                $package_name .= '<option value="' . $tourDate->id . '"> ' . $tourDate->date . '</option>';
                $matchingDateFound = true;
            }
        }
    }

    // If no option date, check tour dates against offer date
    if (!$matchingDateFound && !empty($offer_date)) {
        foreach ($tourDates as $tourDate) {
            if ($tourDate->date == $offer_date) {
                $package_name .= '<option value="' . $tourDate->id . '"> ' . $tourDate->date . '</option>';
                $matchingDateFound = true;
            }
        }
    }

    // Add a disabled option if no matching date was found
    if (!$matchingDateFound) {
        $package_name .= '<option selected disabled>No date to transfer this offer</option>';
    }

    $package_name .= '</select><br>';

    return [$package_name];
}
   public function updateStatus(Request $request){
   
    $office=Offices::where('status',1)->first();
	   if($office){
		    $office->status=0;
	   $office->save();
	   }
		   $office=Offices::where('id',$request->id)->first();
		   $office->status=1;
		   $office->save();  
   }

}
