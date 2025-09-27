<?php

namespace App\Http\Controllers;

use Amranidev\Ajaxis\Ajaxis;
use App\City;
use App\Country;
use App\Comment;
use App\Criteria;
use App\Flight;
use App\GooglePlaces;
use App\Helper\FileTrait;
use App\Helper\CitiesHelper;
use App\Helper\GooglePlacesHelper;
use App\Helper\LaravelFlashSessionHelper;
use App\Helper\PermissionHelper;
use App\Rate;
use App\Repository\Contracts\FlightRepository;
use App\ServicesHasCriteria;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Yajra\Datatables\Datatables;
use Illuminate\Support\Facades\Session;
use App\Library\Services\DeleteModel;

class FlightController extends Controller
{

    use FileTrait;

    /**
     * @var FlightRepository
     */
    protected $flights;



    public function __construct(FlightRepository $flightRepository)
    {
        $this->flights = $flightRepository;
        $this->middleware('permissions.required');
        $this->middleware('preventBackHistory');
        $this->middleware('auth');
    }

    /**
     * return action buttons
     * @param $id flight instance id
     * @return string
     */
    public function getButton($id, $flight)
    {
        $url = array('show'       => route('flights.show', ['id' => $id]),
                     'edit'       => route('flights.edit', ['id' => $id]),
                     'delete_msg' => "/flights/{$id}/delete_msg");
        return DatatablesHelperController::getActionButton($url, false, $flight);
    }

    /**
     * return data to ajax call for Datatables
     * @param  Request $request
     * @return \Illuminate\Http\Response
     */
    public function data(Request $request)
    {
        return Datatables::of(
            Flight::distinct()->leftJoin('countries as countries_from', 'countries_from.alias', '=', 'flights.country_from')
                ->leftJoin('cities as cities_from', 'cities_from.id', '=', 'flights.city_from')
                ->leftJoin('countries as countries_to', 'countries_to.alias', '=', 'flights.country_to')
                ->leftJoin('cities as cities_to', 'cities_to.id', '=', 'flights.city_to')
                ->select(
                    [
                        'flights.id',
                        'flights.name',
                        'flights.date_from',
                        'flights.date_to',
                        'cities_from.name as city_from',
                        'countries_from.name as country_from',
                        'cities_to.name as city_to',
                        'countries_to.name as country_to',
                    ]
                )
        )
            ->when(!is_null($request->date_from), function ($query) use ($request) {
                return $query->where('date_from', '>=', $request->date_from);
            })
            ->when(!is_null($request->date_to), function ($query) use ($request) {
                return $query->where('date_to', '<=', $request->date_to);
            })
            ->addColumn('date_from', function ($flight){
              return $flight->date_from = (new Carbon($flight->date_from))->format('Y-m-d H:i');
            })
            ->addColumn('date_from', function ($flight){
                return $flight->date_to  = (new Carbon($flight->date_to))->format('Y-m-d H:i');
            })
            ->addColumn('action', function ($flight) {
                return $this->getButton($flight->id, $flight);
            })->addColumn('select', function($flight){
                $data = "Country From: {$flight->country_from}({$flight->city_from}) To: {$flight->country_to}({$flight->city_to})";
                return DatatablesHelperController::getSelectButton($flight->id, $data);
            })->rawColumns(['select', 'action'])->make(true);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $query = Flight::distinct()
            ->leftJoin('countries as countries_from', 'countries_from.alias', '=', 'flights.country_from')
            ->leftJoin('cities as cities_from', 'cities_from.id', '=', 'flights.city_from')
            ->leftJoin('countries as countries_to', 'countries_to.alias', '=', 'flights.country_to')
            ->leftJoin('cities as cities_to', 'cities_to.id', '=', 'flights.city_to')
            ->select([
                'flights.id',
                'flights.name',
                'flights.date_from',
                'flights.date_to',
                'cities_from.name as city_from',
                'countries_from.name as country_from',
                'cities_to.name as city_to',
                'countries_to.name as country_to',
            ]);

        // Apply date filters if provided
        if (!is_null($request->date_from)) {
            $query->where('flights.date_from', '>=', $request->date_from);
        }
        if (!is_null($request->date_to)) {
            $query->where('flights.date_to', '<=', $request->date_to);
        }

        $flights = $query->get();

        // Format dates and add action buttons
        foreach ($flights as $flight) {
            $flight->date_from = (new Carbon($flight->date_from))->format('Y-m-d H:i');
            $flight->date_to = (new Carbon($flight->date_to))->format('Y-m-d H:i');
            $flight->action_buttons = $this->getButton($flight->id, $flight);
        }

        return view('flight.index', compact('flights'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $criterias = Criteria::leftJoin('criteria_types', 'criterias.criteria_type', '=', 'criteria_types.id')
            ->orderBy('criterias.name','asc')
            ->select('criterias.*', 'criteria_types.name as criteria_name')
            ->where('criteria_types.service_type', '=', 'flight')
            ->get();
        $rates = Rate::query()->orderBy('sort_order', 'asc')->get();
        return view('flight.create', compact('criterias', 'rates'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request['date_from'] = $request->get('from_date') . ' ' . $request->get('from_time');
        $request['date_to'] = $request->get('to_date') . ' ' . $request->get('to_time');

        $this->validate($request, [
            'name' => 'required|string',
            'date_from' => 'before_or_equal:date_to',
        ]);

        $request = CitiesHelper::setCityFrom($request);
        $request = CitiesHelper::setCityTo($request);


        $flight = $this->flights->create($request->except('attach', 'from_date', 'from_time' , 'to_date', 'to_time', 'criterias', 'place_id'));

        if(!empty($request->criterias)){
            $criterias = explode(',', $request->criterias);
            foreach ($criterias as $criteria){
                $services_has_criteria = new ServicesHasCriteria();
                $services_has_criteria->service_id = $flight->id;
                $services_has_criteria->criteria_id = $criteria;
                $services_has_criteria->service_type = ServicesHasCriteria::$serviceTypes['flight'];
                $services_has_criteria->save();
            }
        }


        if($request->get('place_id') != '' || $request->get('place_id') != null){
            $googlePlaces = new GooglePlaces();
            $googlePlaces->place_id = $request->get('place_id');
            $googlePlaces->type = GooglePlaces::$services['flight'];
            $googlePlaces->service_id = $flight->id;
            $googlePlaces->save();
        }

        LaravelFlashSessionHelper::setFlashMessage("Flight $flight->name created", 'success');

        $this->addFile($request, $flight);
        $data = ['route' => route('flights.index')];
        return response()->json($data);
    }

    /**
     * Display the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {

        $flight = Flight::leftJoin('rates', 'rates.id', '=', 'flights.rate')
            ->select('flights.*', 'rates.name as rate_name')
            ->where('flights.id', $id)
            ->first();
        $flight->getCriterias();
        if($flight == null){
            return abort(404);
        }
        $criterias = Criteria::leftJoin('criteria_types', 'criterias.criteria_type', '=', 'criteria_types.id')
            ->orderBy('criterias.name','asc')
            ->select('criterias.*', 'criteria_types.name as criteria_name')
            ->where('criteria_types.service_type', '=', 'flight')
            ->get();

        $place = GooglePlacesHelper::getPlace($flight->id, GooglePlaces::$services['flight']);
        $files  = $this->parseAttach($flight);
        $flight->date_from = (new Carbon($flight->date_from))->format('Y-m-d H:i');
        $flight->date_to  = (new Carbon($flight->date_to))->format('Y-m-d H:i');

        return view('flight.show', [ 'flight' => $flight, 'files' => $files,
            'criterias' => $criterias, 'place' => $place]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $flight = $this->flights->getById($id);
        $flight->getCriterias();
        $criterias = Criteria::leftJoin('criteria_types', 'criterias.criteria_type', '=', 'criteria_types.id')
            ->orderBy('criterias.name','asc')
            ->select('criterias.*', 'criteria_types.name as criteria_name')
            ->where('criteria_types.service_type', '=', 'flight')
            ->get();
        $rates = Rate::query()->orderBy('sort_order', 'asc')->get();
        $flight['from_date'] = (new Carbon($flight->date_from))->toDateString();
        $flight['from_time'] = (new Carbon($flight->date_from))->toTimeString();
        $flight['to_date'] = (new Carbon($flight->date_to))->toDateString();
        $flight['to_time'] = (new Carbon($flight->date_to))->toTimeString();

        $place = GooglePlacesHelper::getPlace($flight->id, GooglePlaces::$services['flight']);

        $files = $this->parseAttach($flight);
        return view('flight.edit', ['flight' => $flight, 'files' => $files,
            'criterias' => $criterias,
            'rates' => $rates, 'place' => $place]);
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
        $request['date_from'] = $request->get('from_date') . ' ' . $request->get('from_time');
        $request['date_to'] = $request->get('to_date') . ' ' . $request->get('to_time');

        $this->validate($request, [
            'name' => 'required|string',
            'date_from' => 'before_or_equal:date_to'
        ]);
        $request = CitiesHelper::setCityFrom($request);
        $request = CitiesHelper::setCityTo($request);
        $this->flights->updateById($id, $request->except('attach', 'from_date', 'place_id', 'from_time' , 'to_date', 'to_time', 'criterias'));
        ServicesHasCriteria::where('service_id', $id)
            ->where('service_type', ServicesHasCriteria::$serviceTypes['flight'])
            ->delete();
        $flight = $this->flights->getById($id);
        if(!empty($request->criterias)){
            $criterias = explode(',', $request->criterias);
            foreach ($criterias as $criteria){
                $services_has_criteria = new ServicesHasCriteria();
                $services_has_criteria->service_id = $flight->id;
                $services_has_criteria->criteria_id = $criteria;
                $services_has_criteria->service_type = ServicesHasCriteria::$serviceTypes['flight'];
                $services_has_criteria->save();
            }
        }

        GooglePlaces::where('service_id', $flight->id)->where('type', GooglePlaces::$services['flight'])->delete();

        if($request->get('place_id') != '' || $request->get('place_id') != null){
            $googlePlaces = new GooglePlaces();
            $googlePlaces->place_id = $request->get('place_id');
            $googlePlaces->type = GooglePlaces::$services['flight'];
            $googlePlaces->service_id = $flight->id;
            $googlePlaces->save();
        }

        LaravelFlashSessionHelper::setFlashMessage("Flight $flight->name edited", 'success');

        $this->addFile($request, $flight);    
        $data = ['route' => route('flights.index')];
        return response()->json($data);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id, DeleteModel $deleteModel)
    {
        $flight = $this->flights->getById($id);
        $message = $deleteModel->check($flight, 'Flight');
        if ($message){
            Session::flash('message', $message);
        } else {
            ServicesHasCriteria::where('service_id', $id)
                ->where('service_type', ServicesHasCriteria::$serviceTypes['flight'])
                ->delete();
            GooglePlaces::where('service_id', $id)->where('type', GooglePlaces::$services['flight'])->delete();
            $this->removeFile($this->flights->getById($id));
            $this->flights->deleteById($id);
            Comment::query()->where('reference_type', Comment::$services['flight'])->where('reference_id', $id)->delete();
            LaravelFlashSessionHelper::setFlashMessage("Flight $flight->name deleted", 'success');
        }


        return \URL::to('flights');
    }

    /**
     * display ajax message before delete flight instance
     * @param $id
     * @param Request $request
     * @return mixed
     */
    public function deleteMsg($id, Request $request)
    {
//        $msg = Ajaxis::BtDeleting('Warning!!', 'Would you like to remove This?', '/flights/' . $id . '/delete');
        $msg = Ajaxis::BtDeleting(trans('main.Warning').'!!',trans('main.WouldyouliketoremoveThis').'?', '/flights/' . $id . '/delete');

        if ($request->ajax()) {
            return $msg;
        }
    }
}
