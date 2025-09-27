<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Account extends Model
{
 
    public function transactions()
{
    return $this->hasMany(Transaction::class);
}
	public function transaction_detail()
{
    return $this->hasMany(TransDetail::class);
}
	public function totalAmount()
{
		$total_amount = $this->transaction_detail()->sum('amount');
 
      return $total_amount;
}
	
	public function getTotalAmountForDateRange($month,$year)
{
    $totalAmount = $this->transaction_detail()->whereMonth('trans_date', $month)->whereYear('trans_date', $year)->sum('amount');

    return $totalAmount;
}
}
