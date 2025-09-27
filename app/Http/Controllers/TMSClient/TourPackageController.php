<?php

namespace App\Http\Controllers\TMSClient;

use App\Bus;
use App\BusDay;
use App\Comment;
use App\Currencies;
use App\Driver;
use App\Event;
use App\Guide;
use App\Helper\BusDayHelper;
use App\Helper\LaravelFlashSessionHelper;
use App\Helper\PermissionHelper;
use App\Helper\TourPackage\TourService;
use App\Hotel;
use App\HotelRoomTypes;
use App\PricesRoomTypeHotel;
use App\Repository\Contracts\TourPackageRepository;
use App\Restaurant;
use App\RoomTypes;
use App\Status;
use App\TourDay;
use App\Transfer;
use App\Tour;
use App\TransferHasBusesAndDrivers;
use App\TransferToBuses;
use App\TransferToDrivers;
use Carbon\Carbon;
use DateInterval;
use DatePeriod;
use DB;
use Illuminate\Support\Facades\App;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\TourPackage;
use Amranidev\Ajaxis\Ajaxis;
use Illuminate\Support\Facades\Input;
use URL;
use View;
use App\Helper\HelperTrait;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\Models\Activity;
use App\Repository\Contracts\TourRepository;
use App\TourRoomTypeHotel;
use App\Task;
use App\Notification;
use Auth;
use App\User;
use Illuminate\Support\Facades\Crypt;

class TourPackageController extends Controller
{
    use HelperTrait;

    protected $tourRepository;
    /**
     * @var BusDayHelper
     */
    private $busDayHelper;

    /** @var  TourPackageRepository */
    protected $tourPackageRepository;

    /**
     * TourPackageController constructor.
     * @param TourRepository $tourRepository
     * @param BusDayHelper $busDayHelper
     * @param TourPackageRepository $tourPackageRepository
     */
    public function __construct(TourRepository $tourRepository, BusDayHelper $busDayHelper, TourPackageRepository $tourPackageRepository)
    {

        $this->tourRepository = $tourRepository;
        $this->tourPackageRepository = $tourPackageRepository;
        $this->busDayHelper = $busDayHelper;
        $this->middleware('preventBackHistory');
    
    }

    public $defaultTimes =[
        'hotel' => [
            'time_from' => '18:00:00',
            'time_to' => '12:00:00'

        ],
        'service' => [
            'time_from' => '12:00:00',
            'time_to' => null
        ],
        'transfer' => [
            'time_from' => '12:00:00',
            'time_to' => '12:00:00'
        ],
        'description' => [
            'time_from' => '12:00:00'
        ]
    ];

    public $serviceTypes = [
        0   => 'hotel',
        1   => 'event',
        2   => 'guide',
        3   => 'transfer',
        4   => 'restaurant',
        5   => 'tourPackage',
        6   => 'cruise',
        7   => 'flight'
    ];

    /**
     * Display a listing of the resource.
     *
     * @return  \Illuminate\Http\Response
     */
    public function index()
    {
        $title = 'Index - Tour Package';
        $tourPackages = TourPackage::paginate(15);

        return view('tour_package.index', compact('tourPackages', 'title'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return  \Illuminate\Http\Response
     */
    public function create($id, Request $request)
    {
        $serviceTypes = $this->serviceTypes;
        $statuses = Status::query()->orderBy('sort_order', 'asc')->where('type', 'service_in_tour')->get();
        $currencies = Currencies::all();
        if ($request->service_type) {
            $tour_package = TourDay::findOrfail($id);
            $namespace = "App\\" . $request->service_type;
            $model = $namespace::findOrfail($request->service_id);
            $service_type = $request->service_type;
            $service_id = $request->service_id;
        } else $tour_package = TourDay::findOrfail($id);
        // $tour = Tour::findOrfail($tour_package->tour);
        $tour = $this->tourRepository->byId($tour_package->tour);
        return view('tour_package.create', ['tour_package' => $tour_package,
            'statuses' => $statuses,
            'currencies' => $currencies,
            'serviceTypes' => $serviceTypes,
            'id' => $id,
            'serviceName' => $model->name ?? null,
            'selectedServiceType' => $service_type ?? null,
            'selectedServiceId' => $service_id ?? null,
            'tour' => $tour]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param    \Illuminate\Http\Request  $request
     * @return  \Illuminate\Http\Response
     */

    public function store(Request $request)
    {
		
		$latestId = TourPackage::latest()->pluck('id')->first();
		$latestId = $latestId +1;
		$latestId = Crypt::encryptString($latestId );
        $this->validateTourPackage($request);

        $service_id = $request->serviceId;
        $bus_id = $request->get('bus_id', null);
        $drivers_id = $request->get('drivers_id', null);
        $dep_date_transfer = $request->get('dep_date_transfer', null);
        $ret_date_transfer = $request->get('ret_date_transfer', null);
        $package_id = $request->get('package_id', null);
        $tourId = $request->tourId;
        $defaultHotel = $this->getDefaultTimes('hotel');
        $defaultService = $this->getDefaultTimes('service');
        $defaultTransfer = $this->getDefaultTimes('transfer');

        // validate Transfer dates
        if (strtolower($request->serviceType) == 'transfer') {
            // validate buses
            $res_bus_validate = $this->busDayHelper->validateBuses($bus_id,
                $dep_date_transfer,
                $ret_date_transfer);

            if(!$res_bus_validate['check']){
                $data = [
                    'bus_busy' => true,
                    'bus_busy_message' => 'The Bus is Busy from ' . $res_bus_validate['start'] . ' to ' . $res_bus_validate['end']
                ];

                return response()->json($data);
            }

            //validate drivers
            $res_driver_validate = $this->busDayHelper->validateDrivers($drivers_id, $dep_date_transfer, $ret_date_transfer);

            if(!$res_driver_validate){
                $data = [
                    'bus_busy' => true,
                    'bus_busy_message' => 'The Driver is Busy'
                ];

                return response()->json($data);
            }
        }





        if(strtolower($request->serviceType) == 'cruises'){
            $request->serviceType = 'cruise';
        }

        $default_status_service = Status::query()
            ->where('type', 'service_in_tour')
            ->where('name', 'Pending')->first();

        $default_status_hotel = Status::query()
            ->where('type', 'hotel')
            ->where('name', 'Pending')->first();

        $default_status_transfer = Status::query()
            ->where('type', 'bus')
            ->where('name', 'Planned, need to check with coach co')->first();

        $status = null;

        $currency = Currencies::query()->where('name', 'Euro')->first();
        $tourPackage = new TourPackage();
        $tourPackage->type = array_search(strtolower($request->serviceType), $this->serviceTypes);
        $tourPackage->reference = $request->serviceId;
        $tourPackage->name = $request->serviceName;
        $tourPackage->paid = $request->paid ?: false;
        if(strtolower($request->serviceType) == 'hotel'){
            $status = $default_status_hotel ? $default_status_hotel->id : null;
        }else if(strtolower($request->serviceType) == 'transfer'){
            $status = $default_status_transfer ? $default_status_transfer->id : null;
        }else{
            $status = $default_status_service ? $default_status_service->id : null;
        }
        $tourPackage->status = $status;
        $tourPackage->total_amount = $request->total_amount ?: 0;
        $tourPackage->currency = $currency != null ? $currency->id : null;
        $tourPackage->rate = $request->rate;
        $tourPackage->note = $request->note;
        $tourPackage->description = $request->description;
		$tourPackage->supplier_url = "https://dev.eetstravel.com/booking/".$latestId;
		
        $serviceType = $request->serviceType; // we should to connect with tourday or tour(transfer)
        if (strtolower($serviceType) == 'transfer') {
            $tourId = $request->tourId;
            $tour = Tour::findOrFail($tourId);
            $tourPackage->time_from = $dep_date_transfer.' '.$defaultTransfer['time_from'];
        }
        else {
            $tourDay = TourDay::query()->get()->where('id', $request->tourDayId)->first();
            $tourDay_ = $tourDay;
            $tourId = $tourDay->tour;
            $tour = $this->tourRepository->byId($tourId);

            if($request->pageService == 'change_edit_service'){
                $tourPackage->time_from = $request->serviceOldTime;
            }else{

                if(strtolower($serviceType) == 'hotel') {
                    $time = $defaultHotel['time_from'];
                }else {
                    $time = $defaultService['time_from'];
                }

                $tourPackage->time_from = $request->serviceOldTime == null ? $tourDay->date . ' '. $time :  $tourDay->date . ' ' . $request->serviceOldTime;
            }
        }

        $tourPackage->pax = $tour->pax;

        if(strtolower($serviceType) == 'hotel'){
            $tourPackage->time_to = !empty($request->tourDayIdRetirement) ? $request->tourDayIdRetirement.' '.$defaultHotel['time_to'] : $tourPackage->time_from;
        }
        else{
            if (strtolower($serviceType) == 'transfer') {
                $tourPackage->time_to = $ret_date_transfer.' '.$defaultTransfer['time_to'];
            }
            else if($tourPackage->time_from) {
                //$timeFromDate = Carbon::createFromFormat('Y-m-d H:i:s', $tourPackage->time_from)->addHour();
                $timeFromDate = (new Carbon($tourPackage->time_from));
                $timeFromDate->format('Y-m-d H:i:s');
                $timeFromDate->addHour();
                $tourPackage->time_to = $timeFromDate;
            }



        }
        $tourPackage->pax_free = $tour->pax_free;
        $tourPackage->driver_id = $request->get('driver_id', null);
        $tourPackage->save();

        if (strtolower($serviceType) != 'transfer') {
            $tourPackage->assignTourDay($tourDay);
            if(strtolower($request->serviceType) == 'hotel') {
                $this->createHotelPackages($tourPackage, $tourDay->tour, $request->tourDayIdRetirement);
            }
        } else {
            $tourPackage->tour_id = $request->tourId;
            $tourPackage->save();
        }
        $this->tourPackageRepository->setCityTaxInPackage($tourPackage);

        if (strtolower($serviceType) == 'transfer'){

            DB::beginTransaction();

            $dates_interval = $this->busDayHelper->getDatesInterval($dep_date_transfer, $ret_date_transfer);


            foreach ($dates_interval as $tourDay){
                $busDay = new BusDay();
                $busDay->tour_id = $tourId;
                $busDay->transfer_id = $service_id;
                $busDay->tour_package_id = $tourPackage->id;
                $busDay->date = $tourDay['date'];
                $busDay->status_id = $default_status_transfer ? $default_status_transfer->id : null;
                $busDay->bus_id = $bus_id;
                $busDay->name_trip = null;
                $busDay->city_trip = null;
                $busDay->country_trip = null;
                $busDay->save();

                $this->busDayHelper->addDriversToTransfer($drivers_id, $tour->id, $service_id, $tourPackage->id, $busDay->id);
            }

            DB::commit();
        }


        if (strtolower($request->serviceType) == 'hotel') {
            $records = TourRoomTypeHotel::query()->where('tour_id', $request->tourId)->get();
            foreach ($records as $record) {
                $price_hotel = PricesRoomTypeHotel::query()
                    ->where('hotel_id', $tourPackage->reference)
                    ->where('room_type_id', $record->room_type_id)->first();

                $create_record = new HotelRoomTypes();
                $create_record->room_type_id = $record->room_type_id;
                $create_record->tour_package_id = $tourPackage->id;
                $create_record->count = $record->count;
                $create_record->price = $price_hotel ? $price_hotel->price : 0;
                $create_record->save();
                
                $price = $this->checkPriceBySeasons($create_record, $tourPackage, $tourDay_);
            }
        }

        $tour->price_for_one =  $tour->getPriceForOnePaxInTour();
        $tour->save();



        $this->logActivity($tourPackage, $tour);
        if($request->pageService == 'change_edit_service'){
            return response('/tour_package/'.$tourPackage->id.'/edit');
        }

        if ($request->ajax()) return response(route('tour.show', ['id' => $tourId]));
        return redirect(route('tour.edit', ['id' => $tourId]));
    }

    public function getLastDateForHotel($id){
        $last_date = TourPackage::query()->where('id', $id)->first();

        return response()->json($last_date->time_to);
    }

    public function getDateDayId($id){
        return response()->json(TourDay::query()->where('id', $id)->first());
    }

    public function createHotelPackages($parentPackage, $tourId, $tourDayIdRetirement = null, $action = null)
    {
        $hotelRangeFrom = Carbon::parse($parentPackage->time_from);
        if ($tourDayIdRetirement) {
            $hotelRangeTo = Carbon::parse($tourDayIdRetirement);
            $interval = new DateInterval('P1D');
            $hotelRangeFrom->add($interval);
            $daterange = new DatePeriod($hotelRangeFrom, $interval ,$hotelRangeTo);
            $tour = Tour::findOrFail($tourId);
            foreach($daterange as $date) {
                DB::beginTransaction();
                $tourDay = $tour->getTourDateByDate($date);
                $newHotel = $parentPackage->replicate();
                $newHotel->parent_id = $parentPackage->id;
                $newHotel->save();
                $newHotel->assignTourDay($tourDay);

                $records = TourRoomTypeHotel::query()->where('tour_id', $tourId)->get();
                foreach ($records as $record) {
                    $price = null;
                    if($action == 'update'){
                        $price = HotelRoomTypes::query()
                            ->where('tour_package_id', $parentPackage->id)
                            ->where('room_type_id', $record->room_type_id)->first();
                    }else{
                        $price = PricesRoomTypeHotel::query()
                            ->where('hotel_id', $parentPackage->reference)
                            ->where('room_type_id', $record->room_type_id)->first();
                    }

                    $create_record = new HotelRoomTypes();
                    $create_record->room_type_id = $record->room_type_id;
                    $create_record->tour_package_id = $newHotel->id;
                    $create_record->count = $record->count;
                    $create_record->price = $price ? $price->price : 0;
                    $create_record->save();
                    $tp = TourPackage::find($newHotel->id);
                    $price = $this->checkPriceBySeasons($create_record, $tp, $tourDay);
                }
                DB::commit();
            }
        }
    }

    public function logActivity($package, $tour, $descriptionPackage = false)
    {
        $text = $descriptionPackage ? "TourPackage: add description service: {$package->description} at {$tour->name}"
            : "TourPackage: {$package->name} created at Tour: {$tour->name}";
        activity('TourPackage')
            ->withProperties(['action' => 'created', 'link' => route('tour.show', ['id' => $tour->id]) ])
            ->on($package)
            ->log($text);
    }

    /**
     * Display the specified resource.
     *
     * @param    \Illuminate\Http\Request  $request
     * @param    int  $id
     * @return  \Illuminate\Http\Response
     */
    public function show($id, Request $request)
    {
        $title = 'Show - our Package';

        if ($request->ajax()) {
            return URL::to('tour_package/'.$id);
        }

        $tour_package = TourPackage::findOrfail($id);

        return view('tour_package.show', compact('title', 'tour_package'));
    }

    /**
     * Show the form for editing the specified resource.
     * @param    \Illuminate\Http\Request  $request
     * @param    int  $id
     * @return  \Illuminate\Http\Response
     */
    public function edit($id, Request $request)
    {
        $oldInput = \Session::getOldInput();
        if (array_key_exists('type', $oldInput)) {
            $serviceType = $oldInput['type'];
        } elseif ($type = $request->query->get('serviceType')) {
            $serviceType = array_search($type, $this->serviceTypes);
        } else {
            $serviceType = 0;
        }

        if (array_key_exists('search', $oldInput)) {
            $search = $oldInput['search'];
        } elseif ($type = $request->query->get('search')) {
            $search = $type;
        } else {
            $search = '';
        }
        $title = 'Edit - tour_package';

        if ($request->ajax()) {
            return URL::to('tour_package/' . $id . '/edit');
        }


        $tourPackage = TourPackage::findOrfail($id);
        $tourPackage['from_date'] = (new Carbon($tourPackage->time_from))->toDateString();
        $tourPackage['from_time'] = (new Carbon($tourPackage->time_from))->toTimeString();
        $tourPackage['to_date'] = (new Carbon($tourPackage->time_to))->toDateString();
        $tourPackage['to_time'] = (new Carbon($tourPackage->time_to))->toTimeString();

        $serviceTypes = $this->serviceTypes;

        (TourService::$serviceTypes[$tourPackage->type] === 'hotel') ?
            $statuses = Status::query()->where('type', 'hotel')->orderBy('sort_order')->get() :
            $statuses = Status::query()->where('type', 'service_in_tour')->orderBy('sort_order')->get();

        if (TourService::$serviceTypes[$tourPackage->type] === 'transfer') $statuses = Status::query()->orderBy('sort_order', 'asc')->where('type', 'bus')->get();

        $currencies = Currencies::all();
        $service = TourService::getService($serviceType);
        $services = $service->getItems(['serviceType' => $serviceType, 'search' => $search]);
        $filterType = $this->serviceTypes[$serviceType];
        $selectedServiceName = $this->serviceTypes[$tourPackage->type];
        $selectedService = TourService::getService(TourService::$serviceTypes[$tourPackage->type]);

        $service = $selectedService->getItems(
            [
                'serviceType'   => $tourPackage->type,
                'id'            => $tourPackage->reference
            ]
        )->first();
        $serviceName = null;
        if ($service) {
            if ($this->serviceTypes[$tourPackage->type]== 'flight') { // Flight
                $serviceName = $selectedServiceName . ' - '. $service->date_from;
            } else {
                $serviceName = $selectedServiceName . ' - '. $service->name;
            }
        }
        


//        $room_typesDvo = RoomTypes::query()->orderBy('sort_order', 'ASC')->get();
//        
//        $tour = $tourPackage ->getTour();
//        foreach($tour->getAllServices() as $tp){
//            if($tp->type == 0){
//                $room_types = array();
//                foreach ($room_typesDvo as &$room_type){
//                    $room_type['count_room'] = $this->tourPackageRepository->getHotelRoomTypeCount($tp, $room_type->id);
//                    $room_type['price_room'] = $this->tourPackageRepository->getHotelRoomTypePrice($tp, $room_type->id);
//                }
//                $room_types = $room_typesDvo;
//                $selected_room_types = array();
//
//                foreach($tp->room_types_hotel as $item){
//                    $item->room_types['count_room'] = $item->count;
//                    $item->room_types['price_room'] = $item->price;
//                    $selected_room_types[] = $item->room_types;
//                }
//            }
//        }

        $room_typesDvo = RoomTypes::query()->orderBy('sort_order', 'ASC')->get();
		$room_types = array();
		$selected_room_types = array();
		$selected_drivers = array();
		$selected_bus = array();
        $tour = $tourPackage ->getTour();
        $tp = $tourPackage;
            if($tp->type == 0){
                $room_types = array();
                foreach ($room_typesDvo as &$room_type){
                    $room_type['count_room'] = $this->tourPackageRepository->getHotelRoomTypeCount($tp, $room_type->id);
                    $room_type['price_room'] = $this->tourPackageRepository->getHotelRoomTypePrice($tp, $room_type->id);
                }
                $room_types = $room_typesDvo;
                $selected_room_types = array();

                foreach($tp->room_types_hotel as $item){
                    $item->room_types['count_room'] = $item->count;
                    $item->room_types['price_room'] = $item->price;
                    $selected_room_types[] = $item->room_types;
                }
            }
      
        $drivers = null;
        $buses = null;

        if (TourService::$serviceTypes[$tourPackage->type] == 'transfer') {
            $serviceItem = Transfer::findOrFail( $tourPackage->reference );
            $drivers = Driver::query()->where('transfer_id', $serviceItem->id)->get();
            $buses = Bus::query()->where('transfer_id', $serviceItem->id)->get();

            $selected_drivers = TransferToDrivers::query()
                ->where('tour_package_id', $tourPackage->id)
                ->get();

            $selected_bus = BusDay::query()
                ->where('tour_package_id', $tourPackage->id)
                ->first();
        }



        return view(
            'tour_package.edit',
            compact(
                'title',
                'tourPackage',
                'serviceTypes',
                'statuses',
                'currencies',
                'services',
                'filterType',
                'serviceType',
                'search',
                'serviceName',
                'room_types',
                'selected_room_types',
                'drivers',
                'buses',
                'selected_drivers',
                'selected_bus'
            )
        );
    }

    private function checkPriceBySeasons($item, $tourPackage, $tourDay = null){
        $hotel = Hotel::where('id', $tourPackage->reference)->first();

        foreach ($hotel->seasons as $season){
            $start_year = Carbon::parse($tourPackage->time_from)->format('Y');
            $end_year = Carbon::parse($tourPackage->time_to)->format('Y');
            $season_start = Carbon::parse($season->start_date)->format('m-d');
            $season_end = Carbon::parse($season->end_date)->format('m-d');
            $check_start = $start_year . "-" . $season_start;
            $check_end = $end_year . "-" . $season_end;
//            if(Carbon::parse($tourPackage->time_from)->format('Y-m-d') >= $check_start && Carbon::parse($tourPackage->time_to)->format('Y-m-d') <= $check_end){
            if($tourDay->date >= $check_start && $tourDay->date <= $check_end){
                foreach($season->seasons_room_types as $room){
                    if($room->room_type_id == $item->room_type_id) { $item->price = $room->price; $item->save(); }
                }
            }

        }
        return $item->price;
    }

    public function viewHotelRoomType(Request $request){
        $room_type = json_decode($request->get('room_type'));

        $view = View::make(
            'component.item_hotel_room_type',
            [
                'room_type'   => $room_type
            ]
        );

        $contents = $view->render();

        return response()->json($contents);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param    \Illuminate\Http\Request  $request
     * @param    int  $id
     * @return  \Illuminate\Http\Response
     */
    public function update($id, Request $request)
    {
        $service_id = $request->serviceId;
        $drivers_id = $request->driver_transfer;
        $bus_id = $request->bus_transfer;
        $serviceType = $request->serviceType;

        $room_types_count = collect($request->get('room_types_qty'));
        $room_types_price = collect($request->get('room_types_price'));
        $request['time_from'] = $request->get('from_date') . ' ' . $request->get('from_time');
        $request['time_to'] = $request->get('to_date') . ' ' . $request->get('to_time');

        $tour_package = TourPackage::query()->where('id', $id)->first();
        $tour_id = $tour_package->getTour()->id;

        if (strtolower($request->serviceType) == 'transfer') {

            // validate buses
            $res_bus_validate = $this->busDayHelper
                ->validateBuses($bus_id, $request->time_from, $request->time_to, $tour_package->id);


            if(!$res_bus_validate['check']){
                return back()->with('message_buses', trans('main.TheBusisBusyfrom') . $res_bus_validate['start'] . trans('main.to') . $res_bus_validate['end']);
            }

            //validate drivers
            $res_driver_validate = $this->busDayHelper
                ->validateDrivers($drivers_id, $request->time_from, $request->time_to, $tour_package->id);

            if(!$res_driver_validate){

                return back()->with('message_buses', trans('main.TheDriverisBusy'));
            }
        }


        $this->validate($request, [
            'from_date' => 'before_or_equal:to_date'
        ]);

        $room_type_dto = array();

        foreach ($room_types_count as $key => $item){
            $room_type_dto[] = [
                'room_type_id' => $key,
                'count' => $item,
                'price' => $room_types_price[$key]
            ];
        }

        DB::beginTransaction();
        $tourPackage = TourPackage::findOrfail($id);
        $tourDay = TourDay::get()->where('id', $request->tourDayId)->first();


        if ($tourDay && $request->get('from_date') !== $tourDay->date) {
            $day = $this->findTourDay($request->time_from, $tourDay->tour);
            if (!$day) return back()->with('date-error', 'Sorry, wrong date');
            $tourPackage->removeTourDay($tourDay);
            $tourPackage->assignTourDay($day);
        }
        $tourPackage->type = array_search($request->serviceType, $this->serviceTypes);
        $tourPackage->reference = $request->serviceId;
        $tourPackage->name = $request->name;
        $tourPackage->paid = $request->paid;
        $tourPackage->pax = $request->pax;
        $tourPackage->pax_free = $request->pax_free;

        $tourPackage->total_amount_manually = $request->total_amount_manually;
        if (strtolower($serviceType) != 'hotel'){
            $tourPackage->total_amount = $request->total_amount;
        }

        $tourPackage->currency = $request->currency;
        $tourPackage->rate = $request->rate;
        $tourPackage->note = $request->note;
        $tourPackage->time_from = $request->time_from;
        $tourPackage->time_to = $request->time_to;

        $tourPackage->city_tax = $request->city_tax;
        $tourPackage->description = $request->description;
        $tourPackage->driver_id = $request->get('driver', null);
        $tourPackage->status = $request->status;
        $tourPackage->save();
        DB::commit();
        $this->tourPackageRepository->setCityTaxInHotel($tourPackage);
        $this->tourPackageRepository->syncPackageMenus($tourPackage, $request->package_menu );

        // change status for bus days, if transfer
        if(strtolower($serviceType) == 'transfer'){
            $bus_days = BusDay::query()->where('tour_package_id', $id)->get();

            if($bus_days->isNotEmpty()){
                foreach ($bus_days as $bus_day){
                    $bus_day->status_id = $request->status;
                    $bus_day->save();
                }
            }
        }

        if($tourDay) {
            $tourId = $tourDay->tour;
        } else {
            $tourId = $tourPackage->tour ? $tourPackage->tour->id : '' ;
        }

        if(strtolower($serviceType) == 'hotel'){
            $last_date = $tourPackage->time_to;

            TourPackage::query()->where('parent_id', $tourPackage->id)->forceDelete();

            $this->createHotelPackages($tourPackage, $tourId, $last_date, 'update');
        }else{

            if($tourPackage->time_from > $tourPackage->time_to) {
                $tourPackage->time_to = Carbon::parse($tourPackage->time_from)->addHour();
                $tourPackage->save();
            }
        }

        $child_tour_packages = TourPackage::query()->where('parent_id', $tour_package->id)->get();
        $child_tour_packages_id = [];
        if($child_tour_packages->isNotEmpty()){
            foreach ($child_tour_packages as $item){
                $child_tour_packages_id[] = $item->id;
            }
        }

        $child_tour_packages_id[] = (int) $id;
        HotelRoomTypes::query()->whereIn('tour_package_id', $child_tour_packages_id)->delete();

        if(!empty($room_type_dto)){
            foreach ($room_type_dto as $item){
                if($item['count'] !== null && $item['price'] !== null){
                    foreach ($child_tour_packages_id as $tour_package_id){
                        $create_room_type = new HotelRoomTypes();
                        $create_room_type->room_type_id = $item['room_type_id'];
                        $create_room_type->tour_package_id = $tour_package_id;
                        $create_room_type->count = $item['count'];
                        $create_room_type->price = $item['price'];
                        $create_room_type->save();
                    }
                }
            }
        }

        // update all total amount after create new child packages and main  tour package
        if(strtolower($serviceType) == 'hotel'){
            TourPackage::query()->whereIn('id', $child_tour_packages_id)->update([
                'total_amount' => $tourPackage->getPricePerPersonForHotel()
            ]);
        }






        if (strtolower($serviceType) == 'transfer'){
            DB::beginTransaction();

            $dates_interval = $this->busDayHelper->getDatesInterval($request->time_from, $request->time_to);
            $this->busDayHelper->deleteDriversToTransfer($tourPackage->id);
            $this->busDayHelper->deleteBusDays($tourPackage->id);


            foreach ($dates_interval as $tourDay){
                $busDay = new BusDay();
                $busDay->tour_id = $tourId;
                $busDay->transfer_id = $service_id;
                $busDay->tour_package_id = $tourPackage->id;
                $busDay->date = $tourDay['date'];
                $busDay->status_id = $request->status;
                $busDay->bus_id = $bus_id;
                $busDay->name_trip = null;
                $busDay->city_trip = null;
                $busDay->country_trip = null;
                $busDay->save();

                $this->busDayHelper->addDriversToTransfer($drivers_id, $tour_id, $service_id, $tourPackage->id, $busDay->id);
            }

            DB::commit();
        }

        $tour = Tour::query()->where('id', $tourId)->first();
        $tour->price_for_one =  $tour->getPriceForOnePaxInTour();
        $tour->save();

        LaravelFlashSessionHelper::setFlashMessage("Tour package {$tourPackage->name} edited", 'success');

        return redirect(route('tour.show', ['id' => $tourId]));
//        return redirect(route('tour_package.edit', ['id' => $id]));
    }

    public function checkDriverBusy($driver_id, $id){

    }
    public function findTourDay($date, $tourId)
    {
        $date = $this->convertDateToDays($date);
        $tourDay = TourDay::where('tour', $tourId)->where('date', $date)->first();
        if ($tourDay) return $tourDay;
    }
    /**
     * Delete confirmation message by Ajaxis.
     *
     * @link      https://github.com/amranidev/ajaxis
     * @param    \Illuminate\Http\Request  $request
     * @return  String
     */
    public function DeleteMsg($id, Request $request)
    {
        $msg = Ajaxis::BtDeleting('Warning!!', 'Would you like to remove This?', '/tour_package/'. $id . '/delete');

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
    public function destroy($id)
    {
        $tourPackage = TourPackage::findOrfail($id);
        $tour = $tourPackage->getTour();

        if($tourPackage->parrent_package_id){
            return back()->with('retirement_package', 'This is retirement package');
        }

        $tourPackageName = $tourPackage->name;

        HotelRoomTypes::query()->where('tour_package_id', $id)->delete();
        $tourPackage->delete();

        if($tourPackage->type !== null){
            if(strtolower(TourService::$serviceTypes[$tourPackage->type]) === 'hotel'){
                TourPackage::query()->where('parent_id', $id)->forceDelete();
            }

            if(strtolower(TourService::$serviceTypes[$tourPackage->type]) == 'transfer'){
                BusDay::query()
                    ->where('tour_package_id', $id)
                    ->delete();

                TransferToDrivers::query()
                    ->where('tour_package_id', $id)
                    ->delete();
            }
        }
        Comment::query()->where('reference_type', Comment::$services['tour_package'])->where('reference_id', $id)->delete();

        if ($tour) {
            foreach ($tour->users as $user) {
                $notification = Notification::create(['content' => "Deleted ". $tourPackageName. " from ".$tour->name, 'link' => '/tour/'.$tour->id]);
                $user->notifications()->attach($notification);
            }
        }

        LaravelFlashSessionHelper::setFlashMessage("Tour package {$tourPackage->name} deleted", 'success');

        return URL::previous();
    }

    public function generateServices(Request $request)
    {
        $serviceType = $request->request->get('serviceType') ? : 0;
        $search =  $request->request->get('search');
        $filterType = $this->serviceTypes[$serviceType];
        $tourService = TourService::getService(TourService::$serviceTypes[$serviceType]);
        return $tourService->generateTable([
            'serviceType'   => $serviceType,
            'search'        => $search,
            'filterType'    => $filterType
        ]);
    }

    public function getPackagesHotelConfirmed($id, Request $request){
        $tour_packages = TourPackage::query()->where('id', $id)->orWhere('parent_id', $id)->get();
        $type = $this->serviceTypes[$tour_packages[0]->type];
        $name_change_hotel = $tour_packages[0]->name;
        $tour_days = $tour_packages[0]->getTour()->tour_days;

        $dvo = [];
        $id_main_packages = collect();
        if($type == 'hotel'){
            foreach ($tour_packages as $package){
                $day_packages = $package->tourDays[0]->packages->where('type', $tour_packages[0]->type)
                    ->where('id', '!=', $id);

                foreach ($day_packages as $day_package){
                    if($day_package->id != $id && $day_package->parent_id != $id){
                        $id_main_packages[] = $day_package->parent_id == null ? $day_package->id : $day_package->parent_id;
                    }
                }
            }
        }else{
            return 'false';
        }

        $main_packages_id_unique = $id_main_packages->unique();


        foreach ($tour_days as $item){
            $packages_child = [];
            $packages_child[] = $item->packages->where('type', $tour_packages[0]->type)->whereIn('id', $main_packages_id_unique);
            $packages_child[] = $item->packages->where('type', $tour_packages[0]->type)->whereIn('parent_id', $main_packages_id_unique);
            $res_packages = [];

            foreach ($packages_child as $item_p){
                foreach ($item_p as $value_p){
                    $res_packages[] = $value_p;
                }
            }

            $dvo[] = [
                'confirmed_hotel' => $name_change_hotel,
                'date' => $item->date,
                'count_delete' => $res_packages
            ];
        }

        $view = View::make(
            'component.hotel_confirmed',
            [
                'dvo'=> $dvo
            ]);

        $contents = $view->render();
        return $contents;
    }

    public function deletePackagesCancelledHotel($id){
        $tour_packages = TourPackage::query()->where('id', $id)->orWhere('parent_id', $id)->get();
        $type = $this->serviceTypes[$tour_packages[0]->type];

        $packages_id_delete = collect();
        if($type == 'hotel'){
            foreach ($tour_packages as $package){
                $packages_id_delete->push(
                    $package->parent_id == null ?
                        $package->tourDays[0]->packages->where('type', $tour_packages[0]->type)->where('id', '!=', $id) :
                        $package->tourDays[0]->packages->where('type', $tour_packages[0]->type)->where('parent_id', '!=', $id)
                );
            }
        }

        $ids = [];
        foreach ($packages_id_delete as $item){
            foreach ($item as $value){
                $ids[] = $value->parent_id == null ? $value->id : $value->parent_id;
            }
        }

        TourPackage::query()
            ->whereIn('id', $ids)
            ->orWhereIn('parent_id', $ids)
            ->delete();

        return response()->json(true);
    }

    public function apiData($id, Request $request){
        $data = ['options' => $this->serviceTypes];
        return response()->json(json_encode($data));
    }


    /**
     * dragNdrop service reorder at tour show page
     * @param  Request $request
     * @return \Illuminate\Http\Response
     */
    public function reorderService(Request $request)
    {
        $package = TourPackage::findOrFail($request->targetId);
        $changePackage = TourPackage::findOrFail($request->getTime);
        $check_time = (int) Carbon::parse($package->time_from)->format('H');

        if ($package->time_from > $changePackage->time_from) {
            if ($this->checkStartDay($changePackage->time_from)) return response('fail startOfDay check');
            $package->time_from = Carbon::parse($changePackage->time_from)->subHour();
            $package->time_to = Carbon::parse($package->time_from)->addHour();
            $package->save();
            return response('done from package time bigger');
        }else if ($package->time_from < $changePackage->time_from) {
            // dd(Carbon::parse($changePackage->time_from)->addHour());
            if ($this->checkEndOfDay($changePackage->time_from)) return response('fail checkEndOfDay');
            $package->time_from = Carbon::parse($changePackage->time_from)->addHour();
            $package->time_to = Carbon::parse($package->time_from)->addHour();

            if( $check_time == 23 ) {
                $dat = Carbon::parse($package->time_from)->format('Y-m-d');
                $full_date = "{$dat} 23:00:00";
                $package->time_from = Carbon::parse($full_date);// maybe add subHour??
                $dat = Carbon::parse($package->time_to)->format('Y-m-d');
                $full_date = "{$dat} 23:00:00";
                $package->time_to = Carbon::parse($full_date);
            }

            $package->save();
            return response('done from package time less');
        }

        return response('done');
    }


    public function changeTourDay(Request $request, $id)
    {
        $newTourDay = TourDay::findOrfail($request->add_tour_day);
        $oldTourDay = TourDay::findOrfail($request->remove_tour_day);
        $days_between = ceil(round(strtotime($newTourDay->date) - strtotime($oldTourDay->date)) / 86400);
        $tmp_date = [];
        $out_tmp=[];
        /*
         *  $type = $request->type;
            $is_main = $request->is_main;
        foreach ($newTourDay->packages->sortBy('time_from') as $test){
            echo ($test->id."/".$test->name."/".$test->time_from."/n/r");
        }*/

        $package = TourPackage::findOrfail($id);
        $tour = Tour::findOrFail($package->getTour()->id);
        $is_alone = true;

        if($package->parent_id) {
            $packages = TourPackage::where('parent_id', $package->parent_id)->orWhere('id',$package->parent_id)->orderBy('time_from')->get();
            $is_alone = false;
        }else{
            $packages = TourPackage::where('parent_id', $package->id)->orWhere('id',$package->id)->orderBy('time_from')->get();
        }

        foreach ($packages as $pack){

            $date  =  Carbon::parse($pack->tourDays[0]->date)->format('Y-m-d');
            if ($days_between < 0) {
                $dat = Carbon::parse($date)->subDays(abs($days_between))->format('Y-m-d');
            } else {
                $dat = Carbon::parse($date)->addDays($days_between)->format('Y-m-d');
            }

            $tmp_tour_day = TourDay::where('date', $dat)->where('tour',$package->getTour()->id)->whereNull('deleted_at') ->first();
            if(!$tmp_tour_day) return response()->json(['message' => 'Yours changes are beyond the scope of the tour']);
            $tmp_date[]=$dat;
        }

        arsort($tmp_date);
        foreach ($tmp_date as $d){
            array_push($out_tmp, $d);
        }
        //$to_date = $out_tmp[0];
        $from_date = $out_tmp[count($out_tmp)-1];

        foreach ($packages as $pack){

            $date  =  Carbon::parse($pack->tourDays[0]->date)->format('Y-m-d');

            if ($days_between < 0) {
                $dat = Carbon::parse($date)->subDays(abs($days_between))->format('Y-m-d');
            } else {
                $dat = Carbon::parse($date)->addDays($days_between)->format('Y-m-d');
            }

            $tmp_tour_day = TourDay::where('date', $dat)->where('tour',$package->getTour()->id)->whereNull('deleted_at') ->first();

            $pack->removeTourDay($pack->tourDays[0]->id);
            $pack->assignTourDay($tmp_tour_day['id']);
            $time_from = $this->parseFullDate($pack->time_from)['hours'];
            $full_date_f = "{$from_date} {$time_from}";
            $time_to = $this->parseFullDate($pack->time_to)['hours'];
            // $full_date_t = "{$to_date} {$time_to}";

            if($pack->type == 0) {

                if(!$package->parent_id && $is_alone && count($packages) == 1) {

                    $pack->time_from = Carbon::parse($full_date_f);
                    $time_to = $this->parseFullDate($pack->time_to)['hours'];
                    $date = "{$from_date} {$time_to}";
                    $check_date = Carbon::parse("{$from_date}")->addDay()->format('Y-m-d');

                    if($check_date <= $tour->retirement_date) {
                        $full_date_t = Carbon::parse($date)->addDay();
                    }else{
                        $full_date_t = Carbon::parse($date);
                    }
                    $pack->time_to = Carbon::parse($full_date_t);

                }else{

                    if ($days_between < 0) {
                        $from = Carbon::parse($pack->time_from)->subDays(abs($days_between));
                        $to = Carbon::parse($pack->time_to)->subDays(abs($days_between));
                    } else {
                        $from = Carbon::parse($pack->time_from)->addDays(abs($days_between));
                        $to = Carbon::parse($pack->time_to)->addDays(abs($days_between));
                    }
                    $pack->time_from = Carbon::parse($from);
                    $pack->time_to = Carbon::parse($to);
                }

            }else{
                $full_date_f = "{$tmp_tour_day['date']} {$time_from}";
                $full_date_t = "{$tmp_tour_day['date']} {$time_to}";
                $pack->time_from = Carbon::parse($full_date_f);
                $pack->time_to = Carbon::parse($full_date_t);
            }

            $pack->save();
        }

        // we move one hour below from the previous package
        $newTourDay = TourDay::findOrfail($request->add_tour_day);
        $package_prev = TourPackage::where('id', $request->idprev)->first();
        $package_next = TourPackage::where('id', $request->idnext)->first();

        if(count($package_next)>0 || count($package_prev)>0) {

            $newTourDay->packages = $newTourDay->packages->sortBy('time_from');

            foreach ($newTourDay->packages as $pack) {

                if ($id == $pack->id && $pack->type != 0 ) {

                    if (!isset($package_next['time_from'])) {

                        if ((int)Carbon::parse($package_prev['time_from'])->format('H') == 23) {
                            $d = Carbon::parse($pack->time_from)->format('Y-m-d');
                            $t = Carbon::parse($package_prev['time_from'])->format('H:i:s');
                            $f = "{$d} {$t}";
                            $pack->time_from = Carbon::parse($f);
                        } else {
                            $d = Carbon::parse($pack->time_from)->format('Y-m-d');
                            $t = Carbon::parse($package_prev['time_from'])->format('H:i:s');
                            $f = "{$d} {$t}";
 //                           $pack->time_from = Carbon::parse($f);
                            $pack->time_from = Carbon::parse($package_prev['time_from'])->addHour();
                        }

                    } else
                        if (!isset($package_prev['time_from'])) {
                            $d = Carbon::parse($pack->time_from)->format('Y-m-d');
                            $t = Carbon::parse($package_next['time_from'])->subHour()->format('H:i:s');
                            $f = "{$d} {$t}";
                            $pack->time_from = Carbon::parse($f);
                        }

                    $pack->time_to = Carbon::parse($pack->time_from)->addHour();
                    $pack->save();
                }

            }
        }

        $newTourDay = TourDay::findOrfail($request->add_tour_day);
        $newTourDay->packages = $newTourDay->packages->sortBy('time_from');

        if ($package->description_package) return;

    }

    /**
     * @param  \Illuminate\Http\Request incomming request
     * @param  int package id
     * @return \Illuminate\Http\Response
     */
    public function changeTime(Request $request, $id)
    {
        // dd($request);
        //Request URL:http://tms.dev-server.cv.ua/tour_package/2446/change_time?timeKey=time_from&timeValue=19%3A00&type=Hotel&is_main=+0+

        $package = TourPackage::findOrFail($id);
        $date = $this->convertDateToDays($package[$request->timeKey]);
        $newTime = "$date {$request->timeValue}";
        $package[$request->timeKey] = $newTime;

        if($package->type == 0){
            $package->save();
        }else{
            $addHour = Carbon::parse($package[$request->timeKey])->addHour();
            $package['time_to'] = $addHour;
            $package->save();
            if($package->hasParrent()){
                $parrent = $package->parrent();
                $parrent[$request->timeKey] = $package[$request->timeKey];
                $parrent->save();
            }
            if ($package->hasChild()){
                $child = $package->getChild();
                $child[$request->timeKey] = $package[$request->timeKey];
                $child->save();
            }
        }

        return response()->json($package[$request->timeKey]);
    }

    public function descriptionPackage(Request $request)
    {
        $day = TourDay::find($request->tourDayId);
        $status = Status::where('type', 'service_in_tour')->where('name', 'Confirmed')->first()->id;
        $defaultTime = $this->getDefaultTimes('description');
        $date = "{$day->date} {$defaultTime['time_from']}";
        $package = TourPackage::create([
            'time_from' => $date,
            'description' => $request->description,
            'description_package' => true,
            'status' => $status]);
        $package->assignTourDay(TourDay::find($request->tourDayId));
        $this->logActivity($package, Tour::find($day->tour), true);
        return back();
    }

    public function ajaxUpdate(Request $request)
    {
        $package = TourPackage::find($request->package_id);
        $package[$request->fieldName] = $request->fieldValue;
        $package->save();
        return response('done');
    }


    /**
     * check if we can change time
     * @param  $checkTime time that we check
     * @return bool
     */
    public function checkStartDay($checkTime)
    {
        $time = Carbon::parse($checkTime);
        if ($time->copy() == $time->copy()->startOfDay()) return true;
        if ($time->copy()->subHour()->startOfDay() != $time->startOfDay()) return true;
        return false;
    }

    public function checkEndOfDay($checkTime)
    {
        $time = Carbon::parse($checkTime);
        if ($time->copy()->addHour()->startOfDay() == $time->copy()->addDay()->startOfDay()) return true;
        return false;
    }

    public function statusList(Request $request){

        return response()->json(Status::query()->select('name', 'id')->orderBy('sort_order', 'asc')->where('type', 'service_in_tour')->get()->toJson());
    }

    public function statusListHotel(Request $request){

        return response()->json(Status::query()->select('name', 'id')->orderBy('sort_order', 'asc')->where('type', 'hotel')->get()->toJson());
    }

    public function statusListTransfer(Request $request){

        return response()->json(Status::query()->select('name', 'id')->orderBy('sort_order', 'asc')->where('type', 'bus')->get()->toJson());
    }

    public function ajaxUpdateStatus(Request $request){
        $status_id = $request->status_id;
        $package_id = $request->package_id;

        $tour_package = TourPackage::where('id', $package_id)->first();

        if($tour_package->parent_id == null){
            TourPackage::find($package_id)->update([
                'status' => $status_id,
            ]);

            TourPackage::where('parent_id', $package_id)->update([
                'status' => $status_id
            ]);

            if(\App\Helper\TourPackage\TourService::$serviceTypes[$tour_package->type] == 'transfer'){
                $bus_days = BusDay::query()->where('tour_package_id', $package_id)->get();

                if($bus_days->isNotEmpty()){
                    foreach ($bus_days as $bus_day){
                        $bus_day->status_id = $status_id;
                        $bus_day->save();
                    }
                }
            }
        }else{
            $parent_package_id = TourPackage::where('id', $package_id)->first()->parent_id;

            TourPackage::find($parent_package_id)->update([
                'status' => $status_id,
            ]);

            TourPackage::where('parent_id', $parent_package_id)->update([
                'status' => $status_id
            ]);
        }

        if( $status_id == 43 ){
            $assigned_user = [];
            $task = new Task();
            //  $task->content = 'Confirm reservation of '.$tour_package->name.' before '. $request->date;
            $task->content = 'Confirm reservation of '.$tour_package->name;
            $task->start_time = $request->date. ' ' .$request->time;
            $task->dead_line = $request->date . ' ' . $request->time;
            $task->tour = $request->tour_id;
            // $task->assign = $user[$request->get('assign')];
            $task->assign = Auth::user()->id;
            $task->task_type = 1;
            $task->status = 2;
            $task->priority = 0;

            $tour = Tour::find($request->tour_id);
            if($tour) {
                $assigned_user = array_merge($tour->users->pluck('id')->toArray(), (array)$assigned_user);
            }

            $task->save();

            if ($assigned_user){
                $task->assigned_users()->sync($assigned_user);
                foreach ($assigned_user as $user) {
                    $notification = Notification::create(['content' => "New task {$task->content}", 'link' => '/task/'.$task->id]);
                    $user = User::findOrFail($user);
                    $user->notifications()->attach($notification);
                }
            }

        }

        return response()->json(true);
    }

    public function getDefaultTimes($type){
        return $this->defaultTimes[$type];
    }

    public function mainHotel($id, Request $request)
    {
        $tourPackage = TourPackage::find($id);
        if ($tourPackage->type == '0') {   // if hotel
            $tourDay = $tourPackage->tourDays->first();
            $tourDay->setMainHotel($id);

            return response()->json(true);
        }

        return response()->json(false);
    }

    public function apiGetStatus($id){
        $status = Status::query()
            ->where('id', $id)
            ->first();

        if ($status == null){
            return response()->json(false);
        }

        if($status->type == 'hotel' && $status->name == 'Confirmed'){
            return response()->json(true);
        }else{
            return response()->json(false);
        }
    }

    public function getTourPackageTransferDates($id){
        $tour_package_transfer = TourPackage::query()->where('id', $id)->first();

        if($tour_package_transfer){
            return response()->json([
                'dep_date' => $tour_package_transfer->time_from,
                'ret_date' => $tour_package_transfer->time_to
            ]);
        }else{
            return response()->json(false);
        }
    }

    public function validateTourPackage($request){
        $this->validate($request, [
            // 'name' => 'required',
            // 'description' => 'required',
            'serviceId' => 'required',
            'from_date' => 'before_or_equal:to_date',
            // 'pax'   => 'required|numeric',
            // 'pax_free'   => 'required|numeric',
            // 'total_amount' => 'required'
        ]);
    }


    public function supplier_update($id,Request $request){
        $tourPackage = TourPackage::findOrfail($id);
        dd($tourPackage);
    }
}
