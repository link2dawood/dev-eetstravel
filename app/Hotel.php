<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Helper\Trackable;
use App\Http\Controllers\DatatablesHelperController;

/**
 * App\Hotel
 *
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Event[] $events
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Tour[] $tours
 * @mixin \Eloquent
 * @property int $id
 * @property string $name
 * @property string $address_first
 * @property string $address_second
 * @property string $city
 * @property string $code
 * @property int $country
 * @property string $work_phone
 * @property string $work_fax
 * @property string $work_email
 * @property string $contact_name
 * @property string $contact_phone
 * @property string $contact_email
 * @property string $comments
 * @property string $note
 * @property string $int_comments
 * @property int $category
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property \Carbon\Carbon $deleted_at
 * @method static \Illuminate\Database\Query\Builder|\App\Hotel whereAddressFirst($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Hotel whereAddressSecond($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Hotel whereCategory($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Hotel whereCity($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Hotel whereCode($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Hotel whereComments($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Hotel whereContactEmail($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Hotel whereContactName($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Hotel whereContactPhone($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Hotel whereCountry($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Hotel whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Hotel whereDeletedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Hotel whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Hotel whereIntComments($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Hotel whereName($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Hotel whereNote($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Hotel whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Hotel whereWorkEmail($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Hotel whereWorkFax($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Hotel whereWorkPhone($value)
 * @property string|null $data_cb
 * @property string|null $data_cd
 * @property string|null $data_code
 * @property string|null $data_ct
 * @property string|null $data_lmb
 * @property string|null $data_lmd
 * @property string|null $data_lmt
 * @property string|null $data_hotelcat
 * @property string|null $website
 * @property string|null $rate
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\File[] $files
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\PricesRoomTypeHotel[] $prices_room_type
 * @method static bool|null forceDelete()
 * @method static \Illuminate\Database\Query\Builder|\App\Hotel onlyTrashed()
 * @method static bool|null restore()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Hotel whereDataCb($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Hotel whereDataCd($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Hotel whereDataCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Hotel whereDataCt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Hotel whereDataHotelcat($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Hotel whereDataLmb($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Hotel whereDataLmd($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Hotel whereDataLmt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Hotel whereRate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Hotel whereWebsite($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Hotel withTrashed()
 * @method static \Illuminate\Database\Query\Builder|\App\Hotel withoutTrashed()
 */
class Hotel extends Model
{
//    use SoftDeletes;
//    use Trackable;
    
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'hotels';

    protected $dates = ['deleted_at'];

    protected $guarded = [];

    /**
     * event.
     *
     * @return  \Illuminate\Support\Collection;
     */
    public function events()
    {
        return $this->belongsToMany('App\Event');
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
     * tour.
     *
     * @return  \Illuminate\Support\Collection;
     */
    public function tours()
    {
        return $this->belongsToMany('App\Tour');
    }
    
    /**
     * Assign a tour.
     *
     * @param  $tour
     * @return  mixed
     */
    public function assignTour($tour)
    {
        return $this->tours()->attach($tour);
    }

    /**
     * Remove a tour.
     *
     * @param  $tour
     * @return  mixed
     */
    public function removeTour($tour)
    {
        return $this->tours()->detach($tour);
    }

    public function files()
    {
        return $this->hasMany('App\File');
    }

    public function country()
    {
        return $this->hasMany('App\Country');
    }

	public function cityObject()
	{
		return $this->belongsTo('App\City', 'city', 'id');
	}


    public function getButton($id)
    {
        $url = array('show'       => route('hotel.show', ['id' => $id]),
                     'edit'       => route('hotel.edit', ['id' => $id]),
                     'delete_msg' => "/hotel/{$id}/deleteMsg");
        return DatatablesHelperController::getActionButton($url, false, $this);
    }

    public function getCriterias(){
        $criterias = ServicesHasCriteria::where('service_id', $this->id)
            ->where('service_type', ServicesHasCriteria::$serviceTypes['hotel'])
            ->get();

        return $this->criterias = $criterias;
    }

    public function prices_room_type(){
        return $this->hasMany('App\PricesRoomTypeHotel');
    }

    public function agreements(){
        return $this->hasMany('App\HotelAgreements');
    }

    public function menus()
    {
		return $this->hasMany('App\Menu');
    }

    public function seasons(){
        return $this->hasMany('App\Seasons');
    }
    
}
