<?php

namespace App\Http\Controllers;

use App\TourPackage;
use App\User;
use Illuminate\Http\Request;
use Yajra\Datatables\Datatables;
use App\Helper\HelperTrait;
use App\Criteria;
use App\CriteriaType;
use App\ServicesHasCriteria;
use App\Rate;
use Illuminate\Support\Facades\DB;
class SupplierSearchController extends Controller
{
    use HelperTrait;
    public $tour_package;


    public function __construct(TourPackageController $tour_package)
    {
        $this->middleware('permissions.required');
        $this->tour_package = $tour_package;
    }

    // protected $services = ['Cruises', 'Event', 'Flight', 'Guide', 'Hotel', 'Restaurant', 'Transfer'];
    /**
     * show index page
     * @param  Request $request [description]
     * @return [type]           [description]
     */
    public function index(Request $request)
    {
        $criterias = Criteria::all(); 
    	return view('supplier_search.index', ['options' => $this->services, 'criterias' => $criterias]);
    }
    /**
     * get data to display
     * @param  Request $request 
     * @return mixed
     */
    public function show(Request $request)
    {
        $cityCode       = $request->city_code;
        $countryAlias   = $request->countryalias;
        $searchName     = $request->searchname;
        $criterias      = $request->criterias;
        $rates          = $request->rates;

        if($request->search['value']) $searchName = $request->search['value'];

        if ($request->service === 'Service' || $request->service === 'All') {
            $namespace = $this->getCollection($criterias, $rates, $cityCode, $searchName, $countryAlias);
        }
        else {
            $serv_find = [];
            $namespace = 'App\\' . $request->service;
            if (!isset($criterias) && !isset($rates)){
                $model_test = $namespace;
                $table_name = $this->getTableName($model_test);
                $query_builder = $request->service == 'Cruises' || $request->service == 'Flight' ?
                    $namespace::leftJoin('countries', 'countries.alias', '=', "{$table_name}.country_from")
                        ->leftJoin('cities', 'cities.id', '=', "{$table_name}.city_from") :

                    $namespace::leftJoin('countries', 'countries.alias', '=', "{$table_name}.country")
                        ->leftJoin('cities', 'cities.id', '=', "{$table_name}.city");
			
                    if($cityCode){
						
                                $model = $query_builder->select(
                                       "{$table_name}.*",
                                       "cities.name as city",
                                       'countries.name as country')
                                       ->where("{$table_name}.name", 'like', '%' . $searchName . '%')
                                       ->where('cities.code', $cityCode)
                                       ->get();
                    } else{
                        $countryAlias ?
                                $model = $query_builder->select(
                                      "{$table_name}.*",
                                      "cities.name as city",
                                      'countries.name as country')
                                      ->where('countries.alias', 'like', '%' . $countryAlias . '%')
                                      ->where("{$table_name}.name", 'like', '%' . $searchName . '%')
                                      ->get() :
                                $model = $query_builder->select( "{$table_name}.*",
    DB::raw("IFNULL(cities.name, {$table_name}.city) as city"),
    'countries.name as country')
                                      ->where("{$table_name}.name", 'like', '%' . $searchName . '%')
                                      ->get();

						
                    }

                array_push($serv_find, $model);
            }

            if (isset($criterias) && !isset($rates)){
                // $namespace = $this->findByCriterias($criterias, $namespace, $request->service);
                array_push($serv_find, $this->findByCriterias(@$criterias, @$namespace, @$request->service, @$cityCode, @$searchName, @$countryAlias));
            }


            if ($rates && !$criterias) array_push($serv_find, $this->findByRate($rates, $namespace, $cityCode, $searchName, $countryAlias));

            if($rates && $criterias) array_push($serv_find, $this->findByCriterias($criterias, $namespace, $request->service, $rates, $cityCode, $searchName, $countryAlias));
            $collection = collect($serv_find)->collapse()->all();
            foreach ($collection as $item) {
                $tmp = $request->service;
                if($tmp === 'Transfer') $tmp = 'Bus Company' ;
                $item['nameService'] = "{$item->name}"." ({$tmp})";
            }
            $namespace = $collection;
        }
        ini_set('memory_limit', '800M');
        set_time_limit(0);
        //@ToDo: change on correct generate response for datatable
        $namespace = collect($namespace);
        $services = $namespace->unique()->all();
        $data = Datatables::of($services);
        $can = 0;
        if (\Auth::user()->can('tour_package.create')){
            $can = 1;
        }
        if ($request->actionColumn === 'add-service-column') {
            $this->serviceColumn($data, $can);
        }
        else {
            $this->actionColumn($data, $can);
        }      
        
        return $data->make(true);
    }

    public function generateTableServiceList(Request $request){
        $service_id = $request->service_id;

        $serviceName = ucfirst($this->tour_package->servicesTypes[$request->service_type_id]);
        $serviceName = $serviceName === 'Cruise' ? 'Cruises' : $serviceName;
        $namespace = 'App\\' . $serviceName;
        $model_test = $namespace::first();
        $table_name = $this->getTableName($model_test);
        $query_builder = $serviceName == 'Cruises' || $serviceName == 'Flight' ?

            $namespace::leftJoin('countries', 'countries.alias', '=', "{$table_name}.country_from")
                ->leftJoin('cities', 'cities.id', '=', "{$table_name}.city_from") :

            $namespace::leftJoin('countries', 'countries.alias', '=', "{$table_name}.country")
                ->leftJoin('cities', 'cities.id', '=', "{$table_name}.city");

        $model = $query_builder->select(
            "{$table_name}.*",
            "cities.name as city",
            'countries.name as country')
            ->where("{$table_name}.id", '!=', $service_id)
            ->get();


        $servicesDvo = collect($model);
        $services = $servicesDvo->unique()->all();
        $data = Datatables::of($services);
        $this->generateActionServiceItem($data);

        return $data->make(true);
    }

    public function generateActionServiceItem($data){
        return $data->addColumn('action-change-service', function($model){
            $type = strtolower(class_basename(get_class($model)));
            $id = $model->id;
            $name = $model->name;
            $pre_loader = $type == 'transfer' ? '' : 'pre-loader-func';
            return "<button class='btn btn-success change-service-button $pre_loader' data-service_type='{$type}' data-service_id='{$id}' data-service_name=\"{$name}\">Change</button>";
        })->rawColumns(['action-change-service']);
    }

    /**
     * get services by criterias and, optionaly, by rate
     * @param  array $criterias   
     * @param  $namespace   
     * @param  $serviceType 
     * @param  $rate        
     * @return array              
     */
    public function findByCriterias($criterias, $namespace, $serviceType, $rate = null, $cityCode = null, $searchName = null, $countryAlias = null)
    {
        $data = [];

        foreach ($criterias as $criteria) {
            if (strtolower($serviceType) == 'cruises') $serviceType = 'cruise';
            $service_type = ServicesHasCriteria::$serviceTypes[strtolower($serviceType)];
            $service_criteria = ServicesHasCriteria::query()->where('criteria_id', $criteria)
                ->where('service_type', $service_type)->get();
            foreach ($service_criteria as $s_cr) {
                $model_test = $namespace::first();
                $table_name = $this->getTableName($model_test);
                $query_builder = $serviceType == 'Cruises' || $serviceType == 'Flight' ?

                    $namespace::leftJoin('countries', 'countries.alias', '=', "{$table_name}.country_from")
                        ->leftJoin('cities', 'cities.id', '=', "{$table_name}.city_from") :

                    $namespace::leftJoin('countries', 'countries.alias', '=', "{$table_name}.country")
                        ->leftJoin('cities', 'cities.id', '=', "{$table_name}.city");
                    if($cityCode){
                        $model = $query_builder->select(
                            "{$table_name}.*",
                            "cities.name as city",
                            'countries.name as country')
                            ->where("{$table_name}.id", $s_cr->service_id)
                            ->where("{$table_name}.name", 'like', '%' . $searchName . '%')                                     
                            ->where('cities.code', $cityCode)                           
                            ->first();
                    } else{

                        $countryAlias ?
                        $model = $query_builder->select(
                            "{$table_name}.*",
                            "cities.name as city",
                            'countries.name as country')
                            ->where("{$table_name}.id", $s_cr->service_id)
                            ->where("{$table_name}.name", 'like', '%' . $searchName . '%')
                            ->where('countries.alias', 'like', '%' . $countryAlias . '%')                                     
                            ->first() :
                        $model = $query_builder->select(
                            "{$table_name}.*",
                            "cities.name as city",
                            'countries.name as country')
                            ->where("{$table_name}.id", $s_cr->service_id)
                            ->where("{$table_name}.name", 'like', '%' . $searchName . '%')
                            ->first();
                    }

                if($model){
                    $model['nameService'] = "{$model->name} ({$serviceType})";
                    if($rate){
                        if ($model->rate == $rate) array_push($data, $model);
                    } else array_push($data, $model);
                }
            }
        }
        return $data;
    }
    /**
     * find services by rate
     * @param  $rate    
     * @param  $namespace
     * @return mixed            
     */
    public function findByRate($rate, $namespace, $cityCode = null, $searchName = null, $countryAlias = null)
    {
        $model_test = $namespace::first();
        $table_name = $this->getTableName($model_test);
        
        if($cityCode){        
            $model = $namespace::leftJoin('countries', 'countries.alias', '=', "{$table_name}.country")
                ->leftJoin('cities', 'cities.id', '=', "{$table_name}.city")
                ->select("{$table_name}.*", "cities.name as city", 'countries.name as country')
                ->where("{$table_name}.rate", $rate)
                ->where('cities.code', $cityCode)                          
                ->where("{$table_name}.name", 'like', '%' . $searchName . '%')                     
                ->get();
        } else{
            $countryAlias ?
                $model = $namespace::leftJoin('countries', 'countries.alias', '=', "{$table_name}.country")
                    ->leftJoin('cities', 'cities.id', '=', "{$table_name}.city")
                    ->select("{$table_name}.*", "cities.name as city", 'countries.name as country')
                    ->where("{$table_name}.rate", $rate)
                    ->where('countries.alias', 'like', '%' . $countryAlias . '%')  
                    ->where("{$table_name}.name", 'like', '%' . $searchName . '%')                     
                    ->get() :
                $model = $namespace::leftJoin('countries', 'countries.alias', '=', "{$table_name}.country")
                    ->leftJoin('cities', 'cities.id', '=', "{$table_name}.city")
                    ->select("{$table_name}.*", "cities.name as city", 'countries.name as country')
                    ->where("{$table_name}.rate", $rate)
                    ->where("{$table_name}.name", 'like', '%' . $searchName . '%')                     
                    ->get();
        }        
        return $model;
    }
    /**
     * show criterias view component
     * @param  Request $request 
     * @return view
     */
    public function getCriterias(Request $request)
    {
        if ($request->serviceType == 'All') {
            $criterias = Criteria::all();
        } else {
            if (strtolower($request->serviceType) == 'cruises') $request->serviceType = 'cruise';
            $criteria_type = CriteriaType::where('name', strtolower($request->serviceType))->first();
            $criterias = Criteria::where('criteria_type', $criteria_type->id)->get();
        }
        $rates = (strtolower($request->serviceType) == 'hotel') ? Rate::all() : null;
        return view('component.criterias', ['criterias' => $criterias, 'rates' => $rates]);
    }
    /**
     * get services collection
     * @param  array $criterias 
     * @param  $rates     
     * @return mixed          
     */
    public function getCollection($criterias, $rates = null, $cityCode=null, $searchName = null, $countryAlias = null)
    {
        $data = [];
        foreach ($this->services as $service) {
        	if ($service == 'Transfer') {
        		continue;
	        }
            $services = [];
            $namespace = 'App\\' . $service;
            $model_test = $namespace;
            $table_name = $this->getTableName($model_test);
            $query_builder = $service == 'Cruises' || $service == 'Flight' ?

                $namespace::leftJoin('countries', 'countries.alias', '=', "{$table_name}.country_from")
                    ->leftJoin('cities', 'cities.id', '=', "{$table_name}.city_from") :

                $namespace::leftJoin('countries', 'countries.alias', '=', "{$table_name}.country")
                    ->leftJoin('cities', 'cities.id', '=', "{$table_name}.city");

                if($cityCode){
                    $model = $query_builder->select(
                            "{$table_name}.*",
                            "cities.name as city",
                            'countries.name as country')
                        ->where('cities.code', $cityCode)
                        ->where("{$table_name}.name", 'like', '%' . $searchName . '%')
                        ->get();
                } else{
                    if($searchName){
                        $countryAlias ? 
                            $model = $query_builder->select(
                                    "{$table_name}.*",
                                    "cities.name as city",
                                    'countries.name as country')
                                ->where('countries.alias', 'like', '%' . $countryAlias . '%')
                                ->where("{$table_name}.name", 'like', '%' . $searchName . '%')

                                ->get() :
                            $model = $query_builder->select(
                                    "{$table_name}.*",
                                    "cities.name as city",
                                    'countries.name as country')
                                ->where("{$table_name}.name", 'like', '%' . $searchName . '%')
                                ->get();
                    } else{
                        $countryAlias ? 
                            $model = $query_builder->select(
                                    "{$table_name}.*",
                                    "cities.name as city",
                                    'countries.name as country')
                                ->where('countries.alias', 'like', '%' . $countryAlias . '%')
                                ->get() :
                            $model = $query_builder->select(
                                    "{$table_name}.*",
                                    "cities.name as city",
                                    'countries.name as country')
                                ->get();
                    }          
                }

            if (!$criterias){
                foreach ($model as $item) {
                    $item['nameService'] = "{$item->name} ({$service})";
                }
                array_push($services, $model);
            }

            if ($criterias){
                array_push($services, $this->findByCriterias($criterias, $namespace, $service, $cityCode = null, $searchName = null, $countryAlias = null));
            }

            if ($rates) array_push($services, $this->findByRate($rates, $namespace, $cityCode, $searchName, $countryAlias));
            $c_service = collect($services)->collapse()->all();
            array_push($data, $c_service);
        }

        $collection = collect($data)->collapse()->all();
        return $collection;
    }

    public function actionColumn($data, $can)
    {
        $data->addColumn('action', function($model) use ($can){
            return $this->dataId($model, $can);
        })->rawColumns(['action']);
    }

    public function serviceColumn($data)
    {
        return $data->addColumn('add-service-column', function($model){
            $type = strtolower(class_basename(get_class($model)));
            $id = $model->id;
            $name = $model->name;
            $pre_loader = $type == 'hotel' || $type == 'transfer' ? '' : 'pre-loader-func';
            return "<button class='btn btn-success add-service-button $pre_loader' data-link=".route('tour_package.store')." data-service_type='{$type}' data-service_id='{$id}' data-service_name=\"{$name}\">Add</button>";
        })->rawColumns(['add-service-column']);
    }

    public function dataId($model, $can)
    {
        $type = class_basename(get_class($model));
        $id = $model->id;
        $low = strtolower($type);
        if (strtolower($type) == 'flight') $low .= 's';
        $link = route("{$low}.show", ['id' => $model->id]);
        $btn = "";
        if ($can){
            $btn .= "<button class='btn btn-success tourAdd' hidden data-id=$id data-type=$type data-service_name=\"{$model->name}\" id='service-property'>Tour</button>" .
            "<a class='show-button' data-link={$link}></a>";
        }

        return $btn;
    }
}
