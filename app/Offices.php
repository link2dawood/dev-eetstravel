<?php

namespace App;
/**
 * App\Client
 *
 * @property int $id
 * @property string $office_name
 * @property string $office_address
 * @property int $tour_expenses
 * @property int $utility_expenses
 * @property int $total_expenses
 * @property int $earning
 * @property int $employee_salaries
 * @property int $balance_amount
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @property \Carbon\Carbon|null $deleted_at
 
 */

use Illuminate\Database\Eloquent\Model;

class Offices extends Model
{
    
    public $timestamps = false;
    protected $table ="office_fees" ;
    protected $guarded = [];
}
