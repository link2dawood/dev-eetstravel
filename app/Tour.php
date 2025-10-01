<?php

namespace App;

use App\Helper\HelperTrait;
use App\Http\Controllers\TourPackageController;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Helper\Trackable;
use App\Status;
use Illuminate\Support\Facades\Auth;

/**
 * Class Tour.
 *
 * @author The scaffold-interface created at 2017-04-15 02:18:47pm
 * @link https://github.com/amranidev/scaffold-interface
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Event[] $events
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Guide[] $guides
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Hotel[] $hotels
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Restaurant[] $restaurants
 * @mixin \Eloquent
 * @property int $id
 * @property string $name
 * @property string $overview
 * @property string $remark
 * @property string $departure_date
 * @property string $retirement_date
 * @property string $pax
 * @property string $rooms
 * @property $country_begin
 * @property $city_begin
 * @property $country_end
 * @property $city_end
 * @property string $invoice
 * @property string $ga
 * @property int $status
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property \Carbon\Carbon $deleted_at
 * @method static \Illuminate\Database\Query\Builder|\App\Tour whereCityBegin($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Tour whereCityEnd($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Tour whereCountryBegin($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Tour whereCountryEnd($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Tour whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Tour whereDeletedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Tour whereDepartureDate($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Tour whereGa($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Tour whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Tour whereInvoice($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Tour whereName($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Tour whereOverview($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Tour wherePax($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Tour whereRemark($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Tour whereRetirementDate($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Tour whereRooms($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Tour whereStatus($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Tour whereUpdatedAt($value)
 */
class Tour extends Model
{
    //use Trackable;


    public static $statusColors = [
        '1' => '#f39c12',
        '2' => '#45a163',
        '3' => '#ff9300',
        '4' => '#b90000'
    ];

    use SoftDeletes;

    protected $dates = ['deleted_at'];

    protected $table = 'tours';
    protected $guarded = [];

    public function attachments(){
        return $this->morphMany('App\Attachment', 'attachable');
    }

    public function status(){
        return $this->belongsTo('App\Status', 'status', 'id');
    }


    public function files()
    {
        return $this->hasMany('App\File');
    }


    public function tourRoomTypeHotel()
    {
    	return $this->belongsTo(TourRoomTypeHotel::class);
    }

    public function getTransferStatus(){
        $transfer = Status::query()->where('type', 'service_in_tour')->where('id', $this->transfer->status)->first();

        return $transfer->name;
    }

    /**
     * hotel.
     *
     * @return  \Illuminate\Support\Collection;
     */
    // public function hotels()
    // {
    //     return $this->belongsToMany('App\Hotel', 'hotel_tour');
    // }


    public function tour_days(){
        return $this->hasMany('App\TourDay', 'tour');
    }

    /**
     * Assign a hotel.
     *
     * @param  $hotel
     * @return  mixed
     */
    public function assignHotel($hotel)
    {
        return $this->hotels()->attach($hotel);
    }

    /**
     * Remove a hotel.
     *
     * @param  $hotel
     * @return  mixed
     */
    public function removeHotel($hotel)
    {
        return $this->hotels()->detach($hotel);
    }

    /**
     * event.
     *
     * @return  \Illuminate\Support\Collection;
     */
    public function events()
    {
        return $this->belongsToMany('App\Event', 'event_tour');
    }

    /**
     * Assign a event.
     *
     * @param  $event
     * @return  mixed
     */
    public function assignEvent($event)
    {
        return $this->events()->attach($event);
    }

    /**
     * Remove a event.
     *
     * @param  $event
     * @return  mixed
     */
    public function removeEvent($event)
    {
        return $this->events()->detach($event);
    }


    /**
     * restaurant.
     *
     * @return  \Illuminate\Support\Collection;
     */
    public function restaurants()
    {
        return $this->belongsToMany('App\Restaurant', 'restaurant_tour');
    }

    /**
     * Assign a restaurant.
     *
     * @param  $restaurant
     * @return  mixed
     */
    public function assignRestaurant($restaurant)
    {
        return $this->restaurants()->attach($restaurant);
    }

    /**
     * Remove a restaurant.
     *
     * @param  $restaurant
     * @return  mixed
     */
    public function removeRestaurant($restaurant)
    {
        return $this->restaurants()->detach($restaurant);
    }


    /**
     * guide.
     *
     * @return  \Illuminate\Support\Collection;
     */
    public function guides()
    {
        return $this->belongsToMany('App\Guide', 'guide_tour');
    }

    /**
     * Assign a guide.
     *
     * @param  $guide
     * @return  mixed
     */
    public function assignGuide($guide)
    {
        return $this->guides()->attach($guide);
    }

    /**
     * Remove a guide.
     *
     * @param  $guide
     * @return  mixed
     */
    public function removeGuide($guide)
    {
        return $this->guides()->detach($guide);
    }

    // public function assigned_user()
    // {
    //     return $this->belongsToMany('App\User');
    // }

    public function users()
    {
        return $this->belongsToMany('App\User');
    }
    public function author()
    {
        return $this->belongsTo('App\User', 'author');
    }
    
    public function isMyTour()
    {
        if ($this->author == Auth::id()) return true;
        
        foreach ($this->users()->get() as $usr) {
            if (Auth::id() === $usr->id) return true;
        }
        
        return false;
    }
    
    public function isUserTour($user)
    {
        if ($this->author == $user->id) return true;
        
        foreach ($this->users()->get() as $usr) {
            if ($user->id === $usr->id) return true;
        }
        
        return false;
    }
    
    
    public function getDrivers()
    {
        $transfersDriverId = [];
        $transfersDriverId = TransferToDrivers::where('tour_id', $this->id)->lists('id')->toArray();
        
        $drivers = Driver::whereIn('id', $transfersDriverId)->get();
        return $drivers;
    }

    public function city_begin()
    {
        return $this->belongsTo('App\City');
    }

    public function country_begin()
    {
        return $this->belongsTo('App\Country');
    }

    public function city_end()
    {
        return $this->belongsTo('App\City');
    }

    public function country_end()
    {
        return $this->belongsTo('App\Country');
    }

    public function getStatusName()
    {
        $status = Status::find($this->status);
        return $status ? $status->name : 'Unknown';
    }

    public function getStatusColor()
    {
        $status = Status::find($this->status);
        return $status ? $status->color : '#cccccc';
    }

    public function getRowBackgroundColor()
    {
        $statusName = $this->getStatusName();
        switch($statusName) {
            case 'Pending':
                return 'rgb(255, 249, 176)';
            case 'Cancelled':
                return '#ffbbb2';
            case 'Confirmed':
                return 'rgb(159, 255, 135)';
            default:
                return 'rgb(202, 255, 189)';
        }
    }

    public function getCityBeginAttribute($value)
    {
    	$city = City::find($value);
        return $city ? City::findOrFail($value)->name : '';
    }

    public function getCityEndAttribute($value)
    {
	    $city = City::find($value);
	    return $city ? City::findOrFail($value)->name : '';
    }

    // public function getCountryBeginName()
    // {
    //     return Country::where('alias', $this->country_begin)->first()->name;
    // }

    public function getCountryEndAttribute($value)
    {
    	$country = Country::where('alias', $value)->first();
        return $country ? $country->name : '';
    }

    public function getCountryBeginAttribute($value)
    {
	    $country = Country::where('alias', $value)->first();
	    return $country ? $country->name : '';
    }

    public function getPaxFreeAttribute($value)
    {
        return  $value ? "+{$value}" : 0;
    }

    public function showAllAssignedName()
    {
        $users = '';
        foreach ($this->users()->get() as $user) {
            $users .= "{$user->name} ";
        }
        return $users;
    }

    public function getTourDateByDate($date)
    {
        return $this->tour_days->first(function($value) use ($date){
            return $value->date == $date->format('Y-m-d');
        });
    }

	public function getTourDaysSortedByDate()
	{
		return $this->tour_days->sortBy('date');
	}

    public function getTotalAmountForTour(){
        $tourDays = $this->tour_days;

        $pricesTourPackage = collect();

        foreach ($tourDays as $tourDay){
            foreach ($tourDay->packages as $package){
                if($package->parrent_package_id == null){
                    $pricesTourPackage->push($package->getTotalAmountTourPackage());
                }
            }
        }

        $transfers_tour = $this->transfers;
        foreach ($transfers_tour as $item){
            $pricesTourPackage->push($item->getTotalAmountTourPackage());
        }

        $totalAmountForTour = $pricesTourPackage->sum();

        return round($totalAmountForTour);
    }


    public function getPriceForOnePaxInTour(){
        $total = 0;

        if($this->total_amount > 0){
            $total = $this->total_amount;
        }else{
            $total = $this->getTotalAmountForTour();
        }

        if($total == 0 || $this->pax == 0){
            return 0;
        }

        return $total / $this->pax;
    }

	public function quotations()
	{
		return $this->hasMany(Quotation::class);
	}

	public function getAllServices()
	{
		$result = collect([]);
		/** @var TourDay $tourDay */
		foreach ($this->tour_days as $tourDay) {
			$result = $result->merge($tourDay->packages);
		}

		return $result;
	}

	public function getHotels()
	{
		$services = $this->getAllServices();
		$hotels = $services->filter(function ($item) {
			return $item->type == 0; // HOTEL
		});
        $unique = $hotels->unique('name'); $unique->values()->all();

		return $unique;

	}
	public function transfers()
	{
		return $this->hasMany('App\TourPackage');
	}

	public function guestLists()
	{    
		return $this->hasMany(GuestList::class);
	}


	public function getResponsibleUser(){
        if(!$this->responsible) return null;
        $user = User::query()->where('id', $this->responsible)->first();
        if(!$user) return null;

        return $user;
    }

	public function tasks()
    {
        return $this->hasMany('App\Task', 'tour');
    }
	public function childrens()
    {
        return $this->hasMany('App\Childrens');
    }

    public function client()
    {
        return $this->belongsTo('App\Client', 'client_id');
    }
}
