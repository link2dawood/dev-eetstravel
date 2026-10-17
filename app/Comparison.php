<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Comparison extends Model
{
    //
	protected $guarded = [];

    // Missing rows are display placeholders only. Persist them through the
    // explicit, permission-protected initialization POST, never while viewing.
    public static function forDisplay(Quotation $quotation)
    {
        $comparison = static::find($quotation->id) ?: new static(['id' => $quotation->id]);
        $rows = $comparison->exists ? $comparison->comparison_rows : new \Illuminate\Database\Eloquent\Collection();
        foreach ($quotation->tour->tour_days as $day) {
            if (!$rows->firstWhere('date', $day->date)) {
                $rows->push(new ComparisonRow(['date' => $day->date, 'comparison_id' => $quotation->id]));
            }
        }
        $comparison->setRelation('comparison_rows', $rows);
        return $comparison;
    }

    public function needsInitialization()
    {
        return !$this->exists || $this->comparison_rows->contains(function ($row) { return !$row->exists; });
    }

	public function comparison_rows()
	{
		return $this->hasMany('App\ComparisonRow');
	}

	public function comparisonRowByDate($date)
	{
		return $this->comparison_rows->first(function ($row) use ($date) {
			return ($row->date == $date);
		});

	}

	public function isAllRoomingListReserved()
	{
		foreach ($this->comparison_rows as $row) {
			if (!$row->rooming_list_reserved) {
				return false;
			}
		}
		return true;
	}

	public function isAllVisasConfirmed()
	{
		foreach ($this->comparison_rows as $row) {
			if (!$row->visa_confirmation) {
				return false;
			}
		}
		return true;
	}
}
