<?php
namespace App\Library\Services;
  
use App\TransferToDrivers;
use App\TripToDrivers;

class DeleteModel
{
    private $servicesTypes = [
        0   => 'hotel',
        1   => 'event',
        2   => 'guide',
        3   => 'transfer',
        4   => 'restaurant',
        5   => 'tourpackage',
        6   => 'cruise',
        7   => 'flight',
        8   => 'rooming list'
    ];

    public function check($model, $modelName)
    {
        $ret = NULL;
        $flip_servicesTypes = array_flip($this->servicesTypes);
        switch ($modelName){
            case "Rate":    
                $count = $model->hotels->count();
                if ($count > 0){
                    $ret = 'The  '. $model->name . ' is used by hotels!';
                }
                break;

            case "Restaurant": 
                $tp = \App\TourPackage::where('type', $flip_servicesTypes[strtolower($modelName)])->where('reference', $model->id)->get();
                $count = $tp->count();

                if ($count > 0){
                    $ret = "Delete ". $model->name . "! It used in " .$count. " tours!";
                }
                break;

            case "Event":
                $tp = \App\TourPackage::where('type', $flip_servicesTypes[strtolower($modelName)])->where('reference', $model->id)->get();
                $count = $tp->count();
                if ($count > 0){
                    $ret = "Delete ". $model->name . "! It used in " .$count. " tours!";
                }
                break;

            case "Flight":
                $tp = \App\TourPackage::where('type', $flip_servicesTypes[strtolower($modelName)])->where('reference', $model->id)->get();
                $count = $tp->count();
                if ($count > 0){
                    $ret = "Delete ". $model->name . "! It used in " .$count. " tours!";
                }
                break;

            case "Cruise":
                $tp = \App\TourPackage::where('type', $flip_servicesTypes[strtolower($modelName)])->where('reference', $model->id)->get();
                $count = $tp->count();
                if ($count > 0){
                    $ret = "Delete ". $model->name . "! It used in " .$count. " tours!";
                }
                break;
                
            case "Guide":
                $tp = \App\TourPackage::where('type', $flip_servicesTypes[strtolower($modelName)])->where('reference', $model->id)->get();
                $count = $tp->count();
                if ($count > 0){
                    $ret = "Delete  ". $model->name . "! It used in " .$count. " tours!";
                }
                break;

            case "Transfer":
                $tp = \App\TourPackage::where('type', $flip_servicesTypes[strtolower($modelName)])->where('reference', $model->id)->get();
                $count = $tp->count();
                if ($count > 0){
                    $ret = "Delete  ". $model->name . "! It used in " .$count. " tours!";
                }
                break;

            case "Driver":
                $transfer_drivers = TransferToDrivers::query()->where('driver_id', $model->id)->get();
                $trip_drivers = TripToDrivers::query()->where('driver_id', $model->id)->get();
                $count = $transfer_drivers->count() + $trip_drivers->count();
                if($count > 0){
                    $ret = "Delete  ". $model->name . "! It used in " .$count. " bus days!";
                }
                break;

            case "Criteria":
                $tp = \App\ServicesHasCriteria::where('criteria_id', $model->id)->get();
                $count = $tp->count();
                if ($count > 0){
                    $ret = "Delete  ". $model->name . "! It used in " .$count. " tours!";
                }
                break;

            case "User":
                $taskCount = count($model->getTasksAttachedToUser());
                $tourCount = count($model->getToursAttachedToUser());
                
                if ($tourCount + $taskCount > 0){
                    $ret = "Delete User ". $model->name . "! It used in " .$tourCount. " tours and in " .$taskCount. " tasks!";
                }
                break;
                
            case "Bus":
                $bd = \App\BusDay::where('bus_id', $model->id)->get();
                $count = $bd->count();
                if ($count > 0){
                    $ret = "Delete User ". $model->name . "! It used in " .$count. " tours!";
                }
                break;

            default:
                return NULL;
        }
		
		if ($ret !== NULL) {
        $model->delete();
    	}
        return $ret;
    }
}