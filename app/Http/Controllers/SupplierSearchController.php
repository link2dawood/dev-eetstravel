<?php

namespace App\Http\Controllers;

use App\TourPackage;
use App\User;
use Illuminate\Http\Request;
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
    /** Services the search page offers; "All"/"Service" search every one except bus companies */
    const SEARCH_SERVICES = ['Hotel', 'Event', 'Guide', 'Restaurant', 'Transfer', 'Flight', 'Cruises'];

    /** The only columns the result tables show; nothing else (emails, passwords...) leaves the server */
    const RESULT_COLUMNS = ['id', 'name', 'nameService', 'address_first', 'country', 'city', 'work_phone', 'contact_name'];

    /** "%" and "_" are LIKE wildcards; search for them literally */
    private static function likeValue($value)
    {
        return '%' . addcslashes((string) $value, '%_\\') . '%';
    }

    /**
     * One supplier type as a query with the shared result columns, filtered in SQL.
     */
    private function supplierQuery($service, array $filters)
    {
        $class = 'App\\' . $service;
        $model = new $class;
        $table = $model->getTable();
        $from = in_array($service, ['Flight', 'Cruises'], true) ? '_from' : '';
        $label = $service === 'Transfer' ? 'Bus Company' : $service;

        $query = DB::table($table)
            ->leftJoin('countries', 'countries.alias', '=', "{$table}.country{$from}")
            ->leftJoin('cities', 'cities.id', '=', "{$table}.city{$from}")
            ->select([
                // Explicit aliases: ORDER BY uses them, and cities/countries also have id/name columns
                DB::raw(DB::getPdo()->quote($service) . ' as service_type'),
                "{$table}.id as id",
                "{$table}.name as name",
                DB::raw("CONCAT(COALESCE({$table}.name, ''), ' (', " . DB::getPdo()->quote($label) . ", ')') as nameService"),
                "{$table}.address_first as address_first",
                'countries.name as country',
                'cities.name as city',
                "{$table}.work_phone as work_phone",
                "{$table}.contact_name as contact_name",
            ]);

        if (method_exists($model, 'getDeletedAtColumn')) {
            $query->whereNull("{$table}.deleted_at");
        }
        if ($filters['name'] !== '') {
            $query->where("{$table}.name", 'like', self::likeValue($filters['name']));
        }
        if ($filters['cityCode']) {
            $query->where('cities.code', $filters['cityCode']);
        } elseif ($filters['cityName'] !== '') {
            // Many suppliers' city ids do not point at their real city, so the address counts too
            $like = self::likeValue($filters['cityName']);
            $query->where(function ($q) use ($like, $table) {
                $q->where('cities.name', 'like', $like)->orWhere("{$table}.address_first", 'like', $like);
            });
        }
        if ($filters['countryAlias']) {
            $query->where('countries.alias', 'like', self::likeValue($filters['countryAlias']));
        }
        if ($filters['criterias']) {
            $typeKey = $service === 'Cruises' ? 'cruise' : strtolower($service);
            $query->whereIn("{$table}.id", function ($q) use ($filters, $typeKey) {
                $q->from('services_has_criterias')->select('service_id')
                    ->whereIn('criteria_id', $filters['criterias'])
                    ->where('service_type', ServicesHasCriteria::$serviceTypes[$typeKey]);
            });
        }
        if ($filters['rate']) {
            $query->where("{$table}.rate", $filters['rate']);
        }

        return $query;
    }

    /**
     * get data to display
     * @param  Request $request
     * @return mixed
     */
    public function show(Request $request)
    {
        abort_unless(in_array($request->service, array_merge(['Service', 'All'], self::SEARCH_SERVICES), true), 422, 'Select a valid supplier service.');

        $filters = [
            'name' => trim((string) ($request->input('search.value') ?: $request->searchname)),
            'cityCode' => $request->city_code,
            'cityName' => trim((string) $request->input('city_name')),
            'countryAlias' => $request->countryalias,
            'criterias' => array_values(array_filter((array) $request->criterias, 'is_numeric')),
            'rate' => $request->rates,
        ];

        $services = in_array($request->service, ['Service', 'All'], true)
            ? array_values(array_diff(self::SEARCH_SERVICES, ['Transfer', 'Flight', 'Cruises']))
            : [$request->service];

        $parts = array_map(function ($service) use ($filters) {
            return $this->supplierQuery($service, $filters);
        }, $services);

        $can = \Auth::user()->can('tour_package.create') ? 1 : 0;
        $actionColumn = $request->actionColumn === 'add-service-column' ? 'add-service-column' : 'action';

        return $this->dataTableResponse($request, $parts, function ($row) use ($actionColumn, $can) {
            return [$actionColumn => $actionColumn === 'action'
                ? $this->dataIdFor($row->service_type, $row->id, $row->name, $can)
                : $this->serviceButtonFor($row->service_type, $row->id, $row->name)];
        });
    }

    /**
     * DataTables server-side JSON (draw / recordsTotal / recordsFiltered / data) for one query per
     * supplier type, paged and sorted in SQL. Text columns are HTML-escaped: the tables insert them as HTML.
     */
    private function dataTableResponse(Request $request, array $parts, callable $extraColumns)
    {
        // Per-type counts are cheap; counting the UNION would build the whole result first
        $total = array_sum(array_map(function ($part) {
            return (clone $part)->count();
        }, $parts));

        $order = $request->input('order.0');
        $columnKey = $order ? $request->input('columns.' . (int) $order['column'] . '.data') : null;
        $sortable = ['nameService' => 'name', 'name' => 'name', 'address_first' => 'address_first', 'country' => 'country',
            'city' => 'city', 'work_phone' => 'work_phone', 'contact_name' => 'contact_name'];
        $sort = $sortable[$columnKey] ?? 'name';
        $dir = ($order['dir'] ?? 'asc') === 'desc' ? 'desc' : 'asc';

        $start = max(0, (int) $request->input('start', 0));
        $length = (int) $request->input('length', -1);
        $length = $length > 0 ? min($length, 500) : null;

        // Top-N per type: the first start+length rows overall must be among each type's first start+length
        $union = null;
        foreach ($parts as $part) {
            $part = clone $part;
            if ($length) {
                $part->orderBy($sort, $dir)->orderBy('service_type')->orderBy('id')->limit($start + $length);
            }
            $union = $union ? $union->unionAll($part) : $part;
        }
        $query = DB::query()->fromSub($union, 'suppliers')->orderBy($sort, $dir)->orderBy('service_type')->orderBy('id');
        if ($length) {
            $query->offset($start)->limit($length);
        }

        $page = $query->get()->map(function ($row) use ($extraColumns) {
            $data = [];
            foreach (self::RESULT_COLUMNS as $column) {
                $data[$column] = $column === 'id' ? $row->id : e((string) ($row->$column ?? ''));
            }
            return array_merge($data, $extraColumns($row));
        })->values();

        return response()->json([
            'draw' => (int) $request->input('draw', 0),
            'recordsTotal' => $total,
            'recordsFiltered' => $total,
            'data' => $page,
        ]);
    }

    public function generateTableServiceList(Request $request){
        $types = $this->tour_package->servicesTypes;
        $this->validate($request, [
            'service_type_id' => ['required', 'integer', \Illuminate\Validation\Rule::in(array_keys($types))],
            'service_id' => 'nullable|integer',
        ]);

        $serviceName = ucfirst($types[$request->service_type_id]);
        $serviceName = $serviceName === 'Cruise' ? 'Cruises' : $serviceName;
        abort_unless(in_array($serviceName, self::SEARCH_SERVICES, true), 422, 'This service type has no supplier list.');

        $empty = ['name' => '', 'cityCode' => null, 'cityName' => '', 'countryAlias' => null, 'criterias' => [], 'rate' => null];
        $class = 'App\\' . $serviceName;
        $query = $this->supplierQuery($serviceName, $empty)
            ->where((new $class)->getTable() . '.id', '!=', (int) $request->service_id);

        return $this->dataTableResponse($request, [$query], function ($row) {
            $type = strtolower($row->service_type);
            $name = e($row->name);
            $pre_loader = $type == 'transfer' ? '' : 'pre-loader-func';
            return ['action-change-service' => "<button class='btn btn-success change-service-button $pre_loader' data-service_type='{$type}' data-service_id='{$row->id}' data-service_name=\"{$name}\">Change</button>"];
        });
    }

    /** "Add" button for a search row (type is the model class basename, e.g. "Hotel") */
    public function serviceButtonFor($serviceType, $id, $name)
    {
        $type = strtolower($serviceType);
        $name = e($name);
        $id = (int) $id;
        $pre_loader = $type == 'hotel' || $type == 'transfer' ? '' : 'pre-loader-func';
        return "<button class='btn btn-success add-service-button $pre_loader' data-link=".route('tour_package.store')." data-service_type='{$type}' data-service_id='{$id}' data-service_name=\"{$name}\">Add</button>";
    }

    /** View (and, with permission, "Tour") buttons for a search row */
    public function dataIdFor($serviceType, $id, $name, $can)
    {
        $id = (int) $id;
        $low = strtolower($serviceType);
        if ($low == 'flight') $low .= 's';
        $link = route("{$low}.show", $id);
        $name = e($name);
        $type = e($serviceType);
        // The row opens the supplier; the eye button is a visible, keyboard-reachable way to do the same
        $btn = "<a class='btn btn-warning btn-sm btn-icon show-button' href='{$link}' data-link='{$link}' title='View'><i class='ti ti-eye'></i></a>";
        if ($can){
            $btn = "<button class='btn btn-success tourAdd' hidden data-id=$id data-type=$type data-service_name=\"{$name}\">Tour</button>" . $btn;
        }

        return $btn;
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
            $criteria_type = CriteriaType::where('name', strtolower((string) $request->serviceType))->first();
            $criterias = $criteria_type ? Criteria::where('criteria_type', $criteria_type->id)->get() : collect();
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
        // Root fix: Only process valid services (existing model classes)
        // This prevents any errors from non-existent classes
        $validServices = $this->getValidServices();
        
        foreach ($validServices as $service) {
        	if ($service == 'Transfer') {
        		continue;
	        }
            
            // All services in $validServices have been validated to exist
            $namespace = 'App\\' . $service;
            $services = [];
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
        
        // Root fix: Ensure we always return a valid array
        return is_array($collection) ? $collection : [];
    }

    public function serviceButton($model)
    {
        return $this->serviceButtonFor(class_basename(get_class($model)), $model->id, $model->name);
    }

    public function dataId($model, $can)
    {
        return $this->dataIdFor(class_basename(get_class($model)), $model->id, $model->name, $can);
    }
}
