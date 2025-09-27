<?php

namespace App;

use App\Helper\Trackable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Bus extends Model
{
    use SoftDeletes;
    use Trackable;

    protected $table = 'buses';
    protected $dates = ['deleted_at'];
    protected $guarded = [];


    public function driver()
    {
        return $this->hasOne('App\Driver', 'id', 'driver_id');
    }


    public function files()
    {
        return $this->hasMany('App\File');
    }

    public function transfer ()
    {
        return $this->belongsTo('App\Transfer');
    }
}
