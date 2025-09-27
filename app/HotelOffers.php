<?php

namespace App;


use Illuminate\Database\Eloquent\Model;

class HotelOffers extends Model
{
	    protected $table = 'hotel_offers';
protected $fillable = [
        'package_id','tour_id', 'status', 'option_date' ,
	'currency','city_tax','halfboard','foc_after_every_pax','halfboardMax','portrage_perperson','other_coditions','hotel_file','hotel_note','cancellationNote'
    ];
	
	public function offer_room_prices()
		{
			return $this->hasMany(OfferRoomPrices::class, 'offer_id','id');
		}
	public function cancellation_policies()
		{
			return $this->hasMany(OfferCancellationPolicies::class, 'offer_id','id');
		}
	public function cancellation_policiy()
		{
			return $this->hasOne(OfferCancellationPolicies::class, 'offer_id','id');
		}
	public function payment_policies()
		{
			return $this->hasMany(OfferPaymentPolicies::class, 'offer_id','id');
		}
	public function payment_policiy()
		{
			return $this->hasOne(OfferPaymentPolicies::class, 'offer_id','id');
		}
	

	public function offersWithRoomPrice($roomType)
		{
		 $roomPrices = $this->offer_room_prices
                ->where('room_type_id', $roomType->id);
		$price = 0;
	
		foreach($roomPrices as $room_price){
			
			$price = $room_price->price+ $price;
			
			
		}
		
			return $price; 
		}
	
	 public function getStatusName($id)
    {
        return Status::find($id)->name;
    }


}
