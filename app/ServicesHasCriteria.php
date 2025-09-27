<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * App\ServicesHasCriteria
 *
 * @property int $id
 * @property int $service_id
 * @property int $criteria_id
 * @property int $service_type
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|\App\ServicesHasCriteria whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\ServicesHasCriteria whereCriteriaId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\ServicesHasCriteria whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\ServicesHasCriteria whereServiceId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\ServicesHasCriteria whereServiceType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\ServicesHasCriteria whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class ServicesHasCriteria extends Model
{
    public static $serviceTypes = [
        'hotel'         => 0,
        'event'         => 1,
        'guide'         => 2,
        'transfer'      => 3,
        'restaurant'    => 4,
        'tourPackage'   => 5,
        'cruise'        => 6,
        'flight'        => 7
    ];


    protected $table = 'services_has_criterias';
}
