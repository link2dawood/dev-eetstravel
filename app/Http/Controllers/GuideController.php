<?php

namespace App\Http\Controllers;

use App\City;
use App\Country;
use App\Criteria;
use App\GooglePlaces;
use App\Helper\CitiesHelper;
use App\Comment;
use App\Helper\FileTrait;
use App\Helper\GooglePlacesHelper;
use App\Helper\LaravelFlashSessionHelper;
use App\Helper\PermissionHelper;
use App\Rate;
use App\Repository\Contracts\GuideRepository;
use App\ServicesHasCriteria;
use Illuminate\Support\Facades\App;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreGuideRequest;
use App\Http\Requests\UpdateGuideRequest;
use App\Guide;
use Amranidev\Ajaxis\Ajaxis;
use URL;
use Illuminate\Support\Facades\Session;
use App\Library\Services\DeleteModel;

/**
 * Class GuideController.
 *
 * @author  The scaffold-interface created at 2017-04-12 12:55:18pm
 * @link  https://github.com/amranidev/scaffold-interface
 */
class GuideController extends Controller
{
    use FileTrait;

    protected $guide;

    public function __construct(GuideRepository $guide)
    {
        $this->middleware('permissions.required');
        $this->guide = $guide;
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
        $title = 'Index - guide';

        $cacheKey = 'guides_index_data_' . md5(request()->getQueryString());
        $guides = \Cache::remember($cacheKey, 300, function () {
            return Guide::leftJoin('countries', 'countries.alias', '=', 'guides.country')
                ->leftJoin('cities', 'cities.id', '=', 'guides.city')
                ->select([
                    'guides.id',
                    'guides.name',
                    'guides.address',
                    'guides.work_phone',
                    'guides.work_contact',
                    'cities.name as city_name',
                    'countries.name as country_name'
                ])
                ->orderBy('guides.name', 'asc')
                ->paginate(15);
        });

        return view('guide.index', compact('guides', 'title'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return  \Illuminate\Http\Response
     */
    public function create()
    {
        $title = 'Create - guide';
        $criterias = Criteria::leftJoin('criteria_types', 'criterias.criteria_type', '=', 'criteria_types.id')
            ->orderBy('criterias.name','asc')
            ->select('criterias.*', 'criteria_types.name as criteria_name')
            ->where('criteria_types.service_type', '=', 'guide')
            ->get();
        $rates = Rate::query()->orderBy('sort_order', 'asc')->get();
        return view('guide.create', compact('criterias', 'rates'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param    \Illuminate\Http\Request $request
     * @return  \Illuminate\Http\Response
     */
    public function store(StoreGuideRequest $request)
    {

        $request = CitiesHelper::setCityGeneral($request);
        $guide = $this->guide->create($request->except('attach', 'place_id', 'criterias'));

        if(!empty($request->criterias)){
            $criterias = explode(',', $request->criterias);
            foreach ($criterias as $criteria){
                $services_has_criteria = new ServicesHasCriteria();
                $services_has_criteria->service_id = $guide->id;
                $services_has_criteria->criteria_id = $criteria;
                $services_has_criteria->service_type = ServicesHasCriteria::$serviceTypes['guide'];
                $services_has_criteria->save();
            }
        }
        LaravelFlashSessionHelper::setFlashMessage("Guide $guide->name created", 'success');

        $this->addFile($request, $guide);
        $data = ['route' => route('guide.index')];
		return redirect()->route('guide.index');
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
        $title = 'Show - guide';

        if ($request->ajax()) {
            return URL::to('guide/' . $id);
        }

        $guide = Guide::leftJoin('rates', 'rates.id', '=', 'guides.rate')
            ->select('guides.*', 'rates.name as rate_name')
            ->where('guides.id', $id)
            ->first();
        $guide->getCriterias();
        if($guide == null){
            return abort(404);
        }
        $criterias = Criteria::leftJoin('criteria_types', 'criterias.criteria_type', '=', 'criteria_types.id')
            ->orderBy('criterias.name','asc')
            ->select('criterias.*', 'criteria_types.name as criteria_name')
            ->where('criteria_types.service_type', '=', 'guide')
            ->get();


        $files = $this->parseAttach($guide);
        return view('guide.show', ['title' => $title, 'guide' => $guide, 'files' => $files,
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
        $title = 'Edit - guide';
        if ($request->ajax()) {
            return URL::to('guide/' . $id . '/edit');
        }


        $guide = $this->guide->getById($id);
        $guide->getCriterias();
        $criterias = Criteria::leftJoin('criteria_types', 'criterias.criteria_type', '=', 'criteria_types.id')
            ->orderBy('criterias.name','asc')
            ->select('criterias.*', 'criteria_types.name as criteria_name')
            ->where('criteria_types.service_type', '=', 'guide')
            ->get();
        $rates = Rate::query()->orderBy('sort_order', 'asc')->get();;
        $files = $this->parseAttach($guide);
        return view('guide.edit', ['title' => $title, 'guide' => $guide, 'files' => $files,
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
    public function update($id, UpdateGuideRequest $request)
    {
        $request = CitiesHelper::setCityGeneral($request);
        $this->guide->updateById($id, $request->except('attach', 'place_id', 'criterias'));
        ServicesHasCriteria::where('service_id', $id)
            ->where('service_type', ServicesHasCriteria::$serviceTypes['guide'])
            ->delete();
        $guide = $this->guide->getById($id);

        if(!empty($request->criterias)){
            $criterias = explode(',', $request->criterias);
            foreach ($criterias as $criteria){
                $services_has_criteria = new ServicesHasCriteria();
                $services_has_criteria->service_id = $guide->id;
                $services_has_criteria->criteria_id = $criteria;
                $services_has_criteria->service_type = ServicesHasCriteria::$serviceTypes['guide'];
                $services_has_criteria->save();
            }
        }

        LaravelFlashSessionHelper::setFlashMessage("Guide $guide->name edited", 'success');

        $this->addFile($request, $guide);
        $data = ['route' => route('guide.index')];
		return redirect()->route('guide.index');
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
//        $msg = Ajaxis::BtDeleting('Warning!!', 'Would you like to remove This?', '/guide/' . $id . '/delete');
        $msg = Ajaxis::BtDeleting(trans('main.Warning').'!!',trans('main.WouldyouliketoremoveThis').'?', '/guide/' . $id . '/delete');

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
        $guide = $this->guide->getById($id);
        $message = $deleteModel->check($guide, 'Guide');
        if ($message){
            Session::flash('message', $message);
        } else {
            LaravelFlashSessionHelper::setFlashMessage("Guide $guide->name deleted", 'success');
            ServicesHasCriteria::where('service_id', $id)
                ->where('service_type', ServicesHasCriteria::$serviceTypes['guide'])
                ->delete();
            $this->removeFile($guide);
            $this->guide->deleteById($id);
            Comment::query()->where('reference_type', Comment::$services['guide'])->where('reference_id', $id)->delete();
        }

        return URL::to('guide');
    }
}
