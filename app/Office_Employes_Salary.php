<?php

namespace App;
/**
 * App\Client
 *
 * @property int $id
 * @property string $office_id
 * @property string $subject_of_expense
 * @property string $month
 * @property string $total_expense_of_particular_month
 * @property string $total_amount_of_expense
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @property \Carbon\Carbon|null $deleted_at
 
 */







use Illuminate\Database\Eloquent\Model;

class Office_Employes_Salary extends Model
{
    public $timestamps = false;
    protected $table ="office_employes_salary" ;
    protected $guarded = [];
}
