<?php

namespace App;

use App\Helper\Trackable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * App\Quotation
 *
 * @property int $id
 * @property string $note
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Quotation whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Quotation whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Quotation whereNote($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Quotation whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class GuestList extends Model
{
    use SoftDeletes;
    protected $dates = ['deleted_at'];
	protected $guarded = [];
	protected $fillable = ['author_id', 'sent_at'];
    
    
	public function tour()
	{
		return $this->belongsTo(Tour::class);
	}
	
	public function getAuthor()
	{
		return User::find($this->author_id);
	}
	
	public function getSelectedHotelNames()
	{
        $hotels = [];
        $Ids = explode(',', $this->hotel_ids);
        foreach($Ids as $id){
            $tourPackage = TourPackage::find($id);
            if ($tourPackage){
                $hotels[] = TourPackage::find($id)->name;
            }
        }
		return $hotels;
	}

	public function getSelectedHotelNamesEmails()
	{
        $hotels = "";
        $Ids = explode(',', $this->hotel_ids);
        foreach($Ids as $id){
            $tourPackage = TourPackage::find($id);
            if ($tourPackage){
                $email = Hotel::find($tourPackage->reference)->work_email;
                $hotels .= $email . " - ". $tourPackage->name . "<br/>";
            }
        }
		return $hotels;
	}    
}
