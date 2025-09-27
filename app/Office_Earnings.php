<?php

namespace App;
/**
 * App\Client
 *
 * @property int $id
 * @property string $office_id
 * @property string $month
 * @property string $total_earning_amount
 * @property string $total_amount_after_removing_all_expense
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @property \Carbon\Carbon|null $deleted_at
 
 */




use Illuminate\Database\Eloquent\Model;

class Office_Earnings extends Model
{
    public $timestamps = false;
    protected $table ="office_earnings" ;
    protected $guarded = [];
}
