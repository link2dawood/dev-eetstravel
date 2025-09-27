<?php
/**
 * Created by PhpStorm.
 * User: alex
 * Date: 06.12.17
 * Time: 16:29
 */

namespace App\Helper;


use App\BusDay;
use App\TourPackage;
use App\TransferToBuses;
use App\TransferToDrivers;
use App\TripToDrivers;
use Carbon\Carbon;
use DateInterval;
use DatePeriod;

class BusDayHelper
{

    public function addBusesToTransfer($buses_id, $tour_id, $transfer_id, $tour_package_id, $bus_day_id = null){
        if($buses_id != null){
            foreach ($buses_id as $item){
                $transfer_has_buses_drivers = new TransferToBuses();
                $transfer_has_buses_drivers->bus_id = $item;
                $transfer_has_buses_drivers->tour_id = $tour_id;
                $transfer_has_buses_drivers->transfer_id = $transfer_id;
                $transfer_has_buses_drivers->tour_package_id = $tour_package_id;
                $transfer_has_buses_drivers->bus_day_id = $bus_day_id;
                $transfer_has_buses_drivers->save();
            }
        }
    }


    public function addDriversToTransfer($drivers_id, $tour_id, $transfer_id, $tour_package_id, $bus_day_id = null){
        if($drivers_id != null){
            // Transfer to drivers
            foreach ($drivers_id as $item){
                $transfer_has_buses_drivers = new TransferToDrivers();
                $transfer_has_buses_drivers->driver_id = $item;
                $transfer_has_buses_drivers->tour_id = $tour_id;
                $transfer_has_buses_drivers->transfer_id = $transfer_id;
                $transfer_has_buses_drivers->tour_package_id = $tour_package_id;
                $transfer_has_buses_drivers->bus_day_id = $bus_day_id;
                $transfer_has_buses_drivers->save();
            }
        }
    }


    public function addDriversToTrip($drivers_id, $bus_day_id){
        if($drivers_id != null){
            // Transfer to drivers
            foreach ($drivers_id as $item){
                $trip_driver = new TripToDrivers();
                $trip_driver->driver_id = $item;
                $trip_driver->bus_day_id = $bus_day_id;
                $trip_driver->save();
            }
        }
    }

    public function addDriverToTrip($driver_id, $bus_day_id){
        $trip_driver = new TripToDrivers();
        $trip_driver->driver_id = $driver_id;
        $trip_driver->bus_day_id = $bus_day_id;
        $trip_driver->save();
    }

    public function changeStatusTransferPackage($tour_package_id, $status){
        $tour_package = TourPackage::query()->where('id', $tour_package_id)->first();
        $tour_package->status = $status;
        $tour_package->save();
    }


    public function createBusDayTrip(
        $tour_id,
        $transfer_id,
        $tour_package_id,
        $date,
        $status,
        $bus,
        $name,
        $city,
        $country,
        $drivers_trip,
        $trip_id
    ){
        $bus_day = new BusDay();
        $bus_day->tour_id = $tour_id;
        $bus_day->transfer_id = $transfer_id;
        $bus_day->tour_package_id = $tour_package_id;
        $bus_day->date = $date;
        $bus_day->status_id = $status;
        $bus_day->bus_id = $bus;
        $bus_day->name_trip = $name;
        $bus_day->city_trip = $city;
        $bus_day->country_trip = $country;
        $bus_day->trip_id = $trip_id;
        $bus_day->save();

        $this->addDriversToTrip($drivers_trip, $bus_day->id);
    }

    public function busDayDeleteId($id){
        return BusDay::query()
            ->where('id', $id)
            ->delete();
    }

    public function getBusDayId($id){
        return BusDay::query()
            ->where('id', $id)
            ->first();
    }



    public function getDatesInterval($dep_date, $ret_date){
        $start_date = Carbon::parse($dep_date);
        $end_date = Carbon::parse($ret_date);
        $start_date->modify('-1 day');
        $end_date->modify('+1 day');
        $interval = new DateInterval('P1D');
        $start_date->add($interval);
        $this_days_dvo = new DatePeriod($start_date, $interval ,$end_date);

        $this_days = [];
        foreach ($this_days_dvo as $date){
            $this_days[] = ['date' => $date->format('Y-m-d')];
        }

        return $this_days;
    }


    public function deleteDriversToTrip($bus_day_id){
        return TripToDrivers::query()
            ->where('bus_day_id', $bus_day_id)
            ->delete();
    }

    public function deleteDriverToTrip($bus_day_id, $old_driver_id){
        return TripToDrivers::query()
            ->where('bus_day_id', $bus_day_id)
            ->where('driver_id', $old_driver_id)
            ->delete();
    }

    public function getDriversToTrip($bus_day_id){
        return TripToDrivers::query()
            ->where('bus_day_id', $bus_day_id)
            ->get();
    }


    public function deleteDriversToTransfer($tour_package_id, $bus_day_id = null){
        if($bus_day_id == null){
            return TransferToDrivers::query()
                ->where('tour_package_id', $tour_package_id)
                ->delete();
        }else{
            return TransferToDrivers::query()
                ->where('tour_package_id', $tour_package_id)
                ->where('bus_day_id', $bus_day_id)
                ->delete();
        }
    }

    public function deleteBusesToTransfer($tour_package_id, $bus_day_id = null){
        if($bus_day_id == null){
            return TransferToBuses::query()
                ->where('tour_package_id', $tour_package_id)
                ->delete();
        }else{
            return TransferToBuses::query()
                ->where('tour_package_id', $tour_package_id)
                ->where('bus_day_id', $bus_day_id)
                ->delete();
        }
    }

    public function deleteBusDays($tour_package_id){
        return BusDay::query()
            ->where('tour_package_id', $tour_package_id)
            ->delete();
    }

    public function validateDrivers($drivers_id, $dep_date_transfer, $ret_date_transfer, $tour_package_id = null, $bus_day_id = null){
        $bus_days =
            $tour_package_id == null ?
                    $bus_day_id == null ?
                        BusDay::query()->get() :
                        BusDay::query()->get()->where('id', '!=', $bus_day_id) :
                BusDay::query()->get()->where('tour_package_id', '!=', $tour_package_id);

        $dates_interval = $this->getDatesInterval($dep_date_transfer, $ret_date_transfer);

        $drivers_id_bus_day = [];

        foreach ($dates_interval as $item){
            foreach ($bus_days as $bus_day){
                if($item['date'] == $bus_day->date){
                    // tour
                    if($bus_day->tour_package_id != null){
                        $drivers_bus_day = TransferToDrivers::query()
                            ->where('tour_package_id', $bus_day->tour_package_id)
                            ->get();

                        if($drivers_bus_day->isNotEmpty()){
                            foreach ($drivers_bus_day as $item_bus_day){
                                $drivers_id_bus_day[] = $item_bus_day->driver_id;
                            }
                        }
                    }

                    // trip
                    else{
                        $drivers_trip = TripToDrivers::query()
                            ->where('bus_day_id', $bus_day->id)
                            ->get();

                        if($drivers_trip->isNotEmpty()){
                            foreach ($drivers_trip as $item_trip_driver){
                                $drivers_id_bus_day[] = $item_trip_driver->driver_id;
                            }
                        }
                    }
                }
            }
        }

        if($drivers_id){
            foreach ($drivers_id as $driver_id){
                foreach ($drivers_id_bus_day as $driver_id_bus_day){
                    if((int) $driver_id == (int) $driver_id_bus_day){
                        return false;
                    }
                }
            }
        }

        return true;
    }

    public function validateBuses($bus_id, $dep_date, $ret_date, $tour_package_id = null, $bus_day_id = null){

        $bus_days =
            $tour_package_id == null ?
                $bus_day_id == null ? BusDay::query()->get() : BusDay::query()->get()->where('id', '!=', $bus_day_id) :
                BusDay::query()
                    ->get()
                    ->where('tour_package_id', '!=', $tour_package_id);

        $dates_interval = $this->getDatesInterval($dep_date, $ret_date);

        $buses_id_bus_day = [];
        foreach ($dates_interval as $item){
            foreach ($bus_days as $bus_day){
                if($item['date'] == $bus_day->date){
                    $buses_id_bus_day[] = [
                        'bus_id' => $bus_day->bus_id,
                        'trip_id' => $bus_day->trip_id,
                        'tour_package_id' => $bus_day->tour_package_id
                    ];
                }
            }
        }

        foreach ($buses_id_bus_day as $bus_id_bus_day){
            if((int) $bus_id == (int) $bus_id_bus_day['bus_id']){
                $bus_days_busy = null;
                if($bus_id_bus_day['trip_id'] != null){
                    $bus_days_busy = BusDay::query()
                        ->where('trip_id', $bus_id_bus_day['trip_id'])
                        ->orderBy('date')
                        ->get();
                }

                else if($bus_id_bus_day['tour_package_id'] != null){
                    $bus_days_busy = BusDay::query()
                        ->where('tour_package_id', $bus_id_bus_day['tour_package_id'])
                        ->orderBy('date')
                        ->get();
                }

                $start_busy = (new Carbon($bus_days_busy->first()->date))->format('Y-m-d');
                $end_busy = (new Carbon($bus_days_busy->last()->date))->format('Y-m-d');

                return [
                    'check' => false,
                    'start' => $start_busy,
                    'end' => $end_busy
                ];
            }
        }

        return [
            'check' => true
        ];
    }
}