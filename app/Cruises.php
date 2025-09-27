<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Helper\Trackable;

/**
 * App\Cruises
 *
 * @property int $id
 * @property string $name
 * @property string|null $date_from
 * @property string|null $date_to
 * @property string|null $city_to
 * @property string|null $city_from
 * @property string|null $country_from
 * @property string|null $country_to
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
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Cruises whereAddressFirst($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Cruises whereAddressSecond($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Cruises whereCityFrom($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Cruises whereCityTo($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Cruises whereCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Cruises whereComments($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Cruises whereCompany($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Cruises whereContactEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Cruises whereContactName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Cruises whereContactPhone($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Cruises whereCountryFrom($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Cruises whereCountryTo($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Cruises whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Cruises whereDataCb($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Cruises whereDataCd($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Cruises whereDataCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Cruises whereDataCt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Cruises whereDataLmb($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Cruises whereDataLmd($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Cruises whereDataLmt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Cruises whereDateFrom($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Cruises whereDateTo($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Cruises whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Cruises whereIntComments($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Cruises whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Cruises whereRate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Cruises whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Cruises whereWebsite($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Cruises whereWorkEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Cruises whereWorkFax($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Cruises whereWorkPhone($value)
 * @mixin \Eloquent
 */
class Cruises extends Model
{
	//use Trackable;

    protected $guarded = [];

    public function files()
    {
        return $this->hasMany('App\File');
    }

    public function getCriterias(){
        $criterias = ServicesHasCriteria::where('service_id', $this->id)
            ->where('service_type', ServicesHasCriteria::$serviceTypes['cruise'])
            ->get();

        return $this->criterias = $criterias;
    }

    public function getRate(){
        return $this->hasOne('App\Rate', 'id', 'rate');
    }
}
