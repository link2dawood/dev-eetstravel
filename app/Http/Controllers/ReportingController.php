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
    public function index()
    {

		$targetMonths = array('June', 'May', 'April', 'March', 'Febuary');
        $currentMonth = date('m');
        $currentYear = date('Y');
        $totalAmounts = array();
		$accounts = Account::all();
        foreach ($targetMonths as $month) {
            $monthValue = date('m', strtotime($month));
            $yearValue = $monthValue > $currentMonth ? $currentYear - 1 : $currentYear;

            $totalResult = 0;
            foreach($accounts as $account){
				$totalResult = $account->getTotalAmountForDateRange($monthValue,$yearValue);

		}

            $totalAmounts[$month] = $totalResult;
        }
        $date = date("Y-m-d");

        $transactions_by_date = ClientInvoices::where("date", $date)->get();

        $transactions = ClientInvoices::all();
       
		
		

        $options =  ["Event","Hotel", "Restaurant", "Guide"];
	
        // Get default "All" services data for initial table display
        $services = $this->getCollection(null, null, null, null, null);
        $collection = collect($services);
        $servicesData = $collection->unique()->map(function ($service) {
            $type = class_basename(get_class($service));
            $id = $service->id;
            $low = strtolower($type);
            if (strtolower($type) == 'flight') $low .= 's';

            $can = 0;
            if (\Auth::user()->can('tour_package.create')) {
                $can = 1;
            }

            $link = route("reporting.{$low}.show", ['id' => $service->id]);
            $btn = "";
            if ($can) {
                $btn .= "<a class='btn btn-primary btn-sm' hidden data-id=$id data-type=$type data-service_name=\"{$service->name}\" id='service-property' href='{$link}' data-link={$link}><i class='fa fa-info-circle'></i></a>";
            }

            $service->action_buttons = $btn;
            return $service;
        });

        return view('reporting.index', compact( "options","accounts","targetMonths", "servicesData"));
    }

    public function show(Request $request)
    {

        $cityCode       = $request->city_code;
        $countryAlias   = $request->countryalias;
        $searchName     = $request->searchname;
        $criterias      = $request->criterias;
        $rates          = $request->rates;

        if ($request->search['value']) $searchName = $request->search['value'];

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


        /*this amd route*/
        
        $hotel = Hotel::leftJoin('rates', 'rates.id', '=', 'hotels.rate')
            ->select('hotels.*', 'rates.name as rate_name')
            ->where('hotels.id', $id)
            ->first();

        $hotel->getCriterias();
        if ($hotel == null) {
            return abort(404);
        }

        $criterias = Criteria::leftJoin('criteria_types', 'criterias.criteria_type', '=', 'criteria_types.id')
            ->orderBy('criterias.name', 'asc')
            ->select('criterias.*', 'criteria_types.name as criteria_name')
            ->where('criteria_types.service_type', '=', 'hotel')
            ->get();
        
        $invoice_tours = [];
        $invoices = [];
        $tour_packages = TourPackage::where("name", $hotel->name)->get();
        foreach ($tour_packages as $tour_package) {
            $invoice_tourss =  InvoicesTours::where("package_id", $tour_package->id)->get();

            if (count($invoice_tourss) != 0) {
                array_push($invoice_tours, $invoice_tourss);
            }
        }

        foreach ($invoice_tours as $invoice_tour) {

            foreach ($invoice_tour as $tour_invoice) {
                # code...
                $invoicess = Invoices::find($tour_invoice->invoices_id);
                array_push($invoices, $invoicess);
            }
        }

        $targetMonths = array('June', 'May', 'April', 'March', 'Febuary');
        $currentMonth = date('m');
        $currentYear = date('Y');
        $totalAmounts = array();

        foreach ($targetMonths as $month) {
            $monthValue = date('m', strtotime($month));
            $yearValue = $monthValue > $currentMonth ? $currentYear - 1 : $currentYear;

            $invoice_total = 0;
            $totalResult = 0;
            foreach ($invoices as $invoice) {

                $invoice_total  = $invoice_total + $invoice->total_amount;
                $totalResult = $invoice->getTotalAmountForMonth($monthValue, $yearValue) ?? 0;
            }

            $totalAmounts[$month] = $totalResult;
        }
        return view('reporting.hotel_show', compact(
            'hotel',
            'criterias',
            'invoice_total',
            'totalAmounts'
        ));
    }
    public function restaurant_show($id, Request $request)
    {

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
        $invoice_tours = [];
        $invoices = [];
        $tour_packages = TourPackage::where("name", $restaurant->name)->get();
        foreach ($tour_packages as $tour_package) {
            $invoice_tourss =  InvoicesTours::where("package_id", $tour_package->id)->get();

            if (count($invoice_tourss) != 0) {
                array_push($invoice_tours, $invoice_tourss);
            }
        }

        foreach ($invoice_tours as $invoice_tour) {

            foreach ($invoice_tour as $tour_invoice) {
                # code...
                $invoicess = Invoices::find($tour_invoice->invoices_id);
                array_push($invoices, $invoicess);
            }
        }

        $targetMonths = array('June', 'May', 'April', 'March', 'Febuary');
        $currentMonth = date('m');
        $currentYear = date('Y');
        $totalAmounts = array();

        foreach ($targetMonths as $month) {
            $monthValue = date('m', strtotime($month));
            $yearValue = $monthValue > $currentMonth ? $currentYear - 1 : $currentYear;

            $invoice_total = 0;
            $totalResult = 0;
		
            foreach ($invoices as $invoice) {

                $invoice_total  = $invoice_total + $invoice->total_amount;
                $totalResult = $invoice->getTotalAmountForMonth($monthValue, $yearValue) ?? 0;
            }

            $totalAmounts[$month] = $totalResult;
        }



        return view('reporting.restaurant_show', compact("restaurant", "criterias", "invoice_total", "totalAmounts"));
    }
   
    public function event_show($id, Request $request)
    {


        /*this amd route*/
        $event = Event::leftJoin('rates', 'rates.id', '=', 'events.rate')
            ->select('events.*', 'rates.name as rate_name')
            ->where('events.id', $id)
            ->first();
        $event->getCriterias();
        $criterias = Criteria::leftJoin('criteria_types', 'criterias.criteria_type', '=', 'criteria_types.id')
            ->orderBy('criterias.name', 'asc')
            ->select('criterias.*', 'criteria_types.name as criteria_name')
            ->where('criteria_types.service_type', '=', 'event')
            ->get();
        $invoice_tours = [];
        $invoices = [];
        $tour_packages = TourPackage::where("name", $event->name)->get();
        foreach ($tour_packages as $tour_package) {
            $invoice_tourss =  InvoicesTours::where("package_id", $tour_package->id)->get();

            if (count($invoice_tourss) != 0) {
                array_push($invoice_tours, $invoice_tourss);
            }
        }

        foreach ($invoice_tours as $invoice_tour) {

            foreach ($invoice_tour as $tour_invoice) {
                # code...
                $invoicess = Invoices::find($tour_invoice->invoices_id);
                array_push($invoices, $invoicess);
            }
        }

        $targetMonths = array('June', 'May', 'April', 'March', 'Febuary');
        $currentMonth = date('m');
        $currentYear = date('Y');
        $totalAmounts = array();

        foreach ($targetMonths as $month) {
            $monthValue = date('m', strtotime($month));
            $yearValue = $monthValue > $currentMonth ? $currentYear - 1 : $currentYear;

            $invoice_total = 0;
            $totalResult = 0;
            foreach ($invoices as $invoice) {

                $invoice_total  = $invoice_total + $invoice->total_amount;
                $totalResult = $invoice->getTotalAmountForMonth($monthValue, $yearValue) ?? 0;
            }

            $totalAmounts[$month] = $totalResult;
        }

        return view('reporting.event_show', compact("event", "criterias", "invoice_total", "totalAmounts"));
    }
    public function guide_show($id, Request $request)
    {


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
        $invoice_tours = [];
        $invoices = [];
        $tour_packages = TourPackage::where("name", $guide->name)->get();
        foreach ($tour_packages as $tour_package) {
            $invoice_tourss =  InvoicesTours::where("package_id", $tour_package->id)->get();

            if (count($invoice_tourss) != 0) {
                array_push($invoice_tours, $invoice_tourss);
            }
        }

        foreach ($invoice_tours as $invoice_tour) {

            foreach ($invoice_tour as $tour_invoice) {
                # code...
                $invoicess = Invoices::find($tour_invoice->invoices_id);
                array_push($invoices, $invoicess);
            }
        }

        $targetMonths = array('June', 'May', 'April', 'March', 'Febuary');
        $currentMonth = date('m');
        $currentYear = date('Y');
        $totalAmounts = array();

        foreach ($targetMonths as $month) {
            $monthValue = date('m', strtotime($month));
            $yearValue = $monthValue > $currentMonth ? $currentYear - 1 : $currentYear;

            $invoice_total = 0;
            $totalResult = 0;
            foreach ($invoices as $invoice) {

                $invoice_total  = $invoice_total + $invoice->total_amount;
                $totalResult = $invoice->getTotalAmountForMonth($monthValue, $yearValue) ?? 0;
            }

            $totalAmounts[$month] = $totalResult;
        }


        return view('reporting.guide_show', compact("guide", "criterias", "invoice_total", "totalAmounts"));
    }
	
	
public function getTotalAmountForDateRange($accountId, $month, $year)
{
    $totalAmount = Transaction::where('account_id', $accountId)
        ->whereMonth('date', $month)->whereYear('date', $year)->sum('amount');

    return $totalAmount;
}
}
