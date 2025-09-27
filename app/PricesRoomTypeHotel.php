<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * App\PricesRoomTypeHotel
 *
 * @property int $id
 * @property int $price
 * @property int $room_type_id
 * @property int $hotel_id
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @property-read \App\RoomTypes $room_types
 * @method static \Illuminate\Database\Eloquent\Builder|\App\PricesRoomTypeHotel whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\PricesRoomTypeHotel whereHotelId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\PricesRoomTypeHotel whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\PricesRoomTypeHotel wherePrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\PricesRoomTypeHotel whereRoomTypeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\PricesRoomTypeHotel whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class PricesRoomTypeHotel extends Model
{
    protected $table = 'prices_room_type_hotels';

    public function room_types()
    {
        return $this->hasOne('App\RoomTypes', 'id', 'room_type_id');
    }
}
