<?php

namespace App\Http\Controllers;

use Amranidev\Ajaxis\Ajaxis;
use App\Bus;
use App\BusDay;
use App\BusHasDrivers;
use App\City;
use App\Comment;
use App\Country;
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
        $url = array('show'       => route('bus.show', $id),
            'edit'       => route('bus.edit', $id),
            'delete_msg' => "/bus/{$id}/deleteMsg");

        return DatatablesHelperController::getActionButton($url, false, $bus);
    }

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
    public function index(Request $request)
    {
        $title = 'Index - Buses';
        $search = trim((string) $request->input('search'));
        $buses = Bus::query()
            ->leftJoin('transfers', 'transfers.id', '=', 'buses.transfer_id')
            ->select([
                'buses.*',
                'transfers.name as transfer_name'
            ])
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('buses.name', 'like', "%{$search}%")
                        ->orWhere('buses.bus_number', 'like', "%{$search}%")
                        ->orWhere('transfers.name', 'like', "%{$search}%");
                });
            })
            ->orderBy('buses.name', 'asc')
            ->paginate(15)
            ->withQueryString();
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

        $bus = DB::transaction(function () use ($request) {
            $bus = $this->busRepository->create($request->only('name', 'bus_number', 'transfer_id', 'capacity', 'comments'));
            $this->addFile($request, $bus);
            return $bus;
        });

        $data = ['route' => route('bus.index')];
        LaravelFlashSessionHelper::setFlashMessage("Bus $bus->name created", 'success');
        return $request->expectsJson() ? response()->json($data) : redirect()->route('bus.index');
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

        $bus = Bus::query()->findOrFail($id);

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

        $bus = DB::transaction(function () use ($request, $id) {
            $this->busRepository->updateById($id, $request->only('name', 'bus_number', 'transfer_id', 'capacity', 'comments'));
            $bus = $this->busRepository->byId($id);
            $this->addFile($request, $bus);
            return $bus;
        });

        LaravelFlashSessionHelper::setFlashMessage("Bus $bus->name edited", 'success');
        $data = ['route' => route('bus.index')];
        return $request->expectsJson() ? response()->json($data) : redirect()->route('bus.index');
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

        if($busDay){
            $selected_drivers = TripToDrivers::query()->where('bus_day_id', $busDay->id)->get();
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
        $selected_drivers = TransferToDrivers::query()->where('tour_package_id', $tour_package_id)->where('bus_day_id', $bus_day_id)->get();

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
        Transfer::findOrFail($transfer_id);
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
            Transfer::findOrFail($transfer_id);
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
        // Same rules as creating a trip (an unknown status also crashed the activity log)
        $this->validate($request, array_merge([
            'id_bus_day' => 'required|integer',
            'trip_id' => 'required|integer|min:1',
            'trip_mode' => 'nullable|in:0,1',
        ], $this->tripReferenceRules('bus_id_trip', 'drivers_id_trip')), $this->tripReferenceMessages('bus_id_trip', 'drivers_id_trip'));

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
        abort_unless($bus_day && $bus_day->tour_package_id === null && (int) $bus_day->trip_id === (int) $trip_id, 404);
        $start_date = $bus_day->date;
        $end_date = $start_date;
        $excluded_trip_id = null;
        if ($trip_mode == 0) {
            $trip_days = BusDay::query()->where('trip_id', $trip_id)->orderBy('date')->get();
            $start_date = $trip_days->first()->date;
            $end_date = $trip_days->last()->date;
            $excluded_trip_id = $trip_id;
        }

        // validate buses and drivers
        $res_bus_validate = $this->busDayHelper
            ->validateBuses($bus_id_trip, $start_date, $end_date, null, $bus_day->id, $excluded_trip_id);


        if(!$res_bus_validate['check']){
            $data = [
                'bus_busy' => true,
                'bus_busy_message' => trans('main.TheBusisBusyfrom') . $res_bus_validate['start'] . trans('main.to') . $res_bus_validate['end']
            ];

            return response()->json($data);
        }

        //validate drivers
        $res_driver_validate = $this->busDayHelper
            ->validateDrivers($drivers_id_trip, $start_date, $end_date, null, $bus_day->id, $excluded_trip_id);

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
        $request->validate([
            'bus_id' => ['required', 'integer', \Illuminate\Validation\Rule::exists('buses', 'id')->whereNull('deleted_at')],
            'drivers_id' => 'nullable|array',
            'drivers_id.*' => ['integer', 'distinct', \Illuminate\Validation\Rule::exists('drivers', 'id')->whereNull('deleted_at')],
            'status' => ['required', 'integer', \Illuminate\Validation\Rule::exists('status', 'id')->where('type', 'bus')],
            'id_bus_day' => 'required|integer',
            'tour_package_id' => 'required|integer',
            'tour_mode' => 'required|in:0,1',
        ]);
        $bus_id = $request->get('bus_id', null);
        $drivers_id = $request->get('drivers_id', null);
        $status = $request->get('status', null);
        $bus_day_id = $request->get('id_bus_day', null);
        $tour_mode = $request->get('tour_mode', null);
        $tour_package_id = $request->get('tour_package_id', null);
        $bus_days = BusDay::query()
            ->where('tour_package_id', $tour_package_id)
            ->orderBy('date', 'asc')->get();

        $bus_day = $bus_days->firstWhere('id', $bus_day_id);
        abort_unless($bus_day && $tour_package_id, 404);
        $start_date = $tour_mode == 0 ? $bus_days->first()->date : $bus_day->date;
        $end_date = $tour_mode == 0 ? $bus_days->last()->date : $bus_day->date;
        $excluded_package_id = $tour_mode == 0 ? $tour_package_id : null;


        // validate buses
        $res_bus_validate = $this->busDayHelper->validateBuses($bus_id, $start_date, $end_date, $excluded_package_id, $bus_day->id);
        if(!$res_bus_validate['check']){
            $data = [
                'bus_busy' => true,
                'bus_busy_message' => trans('main.TheBusisBusyfrom') . $res_bus_validate['start'] . trans('main.to') . $res_bus_validate['end']
            ];

            return response()->json($data);
        }

//        validate drivers
        $res_driver_validate = $this->busDayHelper->validateDrivers($drivers_id, $start_date, $end_date, $excluded_package_id, $bus_day->id);
        if(!$res_driver_validate){
            $data = [
                'bus_busy' => true,
                'bus_busy_message' => trans('main.TheDriverisBusy')
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
            $bus_day->bus_id = $bus_id;
            $bus_day->save();

            // change transfer status
            $check_status_transfer = true;
            foreach ($bus_days as $bus_day_item){
                if($bus_day_item->status_id != $status){
                    $check_status_transfer = false;
                }
            }

            if($check_status_transfer){
                $this->busDayHelper->changeStatusTransferPackage($bus_day->tour_package_id, $status);
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
    public function destroy($id, DeleteModel $deleteModel, Request $request)
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


        if ($request->expectsJson()) {
            return response()->json(['success' => !$message, 'message' => $message]);
        }

        if ($request->ajax()) {
            return URL::to('bus');
        }

        return redirect()->route('bus.index');
    }

    public function validateBus($request){

        $this->validate($request, [
            'name'    => 'required',
            'bus_number' => 'required'
        ]);
    }


    /**
     * Calendar data for /bus/calendar: one row per bus with its trip/tour day segments.
     * Optional from/to (Y-m-d) choose the range; the default is this month plus six months.
     */
    public function getApiBusDays(Request $request){
        $this->validate($request, [
            'from' => 'nullable|date_format:Y-m-d',
            'to' => 'nullable|date_format:Y-m-d|after_or_equal:from',
        ]);

        $now = new \DateTime();
        $from = $request->input('from') ?: $now->format('Y-m') . '-01';
        $to = $request->input('to') ?: date('Y-m-d', strtotime('+6 months', strtotime($now->format('Y-m') . '-31')));
        // Keep one request bounded: at most 18 months of bus days
        if (strtotime($to) > strtotime('+18 months', strtotime($from))) {
            $to = date('Y-m-d', strtotime('+18 months', strtotime($from)));
        }

        $bus_days_all = BusDay::query()
            ->where('date', '>=', $from)
            ->where('date', '<=', $to)
            ->get();

        // Everything the segments need, loaded once instead of per bus day
        $dayIds = $bus_days_all->pluck('id');
        $tripCities = City::query()->whereIn('id', $bus_days_all->pluck('city_trip')->filter()->unique())->pluck('name', 'id');
        $tripCountries = Country::query()->whereIn('alias', $bus_days_all->pluck('country_trip')->filter()->unique())->pluck('name', 'alias');
        $tours = Tour::query()->whereIn('id', $bus_days_all->pluck('tour_id')->filter()->unique())->get()->keyBy('id');
        $buses = Bus::query()->whereIn('id', $bus_days_all->pluck('bus_id')->filter()->unique())->get()->keyBy('id');
        $tripDrivers = TripToDrivers::query()->whereIn('bus_day_id', $dayIds)->get()->groupBy('bus_day_id');
        $transferDrivers = TransferToDrivers::query()->whereIn('bus_day_id', $dayIds)->get()->groupBy('bus_day_id');
        $drivers = Driver::query()->whereIn('id', $tripDrivers->flatten()->pluck('driver_id')
            ->merge($transferDrivers->flatten()->pluck('driver_id'))->unique())->get()->keyBy('id');
        $statuses = Status::query()->whereIn('id', $bus_days_all->pluck('status_id')->filter()->unique())->get()->keyBy('id');
        $defaultStatus = Status::query()->where('type', 'bus')->orderBy('id', 'desc')->first();
        $commentCounts = Comment::query()
            ->where('reference_type', Comment::$services['BusDay'])
            ->whereIn('reference_id', $dayIds)
            ->selectRaw('reference_id, count(*) as total')
            ->groupBy('reference_id')
            ->pluck('total', 'reference_id');

        $driversFor = function ($links) use ($drivers) {
            $ids = [];
            $names = [];
            // Same order as before: drivers by id, as Driver::whereIn()->get() returned them
            foreach (collect($links)->pluck('driver_id')->unique()->sort()->values() as $driverId) {
                if ($driver = $drivers->get($driverId)) {
                    $ids[] = $driver->id;
                    $names[] = $driver->name;
                }
            }
            return [$ids, $names];
        };

        // Resolve tour hotel cities in batches, with no per-day hotel or city queries.
        $tourDates = TourDay::query()->with('packages')->whereIn('tour', $tours->keys())
            ->orderBy('date')->get(['id', 'date', 'tour']);
        $hotelPackages = $tourDates->flatMap(function ($day) { return $day->packages; })->where('type', 0);
        $hotelNames = $hotelPackages->map(function ($package) { return trim(explode('(', (string) $package->name)[0]); })->filter()->unique();
        $hotels = Hotel::query()->where(function ($query) use ($hotelPackages, $hotelNames) {
            $query->whereIn('id', $hotelPackages->pluck('reference')->filter()->unique())
                ->orWhereIn('name', $hotelNames);
        })->get(['id', 'name', 'city']);
        $hotelsById = $hotels->keyBy('id');
        $hotelsByName = $hotels->keyBy('name');
        $hotelCities = City::query()->whereIn('id', $hotels->pluck('city')->filter()->unique())->pluck('name', 'id');
        $tourCities = [];
        foreach ($tourDates as $day) {
            $city = null;
            foreach ($day->packages as $package) {
                if ((int) $package->type !== 0) continue;
                $hotel = $hotelsById->get($package->reference)
                    ?: $hotelsByName->get(trim(explode('(', (string) $package->name)[0]));
                if ($hotel && isset($hotelCities[$hotel->getRawOriginal('city')])) {
                    $city = $hotelCities[$hotel->getRawOriginal('city')];
                }
            }
            $tourCities[$day->tour][$day->date] = $city;
        }
        $citiesForTour = function ($tourId) use ($tourCities) {
            return $tourCities[$tourId] ?? [];
        };
        // One group per (tour package, bus), in the order the days are listed (was a GROUP BY
        // without aggregates, which fails under ONLY_FULL_GROUP_BY)
        $groups = $bus_days_all->unique(function ($day) {
            return $day->tour_package_id . '|' . $day->bus_id;
        });

        $dto = [];
        foreach ($groups as $bus_day){
            $groupTour = $bus_day->tour_id ? $tours->get($bus_day->tour_id) : null;
            if ($groupTour && $groupTour->is_quotation) {
                continue;
            }
            $bus = $buses->get($bus_day->bus_id);

            $segments = [];
            $city_count = 0;
            $city_tour = 0;

            foreach ($bus_days_all as $item){
                if ($item->bus_id == null) {
                    $city_count = 0;
                    continue;
                }
                if ($item->bus_id != $bus_day->bus_id || $bus_day->tour_package_id != $item->tour_package_id) {
                    continue;
                }

                $start = Carbon::parse($item->date);
                $end = Carbon::parse($start)->addDays(1);
                $status = $item->status_id != null ? $statuses->get($item->status_id) : $defaultStatus;
                $cities_separated = [];

                if ($item->tour_package_id == null) {
                    // trip
                    [$drivers_id_dto, $drivers_name_dto] = $driversFor($tripDrivers->get($item->id, []));
                    $tour_name = $item->name_trip;
                    $isTour = 'Trip';
                    $cities_separated = ($tripCities[$item->city_trip] ?? '') . ' (' . ($tripCountries[$item->country_trip] ?? '') . ')';
                } else {
                    // tour
                    $links = collect($transferDrivers->get($item->id, []))->where('tour_package_id', $item->tour_package_id);
                    [$drivers_id_dto, $drivers_name_dto] = $driversFor($links);
                    $tour = $item->tour_id ? $tours->get($item->tour_id) : null;
                    if ($tour) {
                        $cities_separated = $citiesForTour($tour->id)[$item->date] ?? '';
                        $tour_name = $tour['name'];
                    } else {
                        $tour_name = 'Tour Delete';
                    }
                    $isTour = 'Tour';
                }

                if ($city_tour != $item->tour_id) $city_count = 0;

                $segments[] = [
                    'start' => $start->format('Y-m-d'),
                    'end' => $end->format('Y-m-d'),
                    // A deleted or unknown status must not break the whole calendar
                    'color' => optional($status)->color ?: '#cccccc',
                    'id' => $item->id,
                    'tour' => $tour_name,
                    'tour_package_id' => $item->tour_package_id,
                    'driver_id' => $drivers_id_dto,
                    'driver_name' => $drivers_name_dto,
                    'comments' => (int) ($commentCounts[$item->id] ?? 0),
                    'bullet' => '#cccc00',
                    'transfer_id' => $item->transfer_id,
                    'tour_id' => $item->tour_id,
                    'isTour' => $isTour,
                    'cities' => $cities_separated,
                    'city_num' => $item->date
                ];

                $city_tour = $item->tour_id;
                $city_count++;
            }

            if ($bus) {
                $dto[] = [
                    'bus_id' => $bus->id,
                    'segments' => $segments
                ];
            }
        }

        // One row per bus (rows used to be merged by name, so buses sharing a name
        // shared a row, and a failed array_search dropped the first empty bus).
        $buses = Bus::query()->orderBy('name')->get(['id', 'name', 'bus_number']);
        $labelCounts = $buses->countBy(function ($bus) {
            return $bus->name . '(' . $bus->bus_number . ')';
        });

        $segmentsByBus = [];
        foreach ($dto as $item) {
            $segmentsByBus[$item['bus_id']] = array_merge($segmentsByBus[$item['bus_id']] ?? [], $item['segments']);
        }

        $finishResult = [];
        foreach ($buses as $bus) {
            $label = $bus->name . '(' . $bus->bus_number . ')';
            if ($labelCounts[$label] > 1) {
                $label .= ' #' . $bus->id; // same name and number as another bus
            }
            $segments = $segmentsByBus[$bus->id] ?? [];
            usort($segments, function ($a, $b) {
                return strcmp($a['start'], $b['start']);
            });
            $finishResult[] = [
                'category' => $label,
                'bus_id' => $bus->id,
                'segments' => $segments,
            ];
        }

        // Buses with trips first, then the rest by name
        usort($finishResult, function ($a, $b) {
            return (empty($a['segments']) <=> empty($b['segments'])) ?: strcasecmp($a['category'], $b['category']);
        });

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

        if($type_add == 'TRIP'){
            $this->validateDayTrip($request);
            $dates_interval = $this->busDayHelper->getDatesInterval($start_date, $end_date);

            // validate buses
            $res_bus_validate = $this->busDayHelper->validateBuses($bus_trip, $start_date, $end_date);
            if(!$res_bus_validate['check']){
                $data = [
                    'bus_busy' => true,
                    'bus_busy_message' => trans('main.TheBusisBusyfrom') . $res_bus_validate['start'] . trans('main.to') . $res_bus_validate['end']
                ];

                return response()->json($data);
            }

            //validate drivers
            $res_driver_validate = $this->busDayHelper->validateDrivers($drivers_trip, $start_date, $end_date);
            if(!$res_driver_validate){
                $data = [
                    'bus_busy' => true,
                    'bus_busy_message' => trans('main.TheDriverisBusy')
                ];

                return response()->json($data);
            }

            $request = CitiesHelper::setCityBegin($request);
            $request = CitiesHelper::setCityEnd($request);

            $country = $request->get('country_begin', null);
            $city = $request->get('city_begin', null);


            // created trip (all days or none: a failure used to leave a one-day trip behind)
            $created = DB::transaction(function () use ($name_trip, $dates_interval, $tour_id, $transfer_id, $status, $bus_trip, $city, $country, $drivers_trip) {
                $trip = new Trip();
                $trip->name = $name_trip;
                $trip->save();

                $busDayIds = [];
                foreach ($dates_interval as $item){
                    $busDayIds[] = $this->busDayHelper->createBusDayTrip(
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
                    )->id;
                }

                return ['trip_id' => $trip->id, 'bus_day_ids' => $busDayIds];
            });

            return response()->json(['success' => true] + $created);
        }

        // Tours are added to buses from the tour page, not from this endpoint
        return response()->json(['success' => false, 'message' => 'Only trips can be added here.'], 422);
    }

    /** Longest trip one request may create (one bus day per date) */
    const MAX_TRIP_DAYS = 60;

    /**
     * Rules for a trip's bus, drivers, status and name, shared by create and update
     * (the two forms name the bus/driver fields differently).
     */
    private function tripReferenceRules($busField, $driversField)
    {
        return [
            // Shown in the calendar tooltip: no markup or control characters
            'name_trip' => ['required', 'string', 'max:191', 'regex:' . \App\Services\ValidationService::SAFE_TEXT_REGEX],
            // A trip without an existing bus is never drawn but still blocks drivers ("ghost" trip)
            $busField => ['required', 'integer', \Illuminate\Validation\Rule::exists('buses', 'id')->whereNull('deleted_at')],
            $driversField => 'nullable|array',
            $driversField . '.*' => ['integer', 'distinct', \Illuminate\Validation\Rule::exists('drivers', 'id')->whereNull('deleted_at')],
            'status' => ['required', 'integer', \Illuminate\Validation\Rule::exists('status', 'id')->where('type', 'bus')],
        ];
    }

    private function tripReferenceMessages($busField, $driversField)
    {
        return [
            'name_trip.regex' => 'The trip name may not contain < > or control characters.',
            $busField . '.required' => 'Select a bus.',
            $busField . '.exists' => 'The selected bus does not exist.',
            $driversField . '.*.exists' => 'One of the selected drivers does not exist.',
            'status.required' => 'Select a status.',
            'status.exists' => 'The selected status is not a bus status.',
        ];
    }

    public function validateDayTrip($request){
        $maxEnd = $request->filled('start_date') && strtotime((string) $request->start_date)
            ? date('Y-m-d', strtotime('+' . (self::MAX_TRIP_DAYS - 1) . ' days', strtotime((string) $request->start_date)))
            : null;

        $this->validate($request, array_merge([
            'country_begin' => 'required',
            'city_begin' => 'required',
            'start_date' => 'required|date_format:Y-m-d',
            'end_date' => array_values(array_filter([
                'required', 'date_format:Y-m-d', 'after_or_equal:start_date',
                // Each day is a row (plus driver links): keep one trip bounded
                $maxEnd ? 'before_or_equal:' . $maxEnd : null,
            ])),
        ], $this->tripReferenceRules('bus_trip', 'drivers_trip')), array_merge($this->tripReferenceMessages('bus_trip', 'drivers_trip'), [
            'end_date.before_or_equal' => 'A trip can be at most ' . self::MAX_TRIP_DAYS . ' days long.',
        ]));
    }

    public function validateDayTour($request){

        $this->validate($request, [
            'tour'    => 'required',
            'transfer_tour'    => 'required',
            'start_date' => 'required|date_format:Y-m-d',
            'end_date' => 'required|date_format:Y-m-d|after_or_equal:start_date'
        ]);
    }

    public function calendar()
    {
        $title = 'Buses - Calendar';
        // The create-trip modal only lists ids and names (tours also show their dates)
        $buses = collect();
        $drivers = collect();
        // The calendar creates standalone trips; tour assignments use the tour page.
        $tours = collect();
        $transfers = collect();
        $bus_statuses = Status::query()->orderBy('sort_order', 'asc')->where('type','bus')->get();
        return view('bus.calendar', compact('buses', 'title','bus_statuses', 'drivers','tours', 'transfers'));
    }



    public function calendarOptions(Request $request)
    {
        $request->validate(['kind' => 'required|in:bus,driver', 'q' => 'nullable|string|max:191', 'page' => 'nullable|integer|min:1|max:10000']);
        $isBus = $request->input('kind') === 'bus';
        $query = $isBus ? Bus::query() : Driver::query();
        $search = trim((string) $request->input('q'));
        if ($search !== '') {
            $query->where(function ($query) use ($search, $isBus) {
                $query->where('name', 'like', '%' . $search . '%');
                if ($isBus) $query->orWhere('bus_number', 'like', '%' . $search . '%');
            });
        }
        $page = $query->orderBy('name')->orderBy('id')->simplePaginate(25, $isBus ? ['id', 'name', 'bus_number'] : ['id', 'name']);
        return response()->json([
            'results' => $page->getCollection()->map(function ($item) use ($isBus) {
                return ['id' => $item->id, 'text' => $item->name . ($isBus ? ' (' . $item->bus_number . ')' : '')];
            })->values(),
            'pagination' => ['more' => $page->hasMorePages()],
        ]);
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
        $request->validate([
            'trip_mode' => 'required|in:0,1',
            'trip_id' => 'required|integer|min:1',
            'bus_day_id' => 'required|integer|min:1',
        ]);
        $bus_day_id = $request->get('bus_day_id');
        $trip_mode = $request->get('trip_mode');
        $trip_id = $request->get('trip_id');
        BusDay::query()->where('id', $bus_day_id)->where('trip_id', $trip_id)->whereNull('tour_package_id')->firstOrFail();

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
