<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PackageMenu extends Model
{
	protected $guarded = [];

	public function package() {
		return $this->belongsTo(TourPackage::class, 'tour_package_id');
	}

	public function menu() {
		return $this->belongsTo(Menu::class);
	}
}
