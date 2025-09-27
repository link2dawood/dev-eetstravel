<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Helper\Trackable;

/**
 * App\Flight
 *
 * @property int $id
 * @property string $name
 * @property string|null $date_from
 * @property string|null $date_to
 * @property \App\City $city_to
 * @property \App\City $city_from
 * @property \App\Country $country_from
 * @property \App\Country $country_to
 * @property string|null $address_first
 * @property string|null $address_second
 * @property string|null $code
 * @property string|null $work_phone
 * @property string|null $work_fax
 * @property string|null $work_email
 * @property string|null $contact_name
 * @property string|null $contact_phone
 * @property string|null $contact_email
 * @property string|null $comments
 * @property string|null $int_comments
 * @property string|null $data_cb
 * @property string|null $data_cd
 * @property string|null $data_code
 * @property string|null $data_ct
 * @property string|null $data_lmb
 * @property string|null $data_lmd
 * @property string|null $data_lmt
 * @property string|null $website
 * @property string|null $company
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @property string|null $rate
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\File[] $files
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Flight whereAddressFirst($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Flight whereAddressSecond($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Flight whereCityFrom($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Flight whereCityTo($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Flight whereCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Flight whereComments($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Flight whereCompany($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Flight whereContactEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Flight whereContactName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Flight whereContactPhone($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Flight whereCountryFrom($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Flight whereCountryTo($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Flight whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Flight whereDataCb($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Flight whereDataCd($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Flight whereDataCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Flight whereDataCt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Flight whereDataLmb($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Flight whereDataLmd($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Flight whereDataLmt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Flight whereDateFrom($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Flight whereDateTo($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Flight whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Flight whereIntComments($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Flight whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Flight whereRate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Flight whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Flight whereWebsite($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Flight whereWorkEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Flight whereWorkFax($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Flight whereWorkPhone($value)
 * @mixin \Eloquent
 */
class Flight extends Model
{
    use Trackable;
    
    protected $guarded = [];

    public function files()
    {
        return $this->hasMany('App\File');
    }

    public function city_from()
    {
        return $this->hasOne('App\City');
    }

    public function country_from()
    {
        return $this->hasOne('App\Country');
    }

    public function city_to()
    {
        return $this->hasOne('App\City');
    }

    public function country_to()
    {
        return $this->hasOne('App\Country');
    }

    public function getCriterias(){
        $criterias = ServicesHasCriteria::where('service_id', $this->id)
            ->where('service_type', ServicesHasCriteria::$serviceTypes['flight'])
            ->get();

        return $this->criterias = $criterias;
    }

    public function getRate(){
        return $this->hasOne('App\Rate', 'id', 'rate');
    }
}
