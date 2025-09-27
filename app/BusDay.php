<?php

namespace App;

use App\Helper\Trackable;
use Illuminate\Database\Eloquent\Model;

class BusDay extends Model
{
    use Trackable;

    protected $table = 'bus_days';
    protected $guarded = [];

    public function city_trip()
    {
        $city = City::query()->where('id', $this->city_trip)->first();
        if($city){
            return $city->name;
        }else{
            return '';
        }
    }

    public function country_trip()
    {
        $country = Country::query()->where('alias', $this->country_trip)->first();
        if($country){
            return $country->name;
        }else{
            return '';
        }
    }

    public function tour()
    {
    	return $this->belongsTo(Tour::class);
    }
}
