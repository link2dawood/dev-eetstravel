<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Seasons extends Model
{
    protected $table = 'seasons';

    protected $guarded = [];

    public function seasons_room_types(){
       return $this->HasMany('App\SeasonsPricesRoomTypeHotels', 'season_id');
    }

    public function getType($id){
        return \App\SeasonTypes::find($id);
    }

    public function getRoom($id){
        return \App\RoomTypes::find($id);
    }
}
