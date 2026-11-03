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
use Illuminate\Database\Eloquent\SoftDeletes;

class Invoices extends Model
{
    use SoftDeletes;
    
    public $timestamps = false;
    protected $table ="supplier_invoices" ;
    protected $guarded = [];
	 public function tours()
		{
			return $this->hasMany(InvoicesTours::class, 'invoices_id','id');
		}

    public function activeTours()
    {
        return $this->hasMany(InvoicesTours::class, 'invoices_id', 'id');
    }
	
    public function files()
    {
        return $this->hasMany('App\File');
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class, 'invoice_id', 'id');
    }

    public function office()
    {
        return $this->belongsTo(Offices::class, 'office_id');
    }
	public function getTotalAmountForMonth($month, $year)
    {
        return $this->whereMonth('date', $month)
                    ->whereYear('date', $year)
                    ->sum('total_amount');
    }
	public function status($invoices_tour)
    {
		$paid = (float) $this->transactions()->where('pay_to', 'Supplier')->sum('amount');
		$balance = (float) $this->total_amount + (float) $this->extra_amount - $paid;
		return $balance <= 0 ? 'Paid' : 'You Owe ' . number_format($balance, 2);
    }
}
