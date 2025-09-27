<?php

namespace App;

use App\Helper\Trackable;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Driver
 *
 * @property int $id
 * @property string $name
 * @property string $phone
 * @property string $email
 * @property int|null $transfer_id
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @property-read \App\Transfer|null $transfer
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Driver whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Driver whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Driver whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Driver whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Driver wherePhone($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Driver whereTransferId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Driver whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Driver extends Model
{
    use Trackable;
    protected $guarded = [];

	/**
	 * The table associated with the model.
	 *
	 * @var string
	 */
	protected $table = 'drivers';


	public function transfer ()
	{
		return $this->belongsTo('App\Transfer');
	}

    public function files()
    {
        return $this->hasMany('App\File');
    }

}
