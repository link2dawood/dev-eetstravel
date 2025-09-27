<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Helper\HelperTrait;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Yajra\Datatables\Datatables;
use Illuminate\Support\Facades\DB;
use App\Tour;
use App\TourPackage;

class   ServicesController extends Controller
{
    use HelperTrait;
    /**
     * get service name for datatables
     * @param  Request $request get service name
     * @return mixed           
     */
    public function datatable(Request $request)
    {
    	$service = $this->createNamespace($request->service_name);
    	return $this->data($service);
    }
    /**
     * return datatable for service
     * @param  $model namespace for builder
     * @return mixed        
     */
    public function data($model)
{
    $data = $this->prepareData($model);
    return Datatables::of($data)
        ->addColumn('action', function ($data) {
            return $data->getButton($data->id);
        })
        ->addColumn('city', function ($data) use ($model) {
            $city_data = $model::find($data->id);
            return  $data->city ?? $city_data->city;
        })
        ->addColumn('select', function ($data) {
            return DatatablesHelperController::getSelectButton($data->id, $data->name);
        })
        ->rawColumns(['select', 'action'])
        ->make(true);
}

    /**
     * prepare data for datatables
     * @param  $data namespace
     * @return mixed       
     */
    public function prepareData($data)
    {

    	$model = $this->getTableName($data);
        return ($model == 'hotels') ?
    	$data::leftJoin('countries', 'countries.alias', '=', "{$model}.country")
    		->leftJoin('cities', 'cities.id', '=', "{$model}.city")
    		->select("{$model}.id", 
    			"{$model}.name", 
    			"{$model}.address_first", 
    			"cities.name as city", 
    			'countries.name as country', 
    			"{$model}.work_phone", 
    			"{$model}.status", 
    			"{$model}.contact_email","{$model}.city as hotel_city")
        :
            $data::leftJoin('countries', 'countries.alias', '=', "{$model}.country")
    		->leftJoin('cities', 'cities.id', '=', "{$model}.city")
    		->select("{$model}.id", 
    			"{$model}.name", 
    			"{$model}.address_first", 
    			"cities.name as city", 
    			'countries.name as country', 
    			"{$model}.work_phone", 
    			"{$model}.contact_email","{$model}.city as hotel_city");
        
    }
    public function showTourPackageHistory(Request $request, $id)
    {
        $service = $this->createNamespace($request->service_name);
        $model = $service::findOrFail($id);
        $packages = $this->getAllTourPackageWithService($model);
        // dd($model);
        return response()->view('component.services_history', ['packages' => $packages]);
    }
    public function getAllTourPackageWithService($serviceModel)
    {
        $name = class_basename($serviceModel);
        $serviceTypeId = array_search($name, $this->servicesTypes);
        $tourPackage = TourPackage::whereNull('parrent_package_id')
            ->where('type', $serviceTypeId)
            ->where('reference', $serviceModel->id)
            ->with('tourDays')
            ->orderBy('created_at','DESC')
            ->groupBy('time_from')
            ->groupBy('time_to')
            ->get();

        foreach ($tourPackage as $package) {
        	if (isset($package->tourDays[0])) {
        		try {
		            $tour = Tour::withTrashed()->findOrFail($package->tourDays[0]->tour);
			        if ($tour->deleted_at) $tour->deleted = true;
			        $package->tour = $tour;
		        } catch (\Exception $e) {
        			continue;
		        }
	        }
        }

        return $tourPackage;
    }



    public function getServiceForTourPackage($package)
    {
        
    }
}
