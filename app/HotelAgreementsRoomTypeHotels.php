<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class HotelAgreementsRoomTypeHotels extends Model
{
    protected $table = 'hotel_agreements_room_type_hotels';

    public function room_types(){
        return $this->belongsTo('App\RoomTypes','room_type_id', 'id');
    }
}
