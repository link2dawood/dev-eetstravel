<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class OfferCancellationPolicies extends Model
{
    //
	use \App\Helper\FormatsDecimalAmounts;

	protected $table ="offer_cancellation_policies" ;

	public function getCancellationPercentageAttribute($value) { return $this->formatAmount($value); }

	public function hotelOffer()
{
    return $this->belongsTo(HotelOffers::class, 'offer_id');
}
}
