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

class Transactions extends Model
{
    
    public $timestamps = false;
    protected $table ="customer_transactions" ;
    protected $guarded = [];
}
