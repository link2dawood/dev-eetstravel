<?php
namespace App\Helper;

use App\Bus;
use App\Http\Controllers\TourController;
use App\Http\Controllers\TourPackageController;
use Dompdf\Exception;
use Illuminate\Support\Facades\Input;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\Models\Activity;
use App\Country;
use App\Status;
use App\City;
use App\Tour;
use App\Task;
use App\Rate;
use App\TourDay;
use App\Driver;
use App\BusDay;
use Carbon\Carbon;

trait Trackable{
	/**
	 * non-standart links
	 * @var array
	 */
	protected $links = ['cruises.show' => 'Cruises', 'flights.show' => 'Flight', 'tour_package.show' => 'TourPackage'];

    /**
     * listen for model event
     */
	public static function bootTrackable(){
		/**
		 * listen for updating event
		 */
		static::updating(function($model){
			$instance = self::getInstance();
			$data = $instance->prepareLog($model, 'updated');
			$text = "change";
			$i = 0;
            $serviceTypes = [
                0   => 'hotel',
                1   => 'event',
                2   => 'guide',
                3   => 'transfer',
                4   => 'restaurant',
                5   => 'tourPackage',
                6   => 'cruise',
                7   => 'flight',
                8   => 'rooming list'
            ];

           // $data['text'] = "{$model} {$model->attributes}";
            //$instance->log($data);

			foreach ($model->attributes as $key => $value) {
				// if ($i > 2) break;
				// dd($model->assigned_users);
				foreach ($model->original as $oldKey => $oldValue) {
					if ($key === $oldKey && (string) $value !== (string) $oldValue) {
						if ($key == 'pax_free' && (int) $oldValue == (int) $value) continue;
						if ($key == 'paid' && (boolean) $value == (boolean) $oldValue) continue;
						if( $key == 'external_name') return;
                        if( $key == 'transfer_id') return;
						if ($key === 'country' || $key === 'country_begin' || $key === 'country_to'){
							$oldValue = $oldValue == null ? ' ' : $instance->countryName($oldValue);
							$value = $value == null ? ' ' : $instance->countryName($value);
						}
						if ($key == 'time_from' || $key == 'time_to' || $key == 'start_time' || $key == 'dead_line'){
							if (Carbon::parse($oldValue) == Carbon::parse($value)) continue;
						}
						if($key == 'status'){
							$oldValue = $instance->statusName($oldValue);
							$value = $instance->statusName($value);
						}
						if ($key == 'paid' && (boolean) $oldValue !== (boolean) $value){
							$oldValue = $oldValue ? 'paid' : 'not paid';
							$value = $value ? 'paid' : 'not paid';
						}
						if ($key === 'city' || $key == 'city_to' || $key == 'city_from'){
							$oldValue = $oldValue == null ? ' ' : $instance->cityName($oldValue);
                            $value = $value == null ? ' ' : $instance->cityName($value);
						}
                        if ($key == 'tour'){
                            if($oldValue){
                                $old_tour = Tour::withTrashed()->where('id', $oldValue)->first();
                            }else{
                                $old_tour = null;
                            }

                            if($value) {
                                $new_tour = Tour::withTrashed()->where('id', $value)->first();
                               $value = !$value ? ' ' : ($new_tour ? $new_tour->name : ' ');

                            }else{
                                $value='';
                            }
                            $oldValue = $old_tour ? $old_tour->name : 'Without Tour';
                        }

						if ($key == 'task_type'){
							$oldValue = Task::$taskTypes[$oldValue];
							$value = Task::$taskTypes[$value];
						}
						if ($key == 'rate'){
                            $oldValue = (Rate::find($oldValue)) ? Rate::find($oldValue)->name : '';
                            $value =  (Rate::find($value)) ? Rate::find($value)->name : '';
						}
                        if ($key == 'status_id'){
                            $oldValue = $instance->statusName($oldValue);
                            $value = $instance->statusName($value);
                        }
                        if ($key == 'status'){
                            $tour = Tour::where('id',$model->tour_id)->first();
                            if ($tour) $value = $tour->name;
                        }
                        if ($key == 'price_for_one' ){
                            $key = "Price per Person";
                        }
                        if ($key == 'total_amount' ){
                            $key = "Price per Person";
                        }
                        if ($key == 'content' ){
                            $oldValue = preg_replace('/style=\\"[^\\"]*/', '', $oldValue);
                            $value = preg_replace('/style=\\"[^\\"]*/', '', $value);
                        }
                        if($key == 'bus_id'){
                            $key = 'Bus';
                            $oldBus = Bus::query()->where('id', $oldValue)->first();
                            $newBus = Bus::query()->where('id', $value)->first();
                            $oldValue =  $oldBus ? $oldBus->name : ' ';
                            $value =  $newBus ? $newBus->name : ' ';
                        }

                        $comma = ($i > 0) ? ',' : '';
                        $text .= "$comma value of '$key' from '$oldValue' to '$value'";
						$i++;
					}
				}
			}

			if ($data['log_name'] == 'TourPackage'){
				$tour = $model->getParrentTour();
				$nameText = $model->descriptionPackage() ? $model->description : $model->name;

                $nameText = str_replace('Transfer','Bus Company',$nameText);

				if ($tour){ // HOTFIX @ToDo: when drag causes an error
					$text .= " at service: '{$nameText}', at Tour: '{$tour->name}'";
				}else{
                    $text .=  " Service to '{$nameText}'" ;
                }
				$data['properties']['link'] = $instance->findLink('tour', $tour);
			} elseif ($data['log_name'] == 'Task') {
				$text .= " at Task: '{$model->content}'";
			} elseif ($data['log_name'] == 'Templates') {
			        $serv_type = ucfirst($serviceTypes[$model->service_id]);
                    $text .= " at {$data['log_name']} '{$model->getTourName()}' in '{$serv_type}' service";
			} else{

                    if($data['model']['name_trip']) $data['log_name']  = "Trip '".$data['model']['name_trip']."'" ;



			        if($model->name) {
                        $text .= " at {$data['log_name']} '{$model->name}' ";
                    }else{
			            $text .= " at {$data['log_name']} ";
                    }
            }

                $data['text'] = $text;
                $data['text']  = str_replace('Transfer','Bus Company', $data['text']);
                $instance->log($data);
		});

		/**
		 * listen for creating event
		 */
		static::created(function($model){

			$instance = self::getInstance();
			$instance->prepareLog($model);


		});

		/**
		 * listen for deleting event
		 */
		static::deleted(function($model){
			$instance = self::getInstance();
			$instance->prepareLog($model, 'deleted');
		});
	}

	/**
	 * create object instance
	 */
	public static function getInstance(){
		return $instance = new self;
	}
	/**
	 * get country name
	 */
	public function countryName($idCountry)
	{
	    if($idCountry) {
            $countryName = Country::where('alias', $idCountry)->first();
            if ($countryName) return $countryName->name;
        }
	    return '';
	}
	/**
	 * get city name
	 */
	public function cityName($idCity)
	{
	    if($idCity){
            $name = City::find($idCity);
            if ($name) return $name->name;
        }
		return '';
	}
	/**
	 * get status name
	 */
	public function statusName($statusId)
	{
		if ($statusId) {
			return Status::find($statusId)->name;
		}
		return false;
	}
	/**
	 * get tour name
	 */
	public function tourName($tourId)
	{
		if (!$tourId) return '';
		if (!$tourName = Tour::find($tourId)->name) return '';
		return $tourName;
	}
    
    /**
     * prepare data for logging
     * @param  \Illuminate\Database\Eloquent\Model $model  current model
     * @param  string $action current event
     * @return array         logging properties
     */
	public function prepareLog($model, $action = 'created'){

		$className = class_basename(get_class($model));

		if ($className == 'TourPackage' && $action == 'created') return;
        if ($className == 'BusDay' && $action == 'created') return;
        $link = ($action === 'deleted') ? '' : $this->findLink($className, $model);
        $text = ($model->name && $action !== 'updated') ? "{$className}: $model->name $action" : "{$className}: $model->content $action";

        if($className =='TripToDrivers' && $action == 'created') {
            $driver = Driver::where('id', $model['driver_id'])->first();
            $driverDay = BusDay::where('id', $model['bus_day_id'])->first();
            $current_drivers = Input::get('drivers_id_trip');
            $allrivers = Input::get('alldrv');
            
            if($allrivers && $current_drivers){
                $old_name = 'removed';
                ($driverDay->name_trip) ? $tripName = $driverDay->name_trip : $tripName = $driverDay->tour_id;
                (Input::get('trip_mode') == 1) ? $datelog = "on ". Input::get('date') : $datelog ='';
                foreach ($allrivers as $item){
                    foreach ($current_drivers as $current){
                        if($current != $item) { $old_name = 'added';}
                    }
                }

                $text = "change value of 'driver' added '{$driver->name}' at Trip {$tripName} {$datelog}";
            }
        }

        if($className =='Templates' && $action == 'created') {
            $text = "{$className}: {$model->getTourName()} $action for service " . $model->getServiceName();
        }
        elseif ($className =='Templates' && $action == 'deleted'){
            $text = "{$className}: {$model->getTourName()} $action in service " . $model->getServiceName();
        }

        $data = ['log_name' => $className, 'text' => $text, 'model' => $model, 'properties' => ['action' => $action, 'link' => $link]];

		if ($action === 'updated') return $data;

		if ($action == 'deleted') {
			$this->removeDeletedLogsLink($model, $className);
		}



		if ($data['log_name'] == 'Comment') {
			$data['text'] = $text . " at {$model->serviceName()}";
		}

		if ($data['log_name'] == 'TourPackage' && $action == 'deleted'){
			$nameText = $model->descriptionPackage() ? $model->description : $model->name;
			if($model->attributes['tour_id']){
		        $tourName = Tour::find($model->attributes['tour_id'])->name;
		        $data['text'] = "{$data['log_name']}: {$nameText} $action at Tour: {$tourName}";
            }else{
                $tour = $model->getParrentTour();
                $data['text'] = "{$className}: {$nameText} $action at Tour: '{$tour->name}'";
            }
		}


        $data['text']  = str_replace('Transfer','Bus Company',$data['text']);

		$this->log($data);
	}

	/**
	 * find non-standart links for route
	 * @param  string $className model name
	 * @param  \Illuminate\Database\Eloquent\Model $model     current model
	 * @return string
	 */
	public function findLink(string $className, $model){
		$linkName = array_search($className, $this->links);
		try {
		    $link = route( $linkName ? $linkName : strtolower("$className.show"), ['id' => $model->id]);
		}
		catch (\Exception $e) {
			$link ='';
		}
		return $link;
	}
    
    /**
     * log activity
     * @param  array  $data logging properties
     */
	public function log(array $data){
		activity($data['log_name'])
			->withProperties($data['properties'])
			->on($data['model'])
			->log($data['text']);
	}
	/**
	 * remove links when delete model instance
	 */
	public function removeDeletedLogsLink($model, $log_name)
	{
		$old = Activity::where('log_name', $log_name)->where('subject_id', $model->id)->get();
		foreach ($old as $o) {
			$o->update(['properties->link' => null]);
		}
	}
}