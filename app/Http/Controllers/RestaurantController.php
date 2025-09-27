<?php

namespace App\Http\Controllers;

use App\Criteria;
use App\GooglePlaces;
use App\Helper\FileTrait;
use App\Helper\GooglePlacesHelper;
use App\Helper\LaravelFlashSessionHelper;
use App\Helper\PermissionHelper;
use App\Rate;
use App\Repository\Contracts\RestaurantRepository;
use App\City;
use App\Country;
use App\Helper\CitiesHelper;
use App\Comment;
use App\ServicesHasCriteria;
use Illuminate\Support\Facades\App;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreRestaurantRequest;
use App\Http\Requests\UpdateRestaurantRequest;
use App\Restaurant;
use Amranidev\Ajaxis\Ajaxis;
use URL;
use Illuminate\Support\Facades\Session;
use App\Library\Services\DeleteModel;

class   RestaurantController extends Controller
{
    use FileTrait;

    protected $restaurants;

    public function __construct(RestaurantRepository $repository)
    {
        $this->middleware('permissions.required');
        $this->restaurants = $repository;
        $this->middleware('preventBackHistory');
        $this->middleware('auth');
    }

    /**
     * get action buttons
     * @param  int $id
     * @return string
     */
    public function getButton($id)
    {
        $url = array('show'       => route('restaurant.show', ['id' => $id]),
                     'edit'       => route('restaurant.edit', ['id' => $id]),
                     'delete_msg' => "/restaurant/{$id}/deleteMsg");
        return DatatablesHelperController::getActionButton($url);
    }

    // Removed data() method - using direct data in index() method instead of DataTables
    // public function data(Request $request)
    // {
    //     return Datatables::of(...)
    // }

    /**
     * Display a listing of the resource.
     *
     * @return  \Illuminate\Http\Response
     */
    public function index()
    {
        $title = 'Index - restaurant';
        $restaurants = Restaurant::leftJoin('countries', 'countries.alias', '=', 'restaurants.country')
            ->leftJoin('cities', 'cities.id', '=', 'restaurants.city')
            ->select(
                'restaurants.*',
                'cities.name as city_name',
                'countries.name as country_name'
            )
            ->paginate(15);
        return view('restaurant.index', compact('restaurants', 'title'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return  \Illuminate\Http\Response
     */
    public function create()
    {
        $title = 'Create - restaurant';
        $criterias = Criteria::leftJoin('criteria_types', 'criterias.criteria_type', '=', 'criteria_types.id')
            ->select('criterias.*', 'criteria_types.name as criteria_name')
            ->orderBy('criterias.name','asc')
            ->where('criteria_types.service_type', '=', 'restaurant')
            ->get();

        $rates = Rate::query()->orderBy('sort_order', 'asc')->get();

        return view('restaurant.create', compact('criterias', 'rates'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param    \Illuminate\Http\Request $request
     * @return  \Illuminate\Http\Response
     */
    public function store(StoreRestaurantRequest $request)
    {
		    $lastRestaurant = Restaurant::latest('id')->first();
    $newId = $lastRestaurant ? $lastRestaurant->id + 1 : 1;

    $data = $request->except('attach', 'place_id', 'criterias');
    $data['id'] = $newId;
    $data['city'] = CitiesHelper::changeCityNameToID($request['city']);

    $restaurant = $this->restaurants->create($data);
   
 

        if(!empty($request->criterias)){
            $criterias = explode(',', $request->criterias);
            foreach ($criterias as $criteria){
                $services_has_criteria = new ServicesHasCriteria();
                $services_has_criteria->service_id = $restaurant->id;
                $services_has_criteria->criteria_id = $criteria;
                $services_has_criteria->service_type = ServicesHasCriteria::$serviceTypes['restaurant'];
                $services_has_criteria->save();
            }
        }
        LaravelFlashSessionHelper::setFlashMessage("Restaurant $restaurant->name created", 'success');
        $this->addFile($request, $restaurant);    
        $data = ['route' => route('restaurant.index')];
		return redirect()->route('restaurant.index');
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
        $title = 'Show - restaurant';

        if ($request->ajax()) {
            return URL::to('restaurant/' . $id);
        }

        $restaurant = Restaurant::leftJoin('rates', 'rates.id', '=', 'restaurants.rate')
            ->select('restaurants.*', 'rates.name as rate_name')
            ->where('restaurants.id', $id)
            ->first();
        $restaurant->getCriterias();

        if($restaurant == null){
            return abort(404);
        }

        $criterias = Criteria::leftJoin('criteria_types', 'criterias.criteria_type', '=', 'criteria_types.id')
            ->orderBy('criterias.name','asc')
            ->select('criterias.*', 'criteria_types.name as criteria_name')
            ->where('criteria_types.service_type', '=', 'restaurant')
            ->get();

        $files = $this->parseAttach($restaurant);

        return view(
            'restaurant.show',
            [ 'title' => $title, 'restaurant' => $restaurant, 'files' => $files,
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
        $title = 'Edit - restaurant';
        if ($request->ajax()) {
            return URL::to('restaurant/' . $id . '/edit');
        }


        $restaurant = $this->restaurants->getById($id);

        $restaurant->getCriterias();
        $criterias = Criteria::leftJoin('criteria_types', 'criterias.criteria_type', '=', 'criteria_types.id')
            ->orderBy('criterias.name','asc')
            ->select('criterias.*', 'criteria_types.name as criteria_name')
            ->where('criteria_types.service_type', '=', 'restaurant')
            ->get();
        $rates = Rate::query()->orderBy('sort_order', 'asc')->get();
        $files = $this->parseAttach($restaurant);
	
        return view('restaurant.edit', ['title' => $title, 'restaurant' => $restaurant, 'files' => $files,
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
    public function update($id, UpdateRestaurantRequest $request)
    {
        $request = CitiesHelper::setCityGeneral($request);
        $this->restaurants->updateById($id, $request->except('attach', 'place_id', 'criterias'));
        ServicesHasCriteria::where('service_id', $id)
            ->where('service_type', ServicesHasCriteria::$serviceTypes['restaurant'])
            ->delete();
        $restaurant = $this->restaurants->getById($id);
        if(!empty($request->criterias)){
            $criterias = explode(',', $request->criterias);
            foreach ($criterias as $criteria){
                $services_has_criteria = new ServicesHasCriteria();
                $services_has_criteria->service_id = $restaurant->id;
                $services_has_criteria->criteria_id = $criteria;
                $services_has_criteria->service_type = ServicesHasCriteria::$serviceTypes['restaurant'];
                $services_has_criteria->save();
            }
        }

        LaravelFlashSessionHelper::setFlashMessage("Restaurant $restaurant->name edited", 'success');

        $this->addFile($request, $restaurant);
        $data = ['route' => route('restaurant.index')];
		return redirect()->route('restaurant.index');
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
//        $msg = Ajaxis::BtDeleting('Warning!!', 'Would you like to remove This?', '/restaurant/' . $id . '/delete');
        $msg = Ajaxis::BtDeleting(trans('main.Warning').'!!',trans('main.WouldyouliketoremoveThis').'?', '/restaurant/' . $id . '/delete');

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
//    public function destroy($id)
//    {
//        
//        $restaurant = $this->restaurants->getById($id);
//        $this->removeFile($restaurant);
//        ServicesHasCriteria::where('service_id', $id)
//            ->where('service_type', ServicesHasCriteria::$serviceTypes['restaurant'])
//            ->delete();
//        GooglePlaces::where('service_id', $id)->where('type', GooglePlaces::$services['restaurant'])->delete();
//        $this->restaurants->deleteById($id);
//        Comment::query()->where('reference_type', Comment::$services['restaurant'])->where('reference_id', $id)->delete();
//        return URL::to('restaurant');
//    }
    
    public function destroy($id, DeleteModel $deleteModel)
    {
        $restaurant = $this->restaurants->getById($id);

        $message = $deleteModel->check($restaurant, 'Restaurant');
        if ($message){
            Session::flash('message', $message);
        } else {        
            $this->removeFile($restaurant);
            ServicesHasCriteria::where('service_id', $id)
                ->where('service_type', ServicesHasCriteria::$serviceTypes['restaurant'])
                ->delete();
            $this->restaurants->deleteById($id);
            Comment::query()->where('reference_type', Comment::$services['restaurant'])->where('reference_id', $id)->delete();
            LaravelFlashSessionHelper::setFlashMessage("Restaurant $restaurant->name deleted", 'success');
        }


        return URL::to('restaurant');
    }
}
