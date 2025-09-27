<?php

namespace App;

use App\Helper\TourPackage\TourService;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Helper\Trackable;
// use App\Helper\TourPackage\TourService;
use App\Helper\HelperTrait;

/**
 * Class TourPackage.
 *
 * @author The scaffold-interface created at 2017-04-12 01:04:20pm
 * @link https://github.com/amranidev/scaffold-interface
 * @mixin \Eloquent
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\TourDay[] $tourDays
 * @property int $id
 * @property string $name
 * @property string $description
 * @property int $status
 * @property bool $paid
 * @property string $pax
 * @property string $total_amount
 * @property int $currency
 * @property string $time_from
 * @property string $time_to
 * @property string $rate
 * @property string $note
 * @property int $type
 * @property int $reference
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property \Carbon\Carbon $deleted_at
 * @method static \Illuminate\Database\Query\Builder|\App\TourPackage whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\TourPackage whereCurrency($value)
 * @method static \Illuminate\Database\Query\Builder|\App\TourPackage whereDeletedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\TourPackage whereDescription($value)
 * @method static \Illuminate\Database\Query\Builder|\App\TourPackage whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\TourPackage whereName($value)
 * @method static \Illuminate\Database\Query\Builder|\App\TourPackage whereNote($value)
 * @method static \Illuminate\Database\Query\Builder|\App\TourPackage wherePaid($value)
 * @method static \Illuminate\Database\Query\Builder|\App\TourPackage wherePax($value)
 * @method static \Illuminate\Database\Query\Builder|\App\TourPackage whereRate($value)
 * @method static \Illuminate\Database\Query\Builder|\App\TourPackage whereReference($value)
 * @method static \Illuminate\Database\Query\Builder|\App\TourPackage whereStatus($value)
 * @method static \Illuminate\Database\Query\Builder|\App\TourPackage whereTimeFrom($value)
 * @method static \Illuminate\Database\Query\Builder|\App\TourPackage whereTimeTo($value)
 * @method static \Illuminate\Database\Query\Builder|\App\TourPackage whereTotalAmount($value)
 * @method static \Illuminate\Database\Query\Builder|\App\TourPackage whereType($value)
 * @method static \Illuminate\Database\Query\Builder|\App\TourPackage whereUpdatedAt($value)
 * @property int|null $pax_free
 * @property int|null $parrent_package_id
 * @property int|null $description_package
 * @property int|null $driver_id
 * @property-read \App\Driver|null $driver
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\HotelRoomTypes[] $room_types_hotel
 * @property-read \App\Tour $tour
 * @method static bool|null forceDelete()
 * @method static \Illuminate\Database\Query\Builder|\App\TourPackage onlyTrashed()
 * @method static bool|null restore()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\TourPackage whereDescriptionPackage($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\TourPackage whereDriverId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\TourPackage whereParrentPackageId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\TourPackage wherePaxFree($value)
 * @method static \Illuminate\Database\Query\Builder|\App\TourPackage withTrashed()
 * @method static \Illuminate\Database\Query\Builder|\App\TourPackage withoutTrashed()
 */
class TourPackage extends Model
{
    use Trackable;
    use HelperTrait;

    use SoftDeletes;

    public static $roomsPeopleCount = [
    	'SIN'   => 1,
	    'DOU'   => 2,
	    'TRI'   => 3,
	    'DFS'   => 1,
	    'TWN'   => 2,
	    'SUE'   => 1
    	];

    protected $dates = ['deleted_at'];

    protected $table = 'tour_packages';

    protected $guarded = [];

    /**
    * tour day.
    *
    * @return  \Illuminate\Support\Collection;
    */
    public function tourDays()
    {
        return $this->belongsToMany('App\TourDay', 'packages_tour_days');
    }

    public function status(){
        return $this->belongsTo('App\Status', 'status', 'id');
    }

    public function driver()
    {
    	return $this->belongsTo('App\Driver');
    }

    /**
    * Assign a tourDay .
    *
    * @param  $tourDay
    * @return  mixed
    */
    public function assignTourDay($tourDay)
    {
        return $this->tourDays()->attach($tourDay);
    }

    /**
    * Remove a tour day.
    *
    * @param  $tourDay
    * @return  mixed
    */
    public function removeTourDay($tourDay)
    {
        return $this->tourDays()->detach($tourDay);
    }

    public function service()
    {
        // $serviceType = TourService::$serviceTypes[$this->type];
        // $service =  TourService::getService($serviceType);
        if (is_null($this->type)) return null;
        $serviceName = $this->servicesTypes[$this->type];
        $namespace = $this->createNamespace($serviceName);
        $model = $namespace::find($this->reference);
        if (is_null($model)) return null;
        $model->service_type = $serviceName;
        return $model;
    }

    public function room_types_hotel(){
        return $this->hasMany('App\HotelRoomTypes');
    }

    public function getPaxFreeAttribute($value)
    {
        return "+{$value}";
    }

    public function getStatusName()
    {
        if($this->status == null){
            return '';
        }

        $status = Status::find($this->status);
        if ($status) {
        	return $status->name;
        }

        return '';


    }

    public function hasParrent()
    {
        return $this->parrent_package_id ? true : false;
    }

    public function parrent()
    {
        return TourPackage::findOrFail($this->parrent_package_id);
    }

    public function hasChild()
    {
        return TourPackage::where('parrent_package_id', $this->id)->first() ? true : false;
    }

    public function getChild()
    {
        return TourPackage::where('parrent_package_id', $this->id)->first();
    }

    public function getParrentTour($step = 0) // @TODO: solve problem with tour days(return null)
    {
    	if ($step == 3) {
    		return false;
	    }
        $tourDay = $this->tourDays()->first();
        if (!$tourDay) {

        	sleep(1);
        	return $this->getParrentTour($step+1);
        }
        return Tour::find($tourDay->tour);
    }

    public function descriptionPackage()
    {
        return $this->description_package ? true : false;
    }

    public function getTour()
    {
    	if ($this->tourDays->isNotEmpty()) {
		    $tourId = $this->tourDays[0]->tour;

		    return Tour::query()->where('id', $tourId)->first();
	    } else {
    		return $this->tour;
	    }

    }

    public function getTotalAmountTourPackage(){
        $priceForOne = $this->total_amount;
        $qtyPax = $this->pax;

        $totalAmountTourPackage = 0;

        if($this->total_amount_manually == 0 || $this->total_amount_manually == null){
            if (is_null($this->type)) return null;
            if(strtolower($this->servicesTypes[$this->type]) == 'hotel'){
                $totalAmountTourPackage = $priceForOne * $qtyPax;
                $totalAmountTourPackage = $totalAmountTourPackage == 0 ? $this->getTotalAmountAuto() : $totalAmountTourPackage;
            }else{
                $totalAmountTourPackage = $priceForOne * $qtyPax;
            }
        }else{
            $totalAmountTourPackage = $this->total_amount_manually;
        }

        return $totalAmountTourPackage;
    }

    public function getPricePerPersonForHotel(){
        return $this->pax == 0 ? 0 : $this->getTotalAmountAuto() / $this->pax;
    }


    public function getPlaceId(){
        return GooglePlaces::query()
            ->where('type', (int) GooglePlaces::$services[strtolower($this->service()->service_type)])
            ->where('service_id', $this->reference)
            ->first();
    }

	/**
	 * Getting price of room by room type
	 *
	 * @param $roomTypeId
	 *
	 * @return int
	 */
    public function getRoomTypePrice($roomTypeId, $isQuotation = null)
    {

    	$roomType = false;
    	if ($this->room_types_hotel) {
		    $roomType =  $this->room_types_hotel->first(function($item) use ($roomTypeId) {
			    return $item->room_type_id == $roomTypeId;
		    });
	    }
	    if ($roomType) {
		    if (!$isQuotation ) {
                return $roomType->price;
            }    
            if ($roomType->room_type_id == 2){
                return $roomType->price;
            }
            return $roomType->price - $this->getDoublePrice();
	    }
	    return 0;
    }

    public function getDoublePrice() {
    	$doubleRoom = RoomTypes::where(['code' => 'DOU'])->first();
	    $roomType = false;
	    if ($this->room_types_hotel) {
		    $roomType =  $this->room_types_hotel->first(function($item) use ($doubleRoom) {
			    return $item->room_type_id == $doubleRoom->id;
		    });
	    }
	    if ($roomType) {
		    return $roomType->price;
	    }
	    return 0;
    }

	public function getSinglePrice() {
		$doubleRoom = RoomTypes::where(['code' => 'SIN'])->first();
		$roomType = false;
		if ($this->room_types_hotel) {
			$roomType =  $this->room_types_hotel->first(function($item) use ($doubleRoom) {
				return $item->room_type_id == $doubleRoom->id;
			});
		}
		if ($roomType) {
			return $roomType->price - $this->getDoublePrice() ;
		}
		return $this->getDoublePrice();
	}

	/**
	 * Getting price of room by room type
	 *
	 * @param $roomTypeId
	 *
	 * @return int
	 */
	public function getRoomTypeCount($roomTypeId)
	{
		$roomType = false;
		if ($this->room_types_hotel) {
			$roomType =  $this->room_types_hotel->first(function($item) use ($roomTypeId) {
				return $item->room_type_id == $roomTypeId;
			});
		}
		if ($roomType) {
			return $roomType->count;
		}
		return 0;
	}

	public function tour()
	{
		return $this->belongsTo('App\Tour');
	}

	public function menu()
	{
		return $this->belongsTo('App\Menu');
	}

	public function getTotalAmountAuto(){
	    $total_amount = 0;

        if(strtolower($this->servicesTypes[$this->type]) != 'hotel'){
            $total_amount = $this->pax * $this->total_amount;
        }else{
            $room_types = HotelRoomTypes::query()->where('tour_package_id', $this->id)->get();
            $price_rooms = 0;
            foreach ($room_types as $room_type){
                $peopleCount = $room_type->count ;
                $price_rooms += $peopleCount * $room_type->price;
            }

            $total_amount = $price_rooms;
        }


        return $total_amount;
	}

    public function menus()
    {
    	return $this->hasMany(PackageMenu::class);
    }

    public function getTransferDrivers(){
        $transfer_id = $this->service()->id;
        $transfer_drivers = TransferToDrivers::query()
            ->where('transfer_id', $transfer_id)
            ->where('tour_package_id', $this->id)
            ->where('tour_id', $this->getTour()->id)
            ->get();

        $transfer_drivers_id = collect();

        foreach ($transfer_drivers as $driver){
            $transfer_drivers_id->push($driver->driver_id);
        }

        $drivers = Driver::query()->whereIn('id', $transfer_drivers_id)->get();

        return $drivers;
    }

      public function getServiceLinkAttribute() {

    	$serviceType = strtolower($this->service()->service_type??"");
        if($serviceType != ""){
    	if($serviceType == 'flight') {
		    $serviceType  = 'flights';
	    }
    	$routeName = '';
    	return route($serviceType.'.show', ['id' => $this->service()->id]);
    }
    return route('tour.index');
    }
	
	 public function hotel_offers()
		{
			return $this->hasMany(HotelOffers::class, 'package_id','id');
		}
	public function latestHotelOffer()
		{
			return $this->hasOne(HotelOffers::class, 'package_id', 'id')->latest('id');
		}
}
