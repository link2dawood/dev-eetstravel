<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * App\StatusType
 *
 * @property int $id
 * @property string $name
 * @property string|null $type
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|\App\StatusType whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\StatusType whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\StatusType whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\StatusType whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\StatusType whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class StatusType extends Model
{
    protected $table = 'status_types';
}
