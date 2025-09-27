<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Helper\HelperTrait;
class Email extends Model
{
    use HelperTrait;

    protected $dates = ['deleted_at'];


    protected $guarded = [];

	const TYPE_EETS = 1;
    const TYPE_VIANET = 2;


}
