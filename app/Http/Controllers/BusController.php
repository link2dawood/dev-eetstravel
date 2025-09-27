<?php

namespace App\Http\Controllers;

use Amranidev\Ajaxis\Ajaxis;
use App\Bus;
use App\BusDay;
use App\BusHasDrivers;
use App\City;
use App\Comment;
use App\Driver;
use App\Helper\BusDayHelper;
use App\Helper\CitiesHelper;
use App\Helper\FileTrait;
use App\Helper\LaravelFlashSessionHelper;
use App\Helper\PermissionHelper;
use App\Hotel;
use App\Repository\Contracts\BusRepository;
use App\TourPackage;
use App\Transfer;
use App\TransferToBuses;
use App\TransferToDrivers;
use App\Trip;
use App\TripToDrivers;
use Carbon\Carbon;
use DateInterval;
use DatePeriod;
use DB;
use Illuminate\Http\Request;
use PhpParser\Node\Expr\Array_;
use URL;
use App\Http\Requests\StoreBusRequest;
use App\Http\Requests\UpdateBusRequest;
use View;
use Yajra\Datatables\Datatables;
use App\Status;
use App\TourDay;
use App\Tour;
use Illuminate\Support\Facades\Session;
use App\Library\Services\DeleteModel;

class BusController extends Controller
{
    use FileTrait;


    /**
     * @var BusRepository
     */
    protected $busRepository;
    /**
     * @var BusDayHelper
     */
    private $busDayHelper;


    public function getButton($id, $bus)
    {
        $url = array('show'       => route('bus.show', ['id' => $id]),
            'edit'       => route('bus.edit', ['id' => $id]),
            'delete_msg' => "/bus/{$id}/deleteMsg");

        return DatatablesHelperController::getActionButton($url, false, $bus);
    }

    public function data(Request $request)
    {
        return Datatables::of(Bus::query()
            ->leftJoin('transfers', 'transfers.id', '=', 'buses.transfer_id')
            ->select(
                [
                    'buses.*',
                    'transfers.name as transfer_name'
                ])
            )
            ->addColumn('action', function ($bus) {
                return $this->getButton($bus->id, $bus);
            })
            ->rawColumns(['action'])
            ->make(true);
    }

    /**
     * BusController constructor.
     * @param BusRepository $busRepository
     * @param BusDayHelper $busDayHelper
     */
    public function __construct(BusRepository $busRepository, BusDayHelper $busDayHelper)
    {
        $this->middleware('permissions.required');
        $this->busRepository = $busRepository;
        $this->busDayHelper = $busDayHelper;
        $this->middleware('preventBackHistory');
        $this->middleware('auth');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $title = 'Index - Buses';
        $buses = Bus::query()
            ->leftJoin('transfers', 'transfers.id', '=', 'buses.transfer_id')
            ->select([
                'buses.*',
                'transfers.name as transfer_name'
            ])
            ->paginate(15);
        return view('bus.index', compact('buses', 'title'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $title = 'Create - Bus';

        $transfers = Transfer::all()->pluck('name', 'id')->toArray();

        return view('bus.create', compact('title', 'transfers'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreBusRequest $request)
    {

        DB::beginTransaction();
        $bus = $this->busRepository->create($request->except('attach'));

        $this->addFile($request, $bus);

        DB::commit();

        $data = ['route' => route('bus.index')];
        LaravelFlashSessionHelper::setFlashMessage("Bus $bus->name created", 'success');
		//return redirect()->back();
        return response()->json($data);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id, Request $request)
    {
        $title = 'Show - Bus';

        $bus = Bus::query()->where('id', $id)->first();

        if($bus == null){
            return abort(404);
        }

        $files = $this->parseAttach($bus);

        return view('bus.show', compact('title', 'bus', 'files'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id, Request $request)
    {
        $title = 'Edit - Bus';

        $bus = Bus::query()->where('id', $id)->first();

        $transfers = Transfer::all()->pluck('name', 'id')->toArray();

        $files = $this->parseAttach($bus);

        return view('bus.edit', compact('title', 'bus', 'files', 'transfers'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateBusRequest $request, $id)
    {

        DB::beginTransaction();
        $this->busRepository->updateById($id, $request->except('attach'));
        $bus = $this->busRepository->byId($id);
        $this->addFile($request, $bus);

        DB::commit();

        LaravelFlashSessionHelper::setFlashMessage("Bus $bus->name edited", 'success');
        $data = ['route' => route('bus.index')];
        return response()->json($data);
    }


    public function DeleteMsg($id, Request $request)
    {
//        $msg = Ajaxis::BtDeleting('Warning!!', 'Would you like to remove This?', '/bus/' . $id . '/delete');
        $msg = Ajaxis::BtDeleting(trans('main.Warning').'!!',trans('main.WouldyouliketoremoveThis').'?', '/bus/' . $id . '/delete');

        if ($request->ajax()) {
            return $msg;
        }
    }

    public function generateFormTrip(Request $request){
        $bus_day_id = $request->get('bus_day_id', null);
        $busDay = BusDay::query()->where('id', $bus_day_id)->first();
        $bus_statuses = Status::query()->orderBy('sort_order', 'asc')->where('type', 'bus')->get();
        $buses = Bus::query()->orderBy('name', 'asc')->get();
        $drivers = Driver::query()->orderBy('name', 'asc')->get();
        $selected_drivers = TripToDrivers::query()->where('bus_day_id', $busDay->id)->get();

        if($busDay){
            $view = View::make('component.generateFormTrip',
                compact('busDay', 'bus_statuses', 'buses', 'drivers', 'selected_drivers')
            );

            $contents = $view->render();

            return $contents;
        }else{
            return response()->json(false);
        }
    }

    public function generateFormTour(Request $request){
        $tour_package_id = $request->get('tour_package_id', null);
        $transfer_id = $request->get('transfer_id', null);
        $bus_day_id = $request->get('bus_day_id', null);
        $bus_statuses = Status::query()->orderBy('sort_order', 'asc')->where('type', 'bus')->get();
        $busDay = BusDay::query()->where('tour_package_id', $tour_package_id)->where('id', $bus_day_id)->first();
        $buses = Bus::query()->where('transfer_id', $transfer_id)->orderBy('name', 'asc')->get();
        $drivers = Driver::query()->where('transfer_id', $transfer_id)->orderBy('name', 'asc')->get();
        $selected_drivers = TransferToDrivers::query()->where('tour_package_id', $tour_package_id)->get();

        if($busDay){
            $view = View::make('component.generateFormTour',
                compact('busDay', 'bus_statuses', 'buses', 'drivers', 'selected_drivers')
            );

            $contents = $view->render();

            return $contents;
        }else{
            return response()->json(false);
        }
    }

    public function getDriverAndBusTransfer($transfer_id){
        $drivers = Driver::query()->where('transfer_id', $transfer_id)->get();
        $buses = Bus::query()->where('transfer_id', $transfer_id)->get();

        $view = View::make(
            'component.get_drivers_buses_transfer',
            [
                'drivers'   => $drivers,
                'buses'   => $buses
            ]
        );

        $contents = $view->render();

        return response()->json($contents);
    }

    public function getDriverAndBusTransferForTable($transfer_id, Request $request){
        $view = null;
        if($transfer_id != 'null'){
            $drivers = Driver::query()->where('transfer_id', $transfer_id)->get();
            $buses = Bus::query()->where('transfer_id', $transfer_id)->get();

            $selected_drivers = TransferToDrivers::query()
                ->where('transfer_id', $transfer_id)
                ->where('tour_id', $request->tour_id)
                ->get();

            $selected_buses = TransferToBuses::query()
                ->where('transfer_id', $transfer_id)
                ->where('tour_id', $request->tour_id)
                ->get();

            $view = View::make(
                'component.get_drivers_buses_transfer_table',
                [
                    'drivers'   => $drivers,
                    'selected_drivers'   => $selected_drivers,
                    'selected_buses'   => $selected_buses,
                    'buses'   => $buses
                ]
            );
        }else{
            $drivers = Driver::query()->get();
            $buses = Bus::query()->get();
            $bus_day = BusDay::query()
                ->where('id', $request->bus_id)
                ->first();


            $view = View::make(
                'component.get_driver_and_buses_one_trip',
                [
                    'drivers' => $drivers,
                    'buses' => $buses,
                    'bus_day' => $bus_day
                ]
            );

        }

        $contents = $view->render();

        return response()->json($contents);
    }

    public function updateBusDay(Request $request){
        $request = CitiesHelper::setCityBegin($request);
        $request = CitiesHelper::setCityEnd($request);

        $country = $request->get('country_begin', null);
        $city = $request->get('city_begin', null);
        $transfer_id = $request->get('transfer_id', null);
        $tour_id = $request->get('tour_id', null);
        $bus_day_id = $request->get('id_bus_day', null);
        $status = $request->get('status', null);
        $name_trip = $request->get('name_trip', null);
        $bus_id_trip = $request->get('bus_id_trip', null);
        $drivers_id_trip = $request->get('drivers_id_trip', null);
        $trip_mode = $request->get('trip_mode', 0);
        $trip_id = $request->get('trip_id', 0);

        $bus_day = $this->busDayHelper->getBusDayId($bus_day_id);
        $start_date = $bus_day->date;

        // validate buses and drivers
        $res_bus_validate = $this->busDayHelper
            ->validateBuses($bus_id_trip, $start_date, $start_date, null, $bus_day->id);


        if(!$res_bus_validate['check']){
            $data = [
                'bus_busy' => true,
                'bus_busy_message' => trans('main.TheBusisBusyfrom') . $res_bus_validate['start'] . ' to ' . $res_bus_validate['end']
            ];

            return response()->json($data);
        }

        //validate drivers
        $res_driver_validate = $this->busDayHelper
            ->validateDrivers($drivers_id_trip, $start_date, $start_date, null, $bus_day->id);

        if(!$res_driver_validate){
            $data = [
                'bus_busy' => true,
                'bus_busy_message' => trans('main.TheDriverisBusy')
            ];

            return response()->json($data);
        }


        // update for full trip
        if($trip_mode == 0){
            $bus_days_full_trip = BusDay::query()->where('trip_id', $trip_id)->get();

            foreach ($bus_days_full_trip as $item){
                DB::beginTransaction();
                $drivers_trip = $this->busDayHelper->getDriversToTrip($item->id);
                $old_drivers_trip_id = [];
                $new_drivers_trip_id = [];

                if($drivers_trip->isNotEmpty()){
                    foreach ($drivers_trip as $driver_trip){
                        $old_drivers_trip_id[] = $driver_trip->driver_id;
                    }
                }

                if($drivers_id_trip){
                    foreach ($drivers_id_trip as $driver_id_trip){
                        $new_drivers_trip_id[] = (int) $driver_id_trip;
                    }
                }

                // getting new drivers for creating and old drivers for deleting
                $new_drivers = array_values(array_diff($new_drivers_trip_id, $old_drivers_trip_id));
                $deleted_drivers = array_values(array_diff($old_drivers_trip_id, $new_drivers_trip_id));


                foreach ($new_drivers as $new_driver){
                    $this->busDayHelper->addDriverToTrip($new_driver, $item->id);

                }

                foreach ($deleted_drivers as $deleted_driver){
                    $this->busDayHelper->deleteDriverToTrip($item->id, $deleted_driver);
                }

                $item->tour_id = $tour_id;
                $item->transfer_id = $transfer_id;
                $item->bus_id = $bus_id_trip;
                $item->status_id = $status;
                $item->name_trip = $name_trip;
                $item->city_trip = $city;
                $item->country_trip = $country;
                $item->save();

                DB::commit();
            }
        }
        // update for day trip
        else{
            DB::beginTransaction();

            $this->busDayHelper->deleteDriversToTrip($bus_day_id);
            $this->busDayHelper->addDriversToTrip($drivers_id_trip, $bus_day_id);

            $bus_day = $this->busDayHelper->getBusDayId($bus_day_id);
            $bus_day->tour_id = $tour_id;
            $bus_day->transfer_id = $transfer_id;
            $bus_day->bus_id = $bus_id_trip;
            $bus_day->status_id = $status;
            $bus_day->name_trip = $name_trip;
            $bus_day->city_trip = $city;
            $bus_day->country_trip = $country;
            $bus_day->save();

            DB::commit();
        }


        return response()->json(true);
    }

    public function updateBusDayTour(Request $request){
        $bus_id = $request->get('bus_id', null);
        $drivers_id = $request->get('drivers_id', null);
        $status = $request->get('status', null);
        $bus_day_id = $request->get('id_bus_day', null);
        $tour_mode = $request->get('tour_mode', null);
        $tour_package_id = $request->get('tour_package_id', null);
        $bus_days = BusDay::query()
            ->where('tour_package_id', $tour_package_id)
            ->orderBy('date', 'asc')->get();

        $start_date = $bus_days->first()->date;
        $end_date = $bus_days->last()->date;


        // validate buses
        $res_bus_validate = $this->busDayHelper->validateBuses($bus_id, $start_date, $end_date, $tour_package_id);
        if(!$res_bus_validate['check']){
            $data = [
                'bus_busy' => true,
                'bus_busy_message' => trans('main.TheBusisBusyfrom') . $res_bus_validate['start'] . trans('main.to') . $res_bus_validate['end']
            ];

            return response()->json($data);
        }

//        validate drivers
        $res_driver_validate = $this->busDayHelper->validateDrivers($drivers_id, $start_date, $end_date, $tour_package_id);
        if(!$res_driver_validate){
            $data = [
                'bus_busy' => true,
                'bus_busy_message' => 'The Driver is Busy'
            ];

            return response()->json($data);
        }


        $bus_day = BusDay::query()->where('id', $bus_day_id)->first();

        // update full tour
        if($tour_mode == 0){
            $this->busDayHelper->changeStatusTransferPackage($bus_day->tour_package_id, $status);
            $this->busDayHelper->deleteDriversToTransfer($tour_package_id);

            foreach ($bus_days as $bus_day){
                $bus_day->status_id = $status;
                $bus_day->bus_id = $bus_id;
                $bus_day->save();

                $this->busDayHelper->addDriversToTransfer($drivers_id, $bus_day->tour_id, $bus_day->transfer_id, $tour_package_id, $bus_day->id);
            }

        }
        // update tour day
        else{

            $this->busDayHelper->deleteDriversToTransfer($tour_package_id, $bus_day->id);
            $this->busDayHelper->addDriversToTransfer($drivers_id, $bus_day->tour_id, $bus_day->transfer_id, $tour_package_id, $bus_day->id);

            $bus_day->status_id = $status;
            $bus_day->save();

            // change transfer status
            $check_status_transfer = true;
            foreach ($bus_days as $bus_day_item){
                if($bus_day_item->status != $status){
                    $check_status_transfer = false;
                }
            }

            if($check_status_transfer){
                $this->busDayHelper->changeStatusTransferPackage($bus_day->tour_package_id, $status);
            }

            if ($bus_id != $bus_day->bus_id){
                foreach ($bus_days as $bus_day_item){
                    $bus_day_item->bus_id = $bus_id;
                    $bus_day_item->save();
                }
            }

        }


        return response()->json(true);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id, DeleteModel $deleteModel)
    {
        $bus = $this->busRepository->byId($id);
        $message = $deleteModel->check($bus, 'Bus');
        if ($message){
            Session::flash('message', $message);
        } else {
            LaravelFlashSessionHelper::setFlashMessage("Bus $bus->name deleted", 'success');
            $this->busRepository->deleteById($id);
            $this->removeFile($bus);
            Comment::query()->where('reference_type', Comment::$services['bus'])->where('reference_id', $id)->delete();
        }


        return URL::to('bus');
    }

    public function validateBus($request){

        $this->validate($request, [
            'name'    => 'required',
            'bus_number' => 'required'
        ]);
    }


    public function getApiBusDays(Request $request){

        $now = new \DateTime();
        $year = $now->format("Y");
        $month = $now->format("m");
        $last = $now->modify('last day of this month');
        $day = $last->format("d");
        $from = $year."-". $month."-01";
        $end = $year."-". $month."-31";

        $allBuses = [];
       // $to = $year."-". $month_end."-". $day;
        $to = date('Y-m-d', strtotime("+6 months", strtotime($end)));

        foreach(Bus::all() as $bus_){
            $allBuses[] = [$bus_->name."(".$bus_->bus_number.")"] ;
        }
        $bus_days = BusDay::query()
            ->groupBy('tour_package_id')
            ->groupBy('bus_id')
            ->where('date', '>=', $from)
            ->where('date', '<=', $to)
            ->get();

        $bus_days_all = BusDay::query()
            ->where('date', '>=', $from)
            ->where('date', '<=', $to)
            ->get();


        $dto = [];
        foreach ($bus_days as $bus_day){
        	if(is_object($bus_day->tour) && $bus_day->tour->is_quotation) {

        		continue;
	        }
            $bus = Bus::query()->where('id', $bus_day->bus_id)->first();

            $category = [];
            if($bus){
                $category[] = $bus->name."(".$bus->bus_number.")" ;
            }

            $segments = [];
            $city_count = 0;
            $city_tour = 0;
/*
            foreach ($bus_days_all as $item){
                if($item->bus_id != null){

                    if(($item->bus_id == $bus_day->bus_id) && ($bus_day->tour_package_id == $item->tour_package_id)){

                        $start = Carbon::parse($item->date);
                        $end = Carbon::parse($start)->addDays(1);

                        $drivers_id_dto = [];
                        $drivers_name_dto = [];
                        $status = null;
                        $comments = [];
                        $tour_name = '';
                        $isTour = '';
                        $cities_separated = '';
                        $cities = [];

                       // trip
                        if($item->tour_package_id == null){
                            $trip_drivers = TripToDrivers::query()
                                ->where('bus_day_id', $item->id)->get();

                            $drivers_id = [];
                            foreach ($trip_drivers as $trip_driver){
                                $drivers_id[] = $trip_driver->driver_id;
                            }

                            $drivers = Driver::query()->whereIn('id', $drivers_id)->get();

                            foreach ($drivers as $driver){
                                $drivers_id_dto[] = $driver->id;
                                $drivers_name_dto[] = $driver->name;
                            }


                            $status = $item->status_id != null ?
                                Status::query()
                                    ->where('id', $item->status_id)->first() :
                                Status::query()
                                    ->where('type', 'bus')
                                    ->orderBy('id','desc')->first();

                            $comments = Comment::query()
                                ->where('reference_type', Comment::$services['BusDay'])
                                ->where('reference_id', $item->id)->get();


                            $tour_name = $item->name_trip;
                            $isTour = 'Trip';
                            $cities_separated = $item->city_trip() . ' (' . $item->country_trip() . ')';
                        }
                        // tour
                        else{
                            $drivers_transfer = TransferToDrivers::query()
                                ->where('tour_package_id', $item->tour_package_id)
                                ->where('bus_day_id', $item->id)
                                ->get();

                            $drivers_id = [];
                            foreach ($drivers_transfer as $item_driver){
                                $drivers_id[] = $item_driver->driver_id;
                            }

                            $drivers = Driver::query()->whereIn('id', $drivers_id)->get();

                            foreach ($drivers as $driver){
                                $drivers_id_dto[] = $driver->id;
                                $drivers_name_dto[] = $driver->name;
                            }

                            $status = $item->status_id != null ?
                                Status::query()
                                    ->where('id', $item->status_id)->first() :
                                Status::query()
                                    ->where('type', 'bus')
                                    ->orderBy('id','desc')->first();

                            $comments = Comment::query()
                                ->where('reference_type',Comment::$services['BusDay'])
                                ->where('reference_id',$item->id)
                                ->get();


                            $tour = Tour::query()->where('id', $item->tour_id)->first();

                            if($tour) {
                                $tourDates = TourDay::query()
                                    ->get(['id', 'date', 'tour'])
                                    ->where('tour',$item->tour_id)
                                    ->sortBy('date');

                                $date_city = 0;

                                foreach ($tourDates as $tourDate) {
                                    $tourDate->packages = $tourDate->packages->sortBy('time_from');

                                    $cities[$date_city] = null;

                                    foreach ($tourDate->packages as $package) {

                                        if ($package->type == 0) {

                                            $hotels = explode("(", $package->name);

                                            $hotel = Hotel::query()->where('name', $hotels[0])->orWhere('name', 'like', '%' . $hotels[0] . '%')->first();
                                            if($hotel) {
                                                $city = Hotel::query()->join('cities', 'hotels.city', '=', 'cities.id')
                                                    ->select('cities.name')
                                                    ->where('hotels.id', $hotel['id'])
                                                    ->first();

                                                if($city['name']) {
                                                    $cities[$date_city] = $city['name'];
                                                }
                                            }

                                        }
                                    }
                                    $date_city ++;
                                }

                                $tour_name = $tour['name'];
                                $isTour = 'Tour';
                            }else{
                                $tour_name = 'Tour Delete';
                                $isTour = 'Tour';
                            }

                            if(count($cities) > 0 ){
                                $cities_separated = $cities;
                            }else{
                                $cities_separated =[];
                            }

                        }

                        if ($city_tour != $item->tour_id) $city_count = 0;

                        $segments[] = [
                            'start' => $start->format('Y-m-d'),
                            'end' => $end->format('Y-m-d'),
                            'color' => $status->color,
                            'id' => $item->id,
                            'tour' => $tour_name,
                            'tour_package_id' => $item->tour_package_id,
                            'driver_id' => $drivers_id_dto,
                            'driver_name' => $drivers_name_dto,
                            'comments' => count($comments),
                            'bullet' => '#cccc00',
                            'transfer_id' => $item->transfer_id,
                            'tour_id' => $item->tour_id,
                            'isTour' => $isTour,
                            'bullet' => '#cccc00',
                            'cities' => $cities_separated,
                            'city_num' => $city_count
                        ];

                        $city_tour= $item->tour_id;
                        $city_count++;
                    }
                }else{
                    $city_count = 0;
                    continue;
                }
            }

            $dto[] = [
                'category' => $category,
                'segments' => $segments
            ];
        }
*/        

            foreach ($bus_days_all as $item){
                if($item->bus_id != null){

                    if(($item->bus_id == $bus_day->bus_id) && ($bus_day->tour_package_id == $item->tour_package_id)){

                        $start = Carbon::parse($item->date);
                        $end = Carbon::parse($start)->addDays(1);

                        $drivers_id_dto = [];
                        $drivers_name_dto = [];
                        $status = null;
                        $comments = [];
                        $tour_name = '';
                        $isTour = '';
                        $cities_separated = '';
                        $cities = [];

                       // trip
                        if($item->tour_package_id == null){
                            $trip_drivers = TripToDrivers::query()
                                ->where('bus_day_id', $item->id)->get();

                            $drivers_id = [];
                            foreach ($trip_drivers as $trip_driver){
                                $drivers_id[] = $trip_driver->driver_id;
                            }

                            $drivers = Driver::query()->whereIn('id', $drivers_id)->get();

                            foreach ($drivers as $driver){
                                $drivers_id_dto[] = $driver->id;
                                $drivers_name_dto[] = $driver->name;
                            }


                            $status = $item->status_id != null ?
                                Status::query()
                                    ->where('id', $item->status_id)->first() :
                                Status::query()
                                    ->where('type', 'bus')
                                    ->orderBy('id','desc')->first();

                            $comments = Comment::query()
                                ->where('reference_type', Comment::$services['BusDay'])
                                ->where('reference_id', $item->id)->get();


                            $tour_name = $item->name_trip;
                            $isTour = 'Trip';
                            $cities_separated = $item->city_trip() . ' (' . $item->country_trip() . ')';
                        }
                        // tour
                        else{
                            $drivers_transfer = TransferToDrivers::query()
                                ->where('tour_package_id', $item->tour_package_id)
                                ->where('bus_day_id', $item->id)
                                ->get();

                            $drivers_id = [];
                            foreach ($drivers_transfer as $item_driver){
                                $drivers_id[] = $item_driver->driver_id;
                            }

                            $drivers = Driver::query()->whereIn('id', $drivers_id)->get();

                            foreach ($drivers as $driver){
                                $drivers_id_dto[] = $driver->id;
                                $drivers_name_dto[] = $driver->name;
                            }

                            $status = $item->status_id != null ?
                                Status::query()
                                    ->where('id', $item->status_id)->first() :
                                Status::query()
                                    ->where('type', 'bus')
                                    ->orderBy('id','desc')->first();

                            $comments = Comment::query()
                                ->where('reference_type',Comment::$services['BusDay'])
                                ->where('reference_id',$item->id)
                                ->get();


                            $tour = Tour::query()->where('id', $item->tour_id)->first();

                            if($tour) {
                                $tourDates = TourDay::query()
                                    ->get(['id', 'date', 'tour'])
                                    ->where('tour',$item->tour_id)
                                    ->sortBy('date');

                                $date_city = 0;

                                foreach ($tourDates as $tourDate) {
                                    $tourDate->packages = $tourDate->packages->sortBy('time_from');

                                    $cities[$date_city] = null;

                                    foreach ($tourDate->packages as $package) {

                                        if ($package->type == 0) {

                                            $hotels = explode("(", $package->name);

                                            $hotel = Hotel::query()->where('name', $hotels[0])->orWhere('name', 'like', '%' . $hotels[0] . '%')->first();
                                            if($hotel) {
                                                $city = Hotel::query()->join('cities', 'hotels.city', '=', 'cities.id')
                                                    ->select('cities.name')
                                                    ->where('hotels.id', $hotel['id'])
                                                    ->first();

                                                if($city['name']) {
                                                    $cities[$date_city] = $city['name'];
                                                }
                                            }

                                        }
                                    }
                                    $date_city ++;
                                }

                                $tour_name = $tour['name'];
                                $isTour = 'Tour';
                            }else{
                                $tour_name = 'Tour Delete';
                                $isTour = 'Tour';
                            }

                            if(count($cities) > 0 ){
                                $cities_separated = $cities;
                            }else{
                                $cities_separated =[];
                            }

                        }

                        if ($city_tour != $item->tour_id) $city_count = 0;

                        $segments[] = [
                            'start' => $start->format('Y-m-d'),
                            'end' => $end->format('Y-m-d'),
                            'color' => $status->color,
                            'id' => $item->id,
                            'tour' => $tour_name,
                            'tour_package_id' => $item->tour_package_id,
                            'driver_id' => $drivers_id_dto,
                            'driver_name' => $drivers_name_dto,
                            'comments' => count($comments),
                            'bullet' => '#cccc00',
                            'transfer_id' => $item->transfer_id,
                            'tour_id' => $item->tour_id,
                            'isTour' => $isTour,
                            'bullet' => '#cccc00',
                            'cities' => $cities_separated,
                            'city_num' => $city_count
                        ];

                        $city_tour= $item->tour_id;
                        $city_count++;
                    }
                }else{
                    $city_count = 0;
                    continue;
                }
            }

            $dto[] = [
                'category' => $category,
                'segments' => $segments
            ];
        }

        $result = array();
        foreach ($dto as $item){
            $key = '';
            asort($item['category']);
            foreach ($item['category'] as $category){
                $key .= $category;
            }

            $segments = array();
            $segments['segments'] = collect();
            if (isset($result[$key])){
                $segments = $result[$key];
            }
            $segments['segments'] = $segments['segments']->concat($item['segments']);
            $result[$key] = $segments;
            $result[$key]['category'] = $item['category'];
        }

        $finishResult = array();
        foreach ($result as $key => $segments) {
            if (count($segments['category']) > 0){
                $finishResult[] = [
                    'category' => array_values($segments['category']),
                    'segments' => $segments['segments']->all(),
                ];
                $pos = array_search(array_values($segments['category']), $allBuses);
                if ($pos !== null){
                    unset($allBuses[$pos]);
                }
            }
        }

        foreach($allBuses as $allB){
            $finishResult[] = [
                    'category' => $allB,
                    'segments' => [],
                ];
        }

        return response()->json($finishResult);
    }


    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function addApiBusDays(Request $request){
        $type_add = $request->form_mode == 0 ? 'TRIP' : 'TOUR';
        $start_date = $request->get('start_date', null);
        $end_date = $request->get('end_date', null);
        $name_trip = $request->get('name_trip', null);
        $tour_id = $request->get('tour', null);
        $transfer_id = $request->get('transfer_tour', null);
        $status = $request->get('status', null);
        $bus_trip = $request->get('bus_trip', null);
        $drivers_trip = $request->get('drivers_trip', null);

        $dates_interval = $this->busDayHelper->getDatesInterval($start_date, $end_date);


        if($type_add == 'TRIP'){
            $this->validateDayTrip($request);

            // validate buses
            $res_bus_validate = $this->busDayHelper->validateBuses($bus_trip, $start_date, $end_date);
            if(!$res_bus_validate['check']){
                $data = [
                    'bus_busy' => true,
                    'bus_busy_message' => 'The Bus is Busy from ' . $res_bus_validate['start'] . ' to ' . $res_bus_validate['end']
                ];

                return response()->json($data);
            }

            //validate drivers
            $res_driver_validate = $this->busDayHelper->validateDrivers($drivers_trip, $start_date, $end_date);
            if(!$res_driver_validate){
                $data = [
                    'bus_busy' => true,
                    'bus_busy_message' => 'The Driver is Busy'
                ];

                return response()->json($data);
            }

            $request = CitiesHelper::setCityBegin($request);
            $request = CitiesHelper::setCityEnd($request);

            $country = $request->get('country_begin', null);
            $city = $request->get('city_begin', null);


            // created trip
            $trip = new Trip();
            $trip->name = $name_trip;
            $trip->save();

            foreach ($dates_interval as $item){
                $this->busDayHelper->createBusDayTrip(
                    $tour_id,
                    $transfer_id,
                    null,
                    $item['date'],
                    $status,
                    $bus_trip,
                    $name_trip,
                    $city,
                    $country,
                    $drivers_trip,
                    $trip->id
                );
            }
        }

        return response()->json($request);

    }

    public function validateDayTrip($request){

        $this->validate($request, [
            'name_trip'    => 'required',
            'country_begin' => 'required',
            'city_begin' => 'required',
            'start_date' => 'required',
            'end_date' => 'required'
        ]);
    }

    public function validateDayTour($request){

        $this->validate($request, [
            'tour'    => 'required',
            'transfer_tour'    => 'required',
            'start_date' => 'required',
            'end_date' => 'required'
        ]);
    }

    public function calendar()
    {
        $title = 'Buses - Calendar';
        $buses = Bus::all();
        $drivers = Driver::all();
        $tours = Tour::all();
        $transfers = Transfer::all();
        $bus_statuses = Status::query()->orderBy('sort_order', 'asc')->where('type','bus')->get();
        return view('bus.calendar', compact('buses', 'title','bus_statuses', 'drivers','tours', 'transfers'));
    }



    public function getDriversTransfer($id){
        $drivers = Driver::query()->where('transfer_id', $id)->orderBy('name', 'asc')->get();

        if($drivers->isNotEmpty()){
            $view = View::make('component.drivers_transfer',
                compact(
                    'drivers'
                )
            );

            $contents = $view->render();

            return $contents;
        }else{
            return response()->json(false);
        }
    }

    public function getBusesTransfer($id){
        $buses = Bus::query()->where('transfer_id', $id)->orderBy('name', 'asc')->get();

        if($buses->isNotEmpty()){
            $view = View::make('component.buses_transfer',
                compact(
                    'buses'
                )
            );

            $contents = $view->render();

            return $contents;
        }else{
            return response()->json(false);
        }
    }

    public function getCityCountryBusDay($id){
        $bus_day = BusDay::query()->where('id', $id)->first();
        if ($bus_day){

            $view = View::make('component.city_country_bus_day_trip',
                compact(
                    'bus_day'
                )
            );

            $contents = $view->render();

            return $contents;
        }else{
            return false;
        }
    }

    public function deleteBusDay(Request $request){
        $bus_day_id = $request->get('bus_day_id');
        $trip_mode = $request->get('trip_mode');
        $trip_id = $request->get('trip_id');

        // delete for full trip
        if($trip_mode == 0){
            $bus_days_trip_full = BusDay::query()->where('trip_id', $trip_id)->get();
            if($bus_days_trip_full->isNotEmpty()){
                $bus_days_ids = [];
                foreach ($bus_days_trip_full as $item){
                    $bus_days_ids[] = $item->id;
                }

                $trip = Trip::findOrfail($trip_id);
                $trip->delete();
                TripToDrivers::query()->whereIn('bus_day_id', $bus_days_ids)->delete();
                BusDay::query()->where('trip_id', $trip_id)->delete();
            }
        }
        // delete for day trip
        else{
            TripToDrivers::query()->where('bus_day_id', $bus_day_id)->delete();
            BusDay::query()->where('id', $bus_day_id)->delete();

            $bus_day = BusDay::query()->where('trip_id', $trip_id)->first();

            if(!$bus_day){
                $trip = Trip::findOrfail($trip_id);
                $trip->delete();
            }
        }

        return response()->json(true);
    }
}
