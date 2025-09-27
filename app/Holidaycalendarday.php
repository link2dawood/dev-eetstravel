<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Holidaycalendarday extends Model
{
    protected $fillable = [ 'user_id',
                            'name',
                            'start_time',
                            'status',
                            'backgroundcolor' ];
            
}
