<?php

namespace App\Http\Controllers;

use App\Criteria;
use App\Events\SomeEvent;
use App\GooglePlaces;
use App\Helper\FileTrait;
use App\Helper\GooglePlacesHelper;
use App\Helper\LaravelFlashSessionHelper;
use App\Helper\PermissionHelper;
use App\Rate;
use App\Repository\Contracts\EventRepository;
use App\City;
use App\Country;
use App\Helper\CitiesHelper;
use App\ServicesHasCriteria;
use GoogleMaps\GoogleMaps;
use App\Comment;
use Illuminate\Support\Facades\App;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreEventRequest;
use App\Http\Requests\UpdateEventRequest;
use App\Event;
use Amranidev\Ajaxis\Ajaxis;
use URL;
use Yajra\Datatables\Datatables;
use Illuminate\Support\Facades\Session;
use App\Library\Services\DeleteModel;

/**
 * Class EventController.
 *
 * @author  The scaffold-interface created at 2017-04-12 12:45:46pm
 * @link    https://github.com/amranidev/scaffold-interface
 */
class EventController extends Controller
{
    use FileTrait;

    protected $events;

    public function __construct(EventRepository $eventRepository)
    {
        $this->middleware('permissions.required');
        $this->events = $eventRepository;
        $this->middleware('preventBackHistory');
        $this->middleware('auth');
    }

    /**
     * Display a listing of the resource.
     *
     * @return  \Illuminate\Http\Response
     */
    public function index()
    {
        $title = 'Index - event';
        $events = Event::leftJoin('countries', 'countries.alias', '=', 'events.country')
            ->leftJoin('cities', 'cities.id', '=', 'events.city')
            ->select(
                'events.*',
                'cities.name as city_name',
                'countries.name as country_name'
            )
            ->paginate(15);

        return view('event.index', compact('events', 'title'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return  \Illuminate\Http\Response
     */
    public function create()
    {
        $title = 'Create - event';

        $criterias = Criteria::leftJoin('criteria_types', 'criterias.criteria_type', '=', 'criteria_types.id')
            ->orderBy('criterias.name','asc')
            ->select('criterias.*', 'criteria_types.name as criteria_name')
            ->where('criteria_types.service_type', '=', 'event')
            ->get();

        $rates = Rate::query()->orderBy('sort_order','asc')->get();

        return view('event.create', compact('criterias', 'rates'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param    \Illuminate\Http\Request $request
     * @return  \Illuminate\Http\Response
     */
    public function store(StoreEventRequest $request)
    {
        $request = CitiesHelper::setCityGeneral($request);
        $event = $this->events->create($request->except(['attach', 'place_id', 'criterias']));


        if(!empty($request->criterias)){
            $criterias = explode(',', $request->criterias);
            foreach ($criterias as $criteria){
                $services_has_criteria = new ServicesHasCriteria();
                $services_has_criteria->service_id = $event->id;
                $services_has_criteria->criteria_id = $criteria;
                $services_has_criteria->service_type = ServicesHasCriteria::$serviceTypes['event'];
                $services_has_criteria->save();
            }
        }

        LaravelFlashSessionHelper::setFlashMessage("Event $event->name created", 'success');

        $this->addFile($request, $event);    
        $data = ['route' => route('event.index')];
		return redirect()->route('event.index');
        return response()->json($data);
    }

    /**
     * Display the specified resource.
     *
     * @param    \Illuminate\Http\Request $request
     * @param    int $id
     * @return  \Illuminate\Http\Response
     */
    public function show($id, Request $request)
    {

        $title = 'Show - event';

        if ($request->ajax()) {
            return URL::to('event/' . $id);
        }

        $event = Event::leftJoin('rates', 'rates.id', '=', 'events.rate')
            ->select('events.*', 'rates.name as rate_name')
            ->where('events.id', $id)
            ->first();
        $event->getCriterias();
        $criterias = Criteria::leftJoin('criteria_types', 'criterias.criteria_type', '=', 'criteria_types.id')
            ->orderBy('criterias.name','asc')
            ->select('criterias.*', 'criteria_types.name as criteria_name')
            ->where('criteria_types.service_type', '=', 'event')
            ->get();
        if($event == null){
            return abort(404);
        }
        $files = $this->parseAttach($event);

        return view('event.show', ['event' => $event, 'title' => $title, 'files' => $files,
            'criterias' => $criterias]);
    }

    /**
     * Show the form for editing the specified resource.
     * @param    \Illuminate\Http\Request $request
     * @param    int $id
     * @return  \Illuminate\Http\Response
     */
    public function edit($id, Request $request)
    {
		
        $title = 'Edit - event';
        if ($request->ajax()) {
            return URL::to('event/' . $id . '/edit');
        }

        $event = $this->events->getById($id);
        $event->getCriterias();
        $criterias = Criteria::leftJoin('criteria_types', 'criterias.criteria_type', '=', 'criteria_types.id')
            ->orderBy('criterias.name','asc')
            ->select('criterias.*', 'criteria_types.name as criteria_name')
            ->where('criteria_types.service_type', '=', 'event')
            ->get();
        $rates = Rate::query()->orderBy('sort_order', 'asc')->get();
        $files = $this->parseAttach($event);
		
        return view('event.edit', ['title' => $title, 'event' => $event, 'files' => $files,
            'criterias' => $criterias,
            'rates' => $rates]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param    \Illuminate\Http\Request $request
     * @param    int $id
     * @return  \Illuminate\Http\Response
     */
    public function update($id, UpdateEventRequest $request)
    {
        $request = CitiesHelper::setCityGeneral($request);
        $this->events->updateById($id, $request->except(['attach', 'place_id', 'criterias']));


        ServicesHasCriteria::where('service_id', $id)
            ->where('service_type', ServicesHasCriteria::$serviceTypes['event'])
            ->delete();
        $event = $this->events->getById($id);
        if(!empty($request->criterias)){
            $criterias = explode(',', $request->criterias);
            foreach ($criterias as $criteria){
                $services_has_criteria = new ServicesHasCriteria();
                $services_has_criteria->service_id = $event->id;
                $services_has_criteria->criteria_id = $criteria;
                $services_has_criteria->service_type = ServicesHasCriteria::$serviceTypes['event'];
                $services_has_criteria->save();
            }
        }

        LaravelFlashSessionHelper::setFlashMessage("Event $event->name edited", 'success');

        $this->addFile($request, $event);    
        $data = ['route' => route('event.index')];
        return response()->json($data);
    }

    /**
     * Delete confirmation message by Ajaxis.
     *
     * @link      https://github.com/amranidev/ajaxis
     * @param    \Illuminate\Http\Request $request
     * @return  String
     */
    public function DeleteMsg($id, Request $request)
    {
//        $msg = Ajaxis::BtDeleting('Warning!!', 'Would you like to remove This?', '/event/' . $id . '/delete');
        $msg = Ajaxis::BtDeleting(trans('main.Warning').'!!',trans('main.WouldyouliketoremoveThis').'?', '/event/' . $id . '/delete');

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
    public function destroy($id, DeleteModel $deleteModel)
    {
        $event = $this->events->getById($id);
        $message = $deleteModel->check($event, 'Event');
        if ($message){
            Session::flash('message', $message);
        } else {
            ServicesHasCriteria::where('service_id', $id)
                ->where('service_type', ServicesHasCriteria::$serviceTypes['event'])
                ->delete();
            $this->removeFile($event);
            $this->events->deleteById($id);
            Comment::query()->where('reference_type', Comment::$services['event'])->where('reference_id', $id)->delete();
            LaravelFlashSessionHelper::setFlashMessage("Event $event->name deleted", 'success');
        }


        return URL::to('event');
    }
}
