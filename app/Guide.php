<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Helper\Trackable;
use App\Http\Controllers\DatatablesHelperController;

/**
 * Class Guide.
 *
 * @author The scaffold-interface created at 2017-04-12 12:55:18pm
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
 * @method static \Illuminate\Database\Query\Builder|\App\Guide whereAddressFirst($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Guide whereAddressSecond($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Guide whereCity($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Guide whereCode($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Guide whereComments($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Guide whereContactEmail($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Guide whereContactName($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Guide whereContactPhone($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Guide whereCountry($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Guide whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Guide whereDeletedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Guide whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Guide whereIntComments($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Guide whereName($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Guide whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Guide whereWebsite($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Guide whereWorkEmail($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Guide whereWorkFax($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Guide whereWorkPhone($value)
 * @property string|null $last_name
 * @property string|null $first_name
 * @property string|null $name_prefix
 * @property string|null $home_phone
 * @property string|null $cell_phone
 * @property string|null $data_cb
 * @property string|null $data_cd
 * @property string|null $data_code
 * @property string|null $data_ct
 * @property string|null $data_lmb
 * @property string|null $data_lmd
 * @property string|null $data_lmt
 * @property string|null $company
 * @property string|null $rate
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\File[] $files
 * @method static bool|null forceDelete()
 * @method static \Illuminate\Database\Query\Builder|\App\Guide onlyTrashed()
 * @method static bool|null restore()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Guide whereCellPhone($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Guide whereCompany($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Guide whereDataCb($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Guide whereDataCd($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Guide whereDataCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Guide whereDataCt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Guide whereDataLmb($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Guide whereDataLmd($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Guide whereDataLmt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Guide whereFirstName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Guide whereHomePhone($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Guide whereLastName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Guide whereNamePrefix($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Guide whereRate($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Guide withTrashed()
 * @method static \Illuminate\Database\Query\Builder|\App\Guide withoutTrashed()
 */
class Guide extends Model
{
    //use Trackable;
    use SoftDeletes;

    protected $dates = ['deleted_at'];


    protected $table = 'guides';

    protected $guarded = [];

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
        $url = array('show'       => route('guide.show', ['id' => $id]),
                     'edit'       => route('guide.edit', ['id' => $id]),
                     'delete_msg' => "/guide/{$id}/deleteMsg");
        return DatatablesHelperController::getActionButton($url, false, $this);
    }

    public function getCriterias(){
        $criterias = ServicesHasCriteria::where('service_id', $this->id)
            ->where('service_type', ServicesHasCriteria::$serviceTypes['guide'])
            ->get();

        return $this->criterias = $criterias;
    }

    public function getRate(){
        return $this->hasOne('App\Rate', 'id', 'rate');
    }
}
