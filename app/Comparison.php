<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Comparison extends Model
{
    //
	protected $guarded = [];

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