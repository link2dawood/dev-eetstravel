<?php

namespace App\Http\Controllers;

use Amranidev\Ajaxis\Ajaxis;
use App\Comment;
use App\Criteria;
use App\Cruises;
use App\GooglePlaces;
use App\Helper\CitiesHelper;
use App\Helper\FileTrait;
use App\Helper\GooglePlacesHelper;
use App\Helper\LaravelFlashSessionHelper;
use App\Helper\PermissionHelper;
use App\Rate;
use App\Repository\Contracts\CruiseRepository;
use App\ServicesHasCriteria;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Yajra\Datatables\Datatables;
use Illuminate\Support\Facades\Session;
use App\Library\Services\DeleteModel;

class CruisesController extends Controller
{
    use FileTrait;

    protected $cruises;

    public function __construct(CruiseRepository $cruise)
    {
        $this->cruises = $cruise;
        $this->middleware('permissions.required');
        $this->middleware('preventBackHistory');
        $this->middleware('auth');
    }

    public function getButton($id, $cruise)
    {
        $url = array('show' => route('cruises.show', ['id' => $id]),
            'edit' => route('cruises.edit', ['id' => $id]),
            'delete_msg' => "/cruises/{$id}/delete_msg");

        return DatatablesHelperController::getActionButton($url, false, $cruise);
    }

    public function data(Request $request)
    {
        return Datatables::of(
            Cruises::distinct()->leftJoin('countries as countries_from', 'countries_from.alias', '=', 'cruises.country_from')
                ->leftJoin('cities as cities_from', 'cities_from.id', '=', 'cruises.city_from')
                ->leftJoin('countries as countries_to', 'countries_to.alias', '=', 'cruises.country_to')
                ->leftJoin('cities as cities_to', 'cities_to.id', '=', 'cruises.city_to')
                ->select(
                    [
                        'cruises.id',
                        'cruises.name',
                        'cruises.date_from',
                        'cruises.date_to',
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
            ->addColumn('action', function ($cruise) {
            return $this->getButton($cruise->id, $cruise);
        })
            ->addColumn('date_from', function ($cruise){
                return $cruise->date_from = (new Carbon($cruise->date_from))->format('Y-m-d H:i');
            })
            ->addColumn('date_to', function ($cruise){
                return $cruise->date_to  = (new Carbon($cruise->date_to))->format('Y-m-d H:i');
            })
            ->addColumn('select', function ($cruise) {
            return DatatablesHelperController::getSelectButton($cruise->id, $cruise->name);
        })->rawColumns(['select', 'action'])->make(true);
    }

    public function deleteMsg($id, Request $request)
    {
//        $msg = Ajaxis::BtDeleting('Warning!!', 'Would you like to remove This?', '/cruises/'. $id . '/delete');
        $msg = Ajaxis::BtDeleting(trans('main.Warning').'!!',trans('main.WouldyouliketoremoveThis').'?', '/cruises/'. $id . '/delete');

        if ($request->ajax()) {
            return $msg;
        }
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('cruises.index');
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
            ->where('criteria_types.service_type', '=', 'cruise')
            ->get();
        $rates = Rate::query()->orderBy('sort_order', 'asc')->get();
        return view('cruises.create', compact('criterias', 'rates'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
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

        $cruise = $this->cruises->create($request->except('attach', 'from_date', 'from_time' , 'to_date', 'to_time' , 'criterias', 'place_id'));

        if(!empty($request->criterias)){
            $criterias = explode(',', $request->criterias);
            foreach ($criterias as $criteria){
                $services_has_criteria = new ServicesHasCriteria();
                $services_has_criteria->service_id = $cruise->id;
                $services_has_criteria->criteria_id = $criteria;
                $services_has_criteria->service_type = ServicesHasCriteria::$serviceTypes['cruise'];
                $services_has_criteria->save();
            }
        }

        LaravelFlashSessionHelper::setFlashMessage("Cruise $cruise->name created", 'success');

        $this->addFile($request, $cruise);    
        $data = ['route' => route('cruises.index')];
        return response()->json($data);
    }

    /**
     * Display the specified resource.
     *
     * @param  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $cruise = Cruises::leftJoin('rates', 'rates.id', '=', 'cruises.rate')
            ->select('cruises.*', 'rates.name as rate_name')
            ->where('cruises.id', $id)
            ->first();
        $cruise->getCriterias();
        if($cruise == null){
            return abort(404);
        }
        $criterias = Criteria::leftJoin('criteria_types', 'criterias.criteria_type', '=', 'criteria_types.id')
            ->orderBy('criterias.name','asc')
            ->select('criterias.*', 'criteria_types.name as criteria_name')
            ->where('criteria_types.service_type', '=', 'cruise')
            ->get();

        $place = GooglePlacesHelper::getPlace($cruise->id, GooglePlaces::$services['cruise']);

        $files = $this->parseAttach($cruise);

        $cruise->date_from = (new Carbon($cruise->date_from))->format('Y-m-d H:i');
        $cruise->date_to  = (new Carbon($cruise->date_to))->format('Y-m-d H:i');

        return view('cruises.show', ['cruise' => $cruise, 'files' => $files,
            'criterias' => $criterias, 'place' => $place]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $cruise = $this->cruises->showById($id);
        $cruise->getCriterias();
        $criterias = Criteria::leftJoin('criteria_types', 'criterias.criteria_type', '=', 'criteria_types.id')
            ->orderBy('criterias.name','asc')
            ->select('criterias.*', 'criteria_types.name as criteria_name')
            ->where('criteria_types.service_type', '=', 'cruise')
            ->get();
        $rates = Rate::query()->orderBy('sort_order', 'asc')->get();
        $cruise['from_date'] = (new Carbon($cruise->date_from))->toDateString();
        $cruise['from_time'] = (new Carbon($cruise->date_from))->toTimeString();
        $cruise['to_date'] = (new Carbon($cruise->date_to))->toDateString();
        $cruise['to_time'] = (new Carbon($cruise->date_to))->toTimeString();



        $files = $this->parseAttach($cruise);

        return view('cruises.edit', ['cruise' => $cruise, 'files' => $files,
            'criterias' => $criterias,
            'rates' => $rates]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $request['date_from'] = $request->get('from_date') . ' ' . $request->get('from_time');
        $request['date_to'] = $request->get('to_date') . ' ' . $request->get('to_time');

        $this->validate($request, [
            'name' => 'required|string',
            'date_from' => 'before_or_equal:date_to',
        ]);

        $request = CitiesHelper::setCityFrom($request);
        $request = CitiesHelper::setCityTo($request);


        $this->cruises->updateById($id, $request->except('attach', 'from_date', 'place_id', 'from_time' , 'to_date', 'to_time', 'criterias'));
        ServicesHasCriteria::where('service_id', $id)
            ->where('service_type', ServicesHasCriteria::$serviceTypes['cruise'])
            ->delete();
        $cruise = $this->cruises->showById($id);
        if(!empty($request->criterias)){
            $criterias = explode(',', $request->criterias);
            foreach ($criterias as $criteria){
                $services_has_criteria = new ServicesHasCriteria();
                $services_has_criteria->service_id = $cruise->id;
                $services_has_criteria->criteria_id = $criteria;
                $services_has_criteria->service_type = ServicesHasCriteria::$serviceTypes['cruise'];
                $services_has_criteria->save();
            }
        }


        GooglePlaces::where('service_id', $cruise->id)->where('type', GooglePlaces::$services['cruise'])->delete();

        if($request->get('place_id') != '' || $request->get('place_id') != null){
            $googlePlaces = new GooglePlaces();
            $googlePlaces->place_id = $request->get('place_id');
            $googlePlaces->type = GooglePlaces::$services['cruise'];
            $googlePlaces->service_id = $cruise->id;
            $googlePlaces->save();
        }

        LaravelFlashSessionHelper::setFlashMessage("Cruise $cruise->name edited", 'success');

        $this->addFile($request, $cruise);    
        $data = ['route' => route('cruises.index')];
        return response()->json($data);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id, DeleteModel $deleteModel)
    {
        $cruise = $this->cruises->showById($id);
        $message = $deleteModel->check($cruise, 'Cruise');
        if ($message){
            Session::flash('message', $message);
        } else {
            ServicesHasCriteria::where('service_id', $id)
                ->where('service_type', ServicesHasCriteria::$serviceTypes['cruise'])
                ->delete();
            GooglePlaces::where('service_id', $id)->where('type', GooglePlaces::$services['cruise'])->delete();
            $this->removeFile($cruise);
            $this->cruises->destroy($id);
            Comment::query()->where('reference_type', Comment::$services['cruises'])->where('reference_id', $id)->delete();
            LaravelFlashSessionHelper::setFlashMessage("Cruise $cruise->name deleted", 'success');
        }


        return \URL::to('cruises');
    }
}
