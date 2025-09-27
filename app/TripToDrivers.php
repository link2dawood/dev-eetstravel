<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Helper\Trackable;

class TripToDrivers extends Model
{
    use Trackable;
    protected $table = 'trip_to_drivers';
    protected $guarded = [];
}
