<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;


/**
 * App\Status
 *
 * @property int $id
 * @property string $name
 * @property string|null $type
 * @property string|null $color
 * @property int $sort_order
 * @property int $status
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Task[] $tasks
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\TourPackage[] $tour_packages
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Tour[] $tours
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Status whereColor($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Status whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Status whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Status whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Status whereSortOrder($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Status whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Status whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Status whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Status extends Model
{
    protected $table = 'status';

    // Sorting By default
	protected static function boot()
	{
		parent::boot();

		// Order by sort_order ASC
		static::addGlobalScope('order', function (Builder $builder) {
			$builder->orderBy('sort_order', 'asc');
		});
	}

	
    public function tours(){
        return $this->hasMany('App\Tour', 'status');
    }

    public function tasks(){
        return $this->hasMany('App\Task', 'status');
    }

    public function tour_packages(){
        return $this->hasMany('App\TourPackage', 'status');
    }
}
