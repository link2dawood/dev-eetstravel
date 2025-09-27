<?php

namespace App;


use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
protected $fillable = [
        'date', 'description', 'invoice_id', 'amount','trans_no','payment_method','invoice_no','pay_to',
    ];


}
