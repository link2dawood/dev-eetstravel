<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class OfferCancellationPolicies extends Model
{
    //
	protected $table ="offer_cancellation_policies" ;
	
	public function hotelOffer()
{
    return $this->belongsTo(HotelOffers::class, 'offer_id');
}
}
