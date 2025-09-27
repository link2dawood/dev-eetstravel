<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TourRoomTypeHotel extends Model
{
    protected $table = 'tour_room_type_hotels';

    public function room_types()
    {
        return $this->hasOne('App\RoomTypes', 'id', 'room_type_id');
    }
	
	public function tour()
	{
        return $this->hasOne('App\Tour', 'id', 'tour_id');
    }
}
