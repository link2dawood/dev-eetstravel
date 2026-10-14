<?php

namespace App\Http\Controllers;

use App\ClientInvoices;
use App\Invoices;
use App\Office_Tours;
use Illuminate\Http\Request;
use Yajra\Datatables\Datatables;
use App\Helper\HelperTrait;
use App\Criteria;

use App\Hotel;
use App\Event;
use App\Guide;
use App\Restaurant;
use App\HotelContacts;
use App\Helper\FileTrait;
use App\InvoicesTours;
use App\TourPackage;
use App\Account;
use App\Transaction;
use App\TransDetail;
use URL;
use DB;

class ReportingController extends Controller
{
    use FileTrait;
    use HelperTrait;
    public function index(Request $request)
    {
        $options = ['Event', 'Hotel', 'Restaurant', 'Guide'];
        $union = null;
        foreach ($options as $type) {
            if (in_array($request->input('service'), $options) && $request->input('service') !== $type) continue;
            $class = 'App\\' . $type;
            $table = (new $class)->getTable();
            $query = $class::query()->select('id', 'name')->selectRaw('? as supplier_type', [$type]);
            if ($request->filled('q')) $query->where('name', 'like', '%' . $request->input('q') . '%');
            $union = $union ? $union->unionAll($query->toBase()) : $query->toBase();
        }
        $servicesData = DB::query()->fromSub($union, 'suppliers')->orderBy('name')->orderBy('supplier_type')->orderBy('id')->paginate(50)->withQueryString();
        $models = [];
        foreach ($servicesData->getCollection()->groupBy('supplier_type') as $type => $rows) {
            $class = 'App\\' . $type;
            foreach ($class::whereIn('id', $rows->pluck('id'))->get() as $model) $models[$type . ':' . $model->id] = $model;
        }
        $cityIds = collect($models)->pluck('city')->filter()->unique();
        $cities = DB::table('cities')->whereIn('id', $cityIds)->pluck('name', 'id');
        $countries = DB::table('countries')->pluck('name', 'alias');
        $servicesData->setCollection($servicesData->getCollection()->map(function ($row) use ($models, $cities, $countries) {
            $service = $models[$row->supplier_type . ':' . $row->id];
            $service->nameService = $service->name . ' (' . $row->supplier_type . ')';
            $service->city = $cities[$service->city] ?? '';
            $service->country = $countries[$service->country] ?? $service->country;
            $service->show_link = route('reporting.' . strtolower($row->supplier_type) . '.show', $service->id);
            return $service;
        }));
        $accounts = collect(); $targetMonths = [];
        return view('reporting.index', compact('options', 'accounts', 'targetMonths', 'servicesData'));
    }

    public function show(Request $request)
    {

        $cityCode       = $request->city_code;
        $countryAlias   = $request->countryalias;
        $searchName     = $request->searchname;
        $criterias      = $request->criterias;
        $rates          = $request->rates;

        if ($request->input('search.value')) $searchName = $request->input('search.value');
        abort_unless(in_array($request->service, ['Service', 'All', 'Hotel', 'Event', 'Guide', 'Restaurant']), 422);

        if ($request->service === 'Service' || $request->service === 'All') {
            $namespace = $this->getCollection($criterias, $rates, $cityCode, $searchName, $countryAlias);
        } else {
            $serv_find = [];
            $namespace = 'App\\' . $request->service;
            if (!isset($criterias) && !isset($rates)) {
                $model_test = $namespace;
                $table_name = $this->getTableName($model_test);
                $query_builder = $request->service == 'Cruises' || $request->service == 'Flight' ?
                    $namespace::leftJoin('countries', 'countries.alias', '=', "{$table_name}.country_from")
                    ->leftJoin('cities', 'cities.id', '=', "{$table_name}.city_from") :

                    $namespace::leftJoin('countries', 'countries.alias', '=', "{$table_name}.country")
                    ->leftJoin('cities', 'cities.id', '=', "{$table_name}.city");
                if ($cityCode) {
                    $model = $query_builder->select(
                        "{$table_name}.*",
                        "cities.name as city",
                        'countries.name as country'
                    )
                        ->where("{$table_name}.name", 'like', '%' . $searchName . '%')
                        ->where('cities.code', $cityCode)
                        ->get();
                } else {
                    $countryAlias ?
                        $model = $query_builder->select(
                            "{$table_name}.*",
                            "cities.name as city",
                            'countries.name as country'
                        )
                        ->where('countries.alias', 'like', '%' . $countryAlias . '%')
                        ->where("{$table_name}.name", 'like', '%' . $searchName . '%')
                        ->get() :
                        $model = $query_builder->select(
                            "{$table_name}.*",
                            "cities.name as city",
                            'countries.name as country'
                        )
                        ->where("{$table_name}.name", 'like', '%' . $searchName . '%')
                        ->get();
                }

                array_push($serv_find, $model);
            }

            if (isset($criterias) && !isset($rates)) {
                // $namespace = $this->findByCriterias($criterias, $namespace, $request->service);
                array_push($serv_find, $this->findByCriterias(@$criterias, @$namespace, @$request->service, @$cityCode, @$searchName, @$countryAlias));
            }


            if ($rates && !$criterias) array_push($serv_find, $this->findByRate($rates, $namespace, $cityCode, $searchName, $countryAlias));

            if ($rates && $criterias) array_push($serv_find, $this->findByCriterias($criterias, $namespace, $request->service, $rates, $cityCode, $searchName, $countryAlias));
            $collection = collect($serv_find)->collapse()->all();
            foreach ($collection as $item) {
                $tmp = $request->service;
                if ($tmp === 'Transfer') $tmp = 'Bus Company';
                $item['nameService'] = "{$item->name}" . " ({$tmp})";
            }
            $namespace = $collection;
        }
        ini_set('memory_limit', '800M');
        set_time_limit(0);
        //@ToDo: change on correct generate response for datatable
        $namespace = collect($namespace);
        $services = $namespace->unique()->all();
        $data = Datatables::of($services)->addColumn('action', function ($services) {
            return "ok";
        })
            ->rawColumns(['select', 'action', 'link']);
        $can = 0;
        if (\Auth::user()->can('tour_package.create')) {
            $can = 1;
        }
        if ($request->actionColumn === 'add-service-column') {
            $this->serviceColumn($data, $can);
        } else {
            $this->actionColumn($data, $can);
        }

        return $data->make(true);
    }

    public function getCollection($criterias, $rates = null, $cityCode = null, $searchName = null, $countryAlias = null)
    {
        $data = [];
        foreach (['Hotel', 'Event', 'Guide', 'Restaurant'] as $service) {
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

            if ($cityCode) {
                $model = $query_builder->select(
                    "{$table_name}.*",
                    "cities.name as city",
                    'countries.name as country'
                )
                    ->where('cities.code', $cityCode)
                    ->where("{$table_name}.name", 'like', '%' . $searchName . '%')
                    ->get();
            } else {
                if ($searchName) {
                    $countryAlias ?
                        $model = $query_builder->select(
                            "{$table_name}.*",
                            "cities.name as city",
                            'countries.name as country'
                        )
                        ->where('countries.alias', 'like', '%' . $countryAlias . '%')
                        ->where("{$table_name}.name", 'like', '%' . $searchName . '%')

                        ->get() :
                        $model = $query_builder->select(
                            "{$table_name}.*",
                            "cities.name as city",
                            'countries.name as country'
                        )
                        ->where("{$table_name}.name", 'like', '%' . $searchName . '%')
                        ->get();
                } else {
                    $countryAlias ?
                        $model = $query_builder->select(
                            "{$table_name}.*",
                            "cities.name as city",
                            'countries.name as country'
                        )
                        ->where('countries.alias', 'like', '%' . $countryAlias . '%')
                        ->get() :
                        $model = $query_builder->select(
                            "{$table_name}.*",
                            "cities.name as city",
                            'countries.name as country'
                        )
                        ->get();
                }
            }

            if (!$criterias) {
                foreach ($model as $item) {
                    $item['nameService'] = "{$item->name} ({$service})";
                }
                array_push($services, $model);
            }

            if ($criterias) {
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
        $data->addColumn('action', function ($model) use ($can) {
            return $this->dataId($model, $can);
        })->rawColumns(['action']);
    }
    public function dataId($model, $can)
    {
        $type = class_basename(get_class($model));
        $id = $model->id;
        $low = strtolower($type);
        if (strtolower($type) == 'flight') $low .= 's';
        $link = route("reporting.{$low}.show", ['id' => $model->id]);
        $btn = "";
        if ($can) {
            $btn .= "
            
            <a class='btn btn-primary btn-sm' hidden data-id=$id data-type=$type data-service_name=\"{$model->name}\" id='service-property'   href='{$link}' data-link={$link}><i class='fa fa-info-circle'></i></a>";
        }

        return $btn;
    }

    public function hotel_show($id, Request $request)
    {
        return $this->supplierReport(Hotel::findOrFail($id), 0);
    }
    public function restaurant_show($id, Request $request)
    {
        return $this->supplierReport(Restaurant::findOrFail($id), 4);
    }
   
    public function event_show($id, Request $request)
    {
        return $this->supplierReport(Event::findOrFail($id), 1);
    }
    public function guide_show($id, Request $request)
    {
        return $this->supplierReport(Guide::findOrFail($id), 2);
    }
	
	
public function getTotalAmountForDateRange($accountId, $month, $year)
{
    $totalAmount = Transaction::where('account_id', $accountId)
        ->whereMonth('date', $month)->whereYear('date', $year)->sum('amount');

    return $totalAmount;
}

    private function supplierReport($supplier, int $type)
    {
        $invoices = \App\Helper\SupplierInvoices::forService($type, $supplier->id);
        $months = [];
        for ($offset = 4; $offset >= 0; $offset--) {
            $date = now()->startOfMonth()->subMonths($offset);
            $months[$date->format('F Y')] = $invoices->filter(fn ($invoice) => $invoice->received_date && \Carbon\Carbon::parse($invoice->received_date)->format('Y-m') === $date->format('Y-m'))->sum('total_amount');
        }
        return view('reporting.supplier_show', compact('supplier', 'invoices', 'months'));
    }
}
