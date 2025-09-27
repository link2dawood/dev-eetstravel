<?php
/**
 * Created by PhpStorm.
 * User: alex
 * Date: 27.10.17
 * Time: 15:33
 */

namespace App\Repository\BusRepository;


use App\Bus;
use App\Repository\Contracts\BusRepository;

class EloquentBusRepository implements BusRepository
{
    public function all(){
        return Bus::all();
    }

    public function byId(int $id){
        return Bus::query()->findOrFail($id);
    }

    public function create(array $data){
        return Bus::query()->create($data);
    }

    public function updateById(int $id, array $data){
        return Bus::query()->findOrFail($id)->update($data);
    }

    public function deleteById(int $id){
        return Bus::query()->findOrFail($id)->delete();
    }
}