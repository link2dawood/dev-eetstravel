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
    /**
     * AUDIT.md CC5 — block mass-assignment of identity / FK columns.
     * Amount / status / business-data fields stay editable so existing
     * update flows (Model::update($request->except(['attach']))) still work,
     * but the FK and identity columns can't be reassigned via a tampered
     * payload.
     */
    protected $guarded = ['id', 'created_at', 'updated_at', 'invoice_id', 'trans_no'];
}
