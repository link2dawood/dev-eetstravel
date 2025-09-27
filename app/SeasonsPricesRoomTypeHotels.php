<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SeasonsPricesRoomTypeHotels extends Model
{
    protected $table = 'seasons_prices_room_type_hotels';

    public function room_types(){
        return $this->belongsTo('App\RoomTypes','room_type_id', 'id');
    }

    public function getRoom($id){
        return \App\RoomTypes::find($id);
    }
}
