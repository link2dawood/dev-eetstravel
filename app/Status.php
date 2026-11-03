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
    use \Illuminate\Database\Eloquent\Factories\HasFactory;
    protected $table = 'status';

    protected $fillable = ['name', 'type', 'color', 'sort_order', 'status', 'is_completed', 'is_aborted'];

    // These identifiers are used by existing tour, task, hotel and bus workflows.
    const SYSTEM_IDS = [1, 2, 4, 6, 7, 8, 9, 10, 17, 23, 34, 39, 46];

    public function isSystem(): bool
    {
        return in_array((int) $this->id, self::SYSTEM_IDS, true);
    }

    public function isInUse(): bool
    {
        $usage = [
            'tour' => [['tours', 'status']], 'task' => [['tasks', 'status']],
            'hotel' => [['tour_packages', 'status']], 'service_in_tour' => [['tour_packages', 'status']],
            'bus' => [['bus_days', 'status_id'], ['tour_packages', 'status']],
            'offer' => [['hotel_offers', 'tms_status']],
        ];
        foreach ($usage[$this->type] ?? [] as [$table, $column]) {
            if (\Illuminate\Support\Facades\DB::table($table)->where($column, $this->id)->exists()) return true;
        }
        return $this->type === 'offer' && \Illuminate\Support\Facades\DB::table('hotel_offers')->where('status', $this->name)->exists();
    }

    public function getColorAttribute($value)
    {
        // Existing invalid values must not become CSS declarations on other screens.
        return is_string($value) && preg_match('/^#(?:[0-9a-f]{3}|[0-9a-f]{6}|[0-9a-f]{8})$/i', $value) ? $value : null;
    }

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
