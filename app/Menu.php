<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Menu extends Model
{
	protected $guarded = [];

	public function menu_meals()
	{
		return $this->hasMany('App\MenuMeal');
	}

	public function restaurant()
	{
		return $this->belongsTo('App\Restaurant');
	}

	public function hotel()
	{
		return $this->belongsTo('App\Hotel');
	}

	public function getParentRoute()
	{
		if ($this->restaurant_id) {
			return route('restaurant.show', ['id' => $this->restaurant_id]);
		}
		if ($this->hotel_id) {
			return route('hotel.show', ['id' => $this->hotel_id]);
		}

		return '#';
	}

	public function packageMenu() {
		return $this->hasMany(PackageMenu::class);
	}

}
