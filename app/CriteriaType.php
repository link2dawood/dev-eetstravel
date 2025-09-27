<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * App\CriteriaType
 *
 * @property int $id
 * @property string $name
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @property string $service_type
 * @method static \Illuminate\Database\Eloquent\Builder|\App\CriteriaType whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\CriteriaType whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\CriteriaType whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\CriteriaType whereServiceType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\CriteriaType whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class CriteriaType extends Model
{
    protected $table = 'criteria_types';
}
