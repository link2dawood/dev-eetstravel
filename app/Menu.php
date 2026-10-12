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
			return route('restaurant.show', $this->restaurant_id);
		}
		if ($this->hotel_id) {
			return route('hotel.show', ['hotel' => $this->hotel_id]);
		}

		return '#';
	}

	/**
	 * Description as display HTML: the rich-text editor's formatting is kept, but scripts,
	 * styles and attributes (onclick=..., editor internals) are dropped before output.
	 */
	public function safeDescription()
	{
		$html = preg_replace('#<(script|style|iframe|object|embed)\b[^>]*>.*?</\1>#is', '', (string) $this->description);
		$html = strip_tags($html, '<p><br><b><strong><i><em><u><ul><ol><li><h3><h4><h5><span><div>');
		return preg_replace('#<(\w+)\s[^>]*>#', '<$1>', $html);
	}

	public function packageMenu() {
		return $this->hasMany(PackageMenu::class);
	}

}
