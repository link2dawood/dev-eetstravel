<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Helper\Trackable;
use App\Http\Controllers\DatatablesHelperController;

/**
 * Class Transfer.
 *
 * @author The scaffold-interface created at 2017-04-12 01:02:32pm
 * @link https://github.com/amranidev/scaffold-interface
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
 * @property string $comments
 * @property string $int_comments
 * @property string $contact_name
 * @property string $contact_phone
 * @property string $contact_email
 * @property string $website
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property \Carbon\Carbon $deleted_at
 * @method static \Illuminate\Database\Query\Builder|\App\Transfer whereAddressFirst($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Transfer whereAddressSecond($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Transfer whereCity($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Transfer whereCode($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Transfer whereComments($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Transfer whereContactEmail($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Transfer whereContactName($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Transfer whereContactPhone($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Transfer whereCountry($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Transfer whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Transfer whereDeletedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Transfer whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Transfer whereIntComments($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Transfer whereName($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Transfer whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Transfer whereWebsite($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Transfer whereWorkEmail($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Transfer whereWorkFax($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Transfer whereWorkPhone($value)
 * @property string|null $data_cb
 * @property string|null $data_cd
 * @property string|null $data_code
 * @property string|null $data_ct
 * @property string|null $data_lmb
 * @property string|null $data_lmd
 * @property string|null $data_lmt
 * @property string|null $rate
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Driver[] $drivers
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\File[] $files
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Tour[] $tours
 * @method static bool|null forceDelete()
 * @method static \Illuminate\Database\Query\Builder|\App\Transfer onlyTrashed()
 * @method static bool|null restore()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Transfer whereDataCb($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Transfer whereDataCd($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Transfer whereDataCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Transfer whereDataCt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Transfer whereDataLmb($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Transfer whereDataLmd($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Transfer whereDataLmt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Transfer whereRate($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Transfer withTrashed()
 * @method static \Illuminate\Database\Query\Builder|\App\Transfer withoutTrashed()
 */
class Transfer extends Model
{
    use Trackable;
    use SoftDeletes;

    protected $dates = ['deleted_at'];

    protected $table = 'transfers';

    protected $guarded = [];

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

	public function tours(){
		return $this->hasMany('App\Tour', 'status');
	}

    public function getButton($id)
    {
        $url = array('show'       => route('transfer.show', ['id' => $id]),
                     'edit'       => route('transfer.edit', ['id' => $id]),
                     'delete_msg' => "/transfer/{$id}/deleteMsg");
        return DatatablesHelperController::getActionButton($url, false, $this);
    }

    public function getCriterias(){
        $criterias = ServicesHasCriteria::where('service_id', $this->id)
            ->where('service_type', ServicesHasCriteria::$serviceTypes['transfer'])
            ->get();

        return $this->criterias = $criterias;
    }

	public function drivers()
	{
		return $this->hasMany('App\Driver');
	}

    public function buses()
    {
        return $this->hasMany('App\Bus');
    }


    public function getRate(){
        return $this->hasOne('App\Rate', 'id', 'rate');
    }
}
