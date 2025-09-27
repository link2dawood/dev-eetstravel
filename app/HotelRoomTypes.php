<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * App\HotelRoomTypes
 *
 * @property int $id
 * @property int $count
 * @property int $room_type_id
 * @property int $tour_package_id
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @property int $price
 * @property-read \App\RoomTypes $room_types
 * @method static \Illuminate\Database\Eloquent\Builder|\App\HotelRoomTypes whereCount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\HotelRoomTypes whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\HotelRoomTypes whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\HotelRoomTypes wherePrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\HotelRoomTypes whereRoomTypeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\HotelRoomTypes whereTourPackageId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\HotelRoomTypes whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class HotelRoomTypes extends Model
{
    protected $table = 'hotel_room_types';


    public function room_types(){
        return $this->hasOne('App\RoomTypes', 'id', 'room_type_id');
    }

}
