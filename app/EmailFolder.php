<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Helper\HelperTrait;
class EmailFolder extends Model
{
    use HelperTrait;

    protected $dates = ['deleted_at'];


    protected $guarded = [];

   
}
