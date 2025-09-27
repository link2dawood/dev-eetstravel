<?php

namespace App\Repository\HotelRepository;

use App\Hotel;
use App\Repository\Contracts\HotelRepository;

/**
 * @author: yurapif
 * Date: 08.05.2017
 */
class EloquentHotelRepository implements HotelRepository{
    public function all(){
        return Hotel::all();
    }

    public function byId(int $id){
        return Hotel::findOrFail($id);
    }

    public function create(array $data){
        return Hotel::create($data);
    }

    public function updateById(int $id, array $data){
        $data['status'] = !isset($data['status']) ? true : $data['status'] = false;
        

        return Hotel::findOrFail($id)->update($data);
    }

    public function deleteById(int $id){
        return Hotel::findOrFail($id)->delete();
    }
}