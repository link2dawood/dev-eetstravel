<?php

namespace App;

use App\Helper\Trackable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * App\Client
 *
 * @property int $id
 * @property string $first_name
 * @property string $country
 * @property string $city
 * @property string $address
 * @property string $work_phone
 * @property string $contact_phone
 * @property string $work_email
 * @property string $contact_email
 * @property string $work_fax
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @property \Carbon\Carbon|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\File[] $files
 * @method static bool|null forceDelete()
 * @method static \Illuminate\Database\Query\Builder|\App\Client onlyTrashed()
 * @method static bool|null restore()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Client whereAddress($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Client whereCity($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Client whereContactEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Client whereContactPhone($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Client whereCountry($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Client whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Client whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Client whereFirstName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Client whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Client whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Client whereWorkEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Client whereWorkFax($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Client whereWorkPhone($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Client withTrashed()
 * @method static \Illuminate\Database\Query\Builder|\App\Client withoutTrashed()
 * @mixin \Eloquent
 */
class Client extends Model
{
    use SoftDeletes;
    //use Trackable;

    protected $table = 'clients';
    protected $dates = ['deleted_at'];
    protected $guarded = [];


    public function files()
    {
        return $this->hasMany('App\File');
    }
}
