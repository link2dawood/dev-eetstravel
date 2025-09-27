<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class HotelAgreements extends Model
{
    protected $table = 'hotel_agreements';

    public function agreements_room_types(){
       return $this->HasMany('App\HotelAgreementsRoomTypeHotels', 'agreement_id');
    }

    public function getRoom($id){
        return \App\RoomTypes::find($id);
    }
}
