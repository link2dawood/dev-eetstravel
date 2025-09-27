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

class ClientInvoices extends Model
{
    
    public $timestamps = false;
    protected $table ="client_invoices" ;
    protected $guarded = [];
	
	public function client(){
    	return $this->hasOne(Client::class,'id','client_id');
	}
	public function status($transactions){
    	 $transaction = Transaction::where("invoice_id",$transactions->id)->where("pay_to","Client");
                $sum_amount = $transaction->sum("amount");
                $amount = $transactions->amount_receiveable ;
                $remaining_amount =  $amount - $sum_amount;
                if($sum_amount == $amount){
                    $result = "Paid";
                }
                elseif($sum_amount == 0){
                    $result = "They Owe ".$amount;
                }
                else{
                    $result = "They Owe ".$remaining_amount;
                }
               
                return $result;
	}
}
