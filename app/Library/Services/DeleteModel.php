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
                // Include archived suppliers: historical references must remain valid too.
                foreach (['hotels', 'events', 'restaurants', 'guides', 'transfers'] as $table) {
                    if (\Illuminate\Support\Facades\DB::table($table)->where('rate', $model->id)->exists()) {
                        return $model->name . ' cannot be deleted: it is used by ' . $table . '.';
                    }
                }
                break;

            case "Restaurant": 
                $tp = \App\TourPackage::where('type', $flip_servicesTypes[strtolower($modelName)])->where('reference', $model->id)->get();
                $count = $tp->count();

                if ($count > 0){
                    $ret = $model->name . " cannot be deleted: it is used in " . $count . " tour service(s).";
                }
                break;

            case "Event":
                $tp = \App\TourPackage::where('type', $flip_servicesTypes[strtolower($modelName)])->where('reference', $model->id)->get();
                $count = $tp->count();
                if ($count > 0){
                    $ret = $model->name . " cannot be deleted: it is used in " . $count . " tour service(s).";
                }
                break;

            case "Flight":
                $tp = \App\TourPackage::where('type', $flip_servicesTypes[strtolower($modelName)])->where('reference', $model->id)->get();
                $count = $tp->count();
                if ($count > 0){
                    $ret = $model->name . " cannot be deleted: it is used in " . $count . " tour service(s).";
                }
                break;

            case "Cruise":
                $tp = \App\TourPackage::where('type', $flip_servicesTypes[strtolower($modelName)])->where('reference', $model->id)->get();
                $count = $tp->count();
                if ($count > 0){
                    $ret = $model->name . " cannot be deleted: it is used in " . $count . " tour service(s).";
                }
                break;
                
            case "Guide":
                $tp = \App\TourPackage::where('type', $flip_servicesTypes[strtolower($modelName)])->where('reference', $model->id)->get();
                $count = $tp->count();
                if ($count > 0){
                    $ret = $model->name . " cannot be deleted: it is used in " . $count . " tour service(s).";
                }
                break;

            case "Transfer":
                if (\App\Bus::where('transfer_id', $model->id)->exists()
                    || \App\Driver::where('transfer_id', $model->id)->exists()) {
                    return $model->name . ' cannot be deleted: it has active buses or drivers.';
                }
                $tp = \App\TourPackage::where('type', $flip_servicesTypes[strtolower($modelName)])->where('reference', $model->id)->get();
                $count = $tp->count();
                if ($count > 0){
                    $ret = $model->name . " cannot be deleted: it is used in " . $count . " tour service(s).";
                }
                break;

            case "Driver":
                $transfer_drivers = TransferToDrivers::query()->where('driver_id', $model->id)->get();
                $trip_drivers = TripToDrivers::query()->where('driver_id', $model->id)->get();
                $count = $transfer_drivers->count() + $trip_drivers->count();
                if($count > 0){
                    $ret = $model->name . " cannot be deleted: it is used in " . $count . " bus day(s).";
                }
                break;

            case "Criteria":
                $tp = \App\ServicesHasCriteria::where('criteria_id', $model->id)->get();
                $count = $tp->count();
                if ($count > 0){
                    $ret = $model->name . " cannot be deleted: it is used in " . $count . " supplier(s).";
                }
                break;

            case "User":
                $taskCount = count($model->getTasksAttachedToUser());
                $tourCount = count($model->getToursAttachedToUser());
                
                if ($tourCount + $taskCount > 0){
                    $ret = "User " . $model->name . " cannot be deleted: it is used in " . $tourCount . " tour(s) and " . $taskCount . " task(s).";
                }
                break;
                
            case "Bus":
                $bd = \App\BusDay::where('bus_id', $model->id)->get();
                $count = $bd->count();
                if ($count > 0){
                    $ret = "Bus ". $model->name . " cannot be deleted because it is used in " .$count. " bus days.";
                }
                return $ret;

            default:
                return NULL;
        }
        // Only report: the caller deletes when there is no message. (This used to delete
        // exactly the records that are still in use.)
        return $ret;
    }
}
