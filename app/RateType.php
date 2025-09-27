<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * App\RateType
 *
 * @property int $id
 * @property string $name
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|\App\RateType whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\RateType whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\RateType whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\RateType whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class RateType extends Model
{
    protected $table = 'rate_types';
}
