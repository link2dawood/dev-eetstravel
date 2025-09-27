<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * App\TourDay
 *
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\TourPackage[] $packages
 * @mixin \Eloquent
 * @property int $id
 * @property string $date
 * @property int $tour
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property \Carbon\Carbon $deleted_at
 * @method static \Illuminate\Database\Query\Builder|\App\TourDay whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\TourDay whereDate($value)
 * @method static \Illuminate\Database\Query\Builder|\App\TourDay whereDeletedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\TourDay whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\TourDay whereTour($value)
 * @method static \Illuminate\Database\Query\Builder|\App\TourDay whereUpdatedAt($value)
 */
class TourDay extends Model
{
    use SoftDeletes;

    protected $dates = ['deleted_at'];


    protected $table = 'tour_days';

	public function tour()
	{
		return $this->belongsTo('App\Tour', 'tour');
	}


	/**
     * package.
     *
     * @return  \Illuminate\Support\Collection;
     */
    public function packages()
    {
        return $this->belongsToMany('App\TourPackage', 'packages_tour_days')->orderBy('time_from');
    }

    /**
     * Assign a Package.
     *
     * @param  $package
     * @return  mixed
     */
    public function assignPackage($package)
    {
        return $this->packages()->attach($package);
    }

    /**
     * Remove a package.
     *
     * @param  $package
     * @return  mixed
     */
    public function removePackage($package)
    {
        return $this->packages()->detach($package);
    }

    public function firstHotel()
    {
    	$firstHotel = $this->packages->first(
		    function(TourPackage $package){
			    if ($package) {
				    return $package->main_hotel == true;
			    }
			    return null;
		    });

    	if ($firstHotel) {
    		return $firstHotel;
	    }

    	return $this->packages->first(
    		function(TourPackage $package){
    			if ($package->service()) {
				    return $package->service()->service_type == 'Hotel';
			    }
			    return false;
		    }
	    );
    }

    public function firstRestaurant()
    {
	    return $this->packages->first(
		    function(TourPackage $package){
			    if ($package->service()) {
			    	$packageTime = Carbon::parse($package->time_from);
				    return ($package->service()->service_type == 'Restaurant' ) && ($packageTime->hour >= 0) && ($packageTime->hour < 15);
			    }
			    return false;
		    }
	    );
    }

	public function secondRestaurant()
	{

		return  $this->packages->filter(
			function(TourPackage $package){
				if ($package->service()) {
					$packageTime = Carbon::parse($package->time_from);
					return ($package->service()->service_type == 'Restaurant' ) && ($packageTime->hour >= 15) && ($packageTime->hour <= 24);
				}
				return false;
			}
		)->values()->get(0);
	}

	public function setMainHotel($packageId)
	{
		$hotels = $this->packages->filter(
			function(TourPackage $package){
				if ($package->service()) {
					return $package->service()->service_type == 'Hotel';
				}
				return false;
			}
		);
		/** @var TourPackage $hotel */
		foreach ($hotels as $hotel) {
			$hotel->main_hotel = false;
			$hotel->save();
		}
		$activeHotel = TourPackage::find($packageId);
		$activeHotel->main_hotel = true;
		return $activeHotel->save();

	}
	public function Hotels()
	{
		$hotels = $this->packages->filter(
			function(TourPackage $package){
				if ($package->service()) {
					return $package->service()->service_type == 'Hotel';
				}
				return false;
			}
		);
		
		return $hotels;
	}
}
