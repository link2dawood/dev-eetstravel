<?php

namespace App;


use Illuminate\Database\Eloquent\Model;

class OfferRoomPrices extends Model
{
	use \App\Helper\FormatsDecimalAmounts;

	    protected $table = 'offer_room_prices';

	public function getPriceAttribute($value) { return $this->formatAmount($value); }

}
