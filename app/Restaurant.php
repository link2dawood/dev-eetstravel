<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Helper\Trackable;
use App\Http\Controllers\DatatablesHelperController;

/**
 * Class Restaurant.
 *
 * @author The scaffold-interface created at 2017-04-12 12:59:13pm
 * @link https://github.com/amranidev/scaffold-interface
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Tour[] $tours
 * @mixin \Eloquent
 * @property int $id
 * @property string $name
 * @property string $address_first
 * @property string $address_second
 * @property string $city
 * @property string $code
 * @property string $country
 * @property string $work_phone
 * @property string $work_fax
 * @property string $work_email
 * @property string $contact_name
 * @property string $contact_phone
 * @property string $contact_email
 * @property string $comments
 * @property string $int_comments
 * @property string $website
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property \Carbon\Carbon $deleted_at
 * @method static \Illuminate\Database\Query\Builder|\App\Restaurant whereAddressFirst($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Restaurant whereAddressSecond($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Restaurant whereCity($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Restaurant whereCode($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Restaurant whereComments($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Restaurant whereContactEmail($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Restaurant whereContactName($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Restaurant whereContactPhone($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Restaurant whereCountry($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Restaurant whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Restaurant whereDeletedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Restaurant whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Restaurant whereIntComments($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Restaurant whereName($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Restaurant whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Restaurant whereWebsite($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Restaurant whereWorkEmail($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Restaurant whereWorkFax($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Restaurant whereWorkPhone($value)
 * @property string|null $data_cb
 * @property string|null $data_cd
 * @property string|null $data_code
 * @property string|null $data_ct
 * @property string|null $data_lmb
 * @property string|null $data_lmd
 * @property string|null $data_lmt
 * @property string|null $rate
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\File[] $files
 * @method static bool|null forceDelete()
 * @method static \Illuminate\Database\Query\Builder|\App\Restaurant onlyTrashed()
 * @method static bool|null restore()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Restaurant whereDataCb($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Restaurant whereDataCd($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Restaurant whereDataCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Restaurant whereDataCt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Restaurant whereDataLmb($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Restaurant whereDataLmd($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Restaurant whereDataLmt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Restaurant whereRate($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Restaurant withTrashed()
 * @method static \Illuminate\Database\Query\Builder|\App\Restaurant withoutTrashed()
 */
class Restaurant extends Model
{
    use SoftDeletes;
   // use Trackable;
    
    protected $dates = ['deleted_at'];
    protected $guarded = [];
    protected $table = 'restaurants';

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

    public function city()
    {
        return $this->hasOne('App\City');
    }

    public function country()
    {
        return $this->hasOne('App\Country');
    }

    public function getButton($id)
    {
        $url = array('show'       => route('restaurant.show', ['id' => $id]),
                     'edit'       => route('restaurant.edit', ['id' => $id]),
                     'delete_msg' => "/restaurant/{$id}/deleteMsg");
        return DatatablesHelperController::getActionButton($url, false, $this);
    }


    public function getCriterias(){
        $criterias = ServicesHasCriteria::where('service_id', $this->id)
            ->where('service_type', ServicesHasCriteria::$serviceTypes['restaurant'])
            ->get();

        return $this->criterias = $criterias;
    }

    public function getRate(){
        return $this->hasOne('App\Rate', 'id', 'rate');
    }

	public function menus()
	{
		return $this->hasMany('App\Menu');
	}

}
