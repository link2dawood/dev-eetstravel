<?php

namespace App;

use App\Helper\PermissionHelper;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Helper\Trackable;
use App\Http\Controllers\DatatablesHelperController;
/**
 * Class Event.
 *
 * @author The scaffold-interface created at 2017-04-12 12:45:46pm
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
 * @method static \Illuminate\Database\Query\Builder|\App\Event whereAddressFirst($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Event whereAddressSecond($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Event whereCity($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Event whereCode($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Event whereComments($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Event whereContactEmail($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Event whereContactName($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Event whereContactPhone($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Event whereCountry($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Event whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Event whereDeletedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Event whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Event whereIntComments($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Event whereName($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Event whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Event whereWebsite($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Event whereWorkEmail($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Event whereWorkFax($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Event whereWorkPhone($value)
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
 * @method static \Illuminate\Database\Query\Builder|\App\Event onlyTrashed()
 * @method static bool|null restore()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Event whereDataCb($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Event whereDataCd($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Event whereDataCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Event whereDataCt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Event whereDataLmb($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Event whereDataLmd($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Event whereDataLmt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Event whereRate($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Event withTrashed()
 * @method static \Illuminate\Database\Query\Builder|\App\Event withoutTrashed()
 */
class Event extends Model
{
    use SoftDeletes;
   // use Trackable;
    
    protected $dates = ['deleted_at'];
    protected $guarded = [];

    protected $table = 'events';

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

    public function city()
    {
        return $this->hasOne('App\City');
    }

    public function country()
    {
        return $this->hasOne('App\Country');
    }


    public function files()
    {
        return $this->hasMany('App\File');
    }

    public function getButton($id)
    {
        $url = ['show'       => route('event.show', ['id' => $id]),
                'edit'       => route('event.edit', ['id' => $id]),
                'delete_msg' => "/event/{$id}/deleteMsg"];

        return DatatablesHelperController::getActionButton($url, false, $this);
    }

    public function getCriterias(){
        $criterias = ServicesHasCriteria::where('service_id', $this->id)
            ->where('service_type', ServicesHasCriteria::$serviceTypes['event'])
            ->get();

        return $this->criterias = $criterias;
    }

    public function getRate(){
        return $this->hasOne('App\Rate', 'id', 'rate');
    }

}
