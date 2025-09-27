<?php

namespace App;
/**
 * App\Client
 *
 * @property int $id
 * @property string $tours
 * @property string $transaction_from
 * @property string $payment
 * @property string $total_amount
 * @property string $amount_payable
 * @property string $amount_recieveable
 * @property string $work_email
 * @property string $contact_email
 * @property string $work_fax
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @property \Carbon\Carbon|null $deleted_at
 
 */

use Illuminate\Database\Eloquent\Model;

class InvoicesTours extends Model
{
    
    public $timestamps = false;
    protected $table ="invoices_tours" ;
    protected $guarded = [];
	
	public function invoices()
    {
        return $this->belongsTo(Invoices::class);
    }
	
	public function package()
    {
        return $this->belongsTo(TourPackage::class);
    }
	public function tours()
    {
        return $this->belongsTo(Tour::class,"invoices_tours_id");
    }
}
