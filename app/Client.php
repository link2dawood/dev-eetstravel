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
    use \Illuminate\Database\Eloquent\Factories\HasFactory;
    use SoftDeletes;
    //use Trackable;

    protected $table = 'clients';
    protected $hidden = ['password'];
    protected $dates = ['deleted_at'];
    protected $fillable = ['name', 'country', 'city', 'address', 'account_no', 'company_address', 'invoice_address', 'work_phone', 'contact_phone', 'work_email', 'contact_email', 'work_fax'];

    public function setPasswordAttribute($value)
    {
        if ($value === null || $value === '') { $this->attributes['password'] = null; return; }
        $this->attributes['password'] = password_get_info((string)$value)['algo'] ? $value : \App\Services\ClientPortalPassword::hash($value);
    }

    public function isInUse(): bool
    {
        return \Illuminate\Support\Facades\DB::table('tours')->where('client_id', $this->id)->exists()
            || \Illuminate\Support\Facades\DB::table('client_invoices')->where('client_id', $this->id)->exists();
    }


    public function tours()
    {
        return $this->hasMany(Tour::class, 'client_id');
    }

    public function files()
    {
        return $this->hasMany('App\File');
    }
}
