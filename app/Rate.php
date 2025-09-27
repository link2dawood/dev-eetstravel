<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Session;
/**
 * App\Rate
 *
 * @property int $id
 * @property string $name
 * @property string $mark
 * @property string|null $rate_type
 * @property int $sort_order
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Rate whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Rate whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Rate whereMark($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Rate whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Rate whereRateType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Rate whereSortOrder($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Rate whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Rate extends Model
{
    protected $table = 'rates';
    
    public function hotels()
    {
        return $this->hasMany('App\Hotel', 'rate');
    }    

}
