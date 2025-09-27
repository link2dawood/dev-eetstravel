<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ComparisonRow extends Model
{
    //
	protected $guarded = [];

	public function comparison()
	{
		return $this->belongsTo('App\Comparison');
	}

}
