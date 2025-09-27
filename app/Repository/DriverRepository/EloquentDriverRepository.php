<?php
/**
 * Created by PhpStorm.
 * User: alex
 * Date: 29.11.17
 * Time: 12:28
 */

namespace app\Repository\DriverRepository;


use App\Driver;
use App\Repository\Contracts\DriverRepository;

class EloquentDriverRepository implements DriverRepository
{

    public function all(){
        return Driver::all();
    }

    public function byId(int $id){
        return Driver::query()->findOrFail($id);
    }

    public function create(array $data){
        return Driver::query()->create($data);
    }

    public function updateById(int $id, array $data){
        return Driver::query()->findOrFail($id)->update($data);
    }

    public function deleteById(int $id){
        return Driver::query()->findOrFail($id)->delete();
    }
}