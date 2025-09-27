<?php

namespace App;


use Illuminate\Database\Eloquent\Model;

class TransDetail extends Model
{
	protected $table = 'trans_detail';
protected $fillable = [
        'trans_date', 'trans_id', 'account_id', 'account_no','account_desc','memo','amount',
    ];

    public function account()
{
    return $this->belongsTo(Account::class);
}
}
