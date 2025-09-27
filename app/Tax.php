<?php

namespace App;

//use App\Helper\Trackable;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Tax extends Model
{

    //use Trackable;

    public $timestamps = false;
    protected $table ="invoice_taxes" ;
    protected $guarded = [];
}
