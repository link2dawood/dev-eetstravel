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

class Invoices extends Model
{
    
    public $timestamps = false;
    protected $table ="supplier_invoices" ;
    protected $guarded = [];
	 public function tours()
		{
			return $this->hasMany(InvoicesTours::class, 'invoices_id','id');
		}
	
    public function files()
    {
        return $this->hasMany('App\File');
    }
	public function getTotalAmountForMonth($month, $year)
    {
        return $this->whereMonth('date', $month)
                    ->whereYear('date', $year)
                    ->sum('total_amount');
    }
	public function status($invoices_tour)
    {
		
        $invoices = Invoices::find($invoices_tour->invoices_id);
        $transaction = Transaction::where("invoice_id",$invoices->id)->where("pay_to","Supplier");
		
                $sum_amount = $transaction->sum("amount");
                $amount = $invoices->total_amount ;
                $remaining_amount =  $amount - $sum_amount;
                if($sum_amount == $amount){
                    $result = "Paid";
                }
                elseif($sum_amount == 0){
                    $result = "You Owe ".$amount;
                }
                else{
                    $result = "You Owe ".$remaining_amount;
                }
               
                return $result;
    }
}
