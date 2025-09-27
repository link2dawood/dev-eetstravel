<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * App\GooglePlaces
 *
 * @property int $id
 * @property string $place_id
 * @property string $type
 * @property int $service_id
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|\App\GooglePlaces whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\GooglePlaces whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\GooglePlaces wherePlaceId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\GooglePlaces whereServiceId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\GooglePlaces whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\GooglePlaces whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class GooglePlaces extends Model
{
    public static $services = [
        'tour' => '1',
        'task' => '2',
        'event' => '3',
        'restaurant' => '4',
        'hotel' => '5',
        'guide' => '6',
        'transfer' => '7',
        'flight' => '8',
        'cruise' => '9',
        'cruises' => '9',
        'tour_package' => '10',
        'status' => '11',
        'room_types' => '12',
        'rate' => '13',
        'currency_rate' => '14',
        'currencies' => '15',
        'criteria' => '16',
    ];


    protected $table = 'google_places';
}
