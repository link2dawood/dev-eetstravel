<?php

namespace App;
/**
 * App\Client
 *
 * @property int $id
 * @property string $office_id
 * @property string $subject_of_balance
 * @property string $month
 * @property string $total_amount
 * @property string $due_date
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @property \Carbon\Carbon|null $deleted_at
 
 */

use Illuminate\Database\Eloquent\Model;

class Office_Balance extends Model
{
    public $timestamps = false;
    protected $table ="office_balance" ;
    protected $guarded = [];
}
