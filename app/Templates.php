<?php

namespace App;

use App\Helper\HelperTrait;
use App\Helper\Trackable;
use Illuminate\Database\Eloquent\Model;

class Templates extends Model
{
    use Trackable;
    use HelperTrait;

    protected $guarded = [];

    protected $table = 'templates';

    public function getServiceName(){
        $service = $this->servicesTypes[$this->service_id];
        return $service;
    }

    public function getTourName(){
        $tour = Tour::query()->where('id', $this->name)->first();
        if($tour){
            return $tour->name;
        }else{
            return 'Tour deleted';
        }
    }
}
