<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * App\RoomTypes
 *
 * @property int $id
 * @property string $name
 * @property string $code
 * @property int $sort_order
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|\App\RoomTypes whereCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\RoomTypes whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\RoomTypes whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\RoomTypes whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\RoomTypes whereSortOrder($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\RoomTypes whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class RoomTypes extends Model
{
    protected $table = 'room_types';
}
