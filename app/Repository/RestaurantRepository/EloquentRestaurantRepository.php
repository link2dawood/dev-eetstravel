<?php
namespace App\Repository\RestaurantRepository;
use App\Repository\Contracts\RestaurantRepository;
use App\Restaurant;

/**
 * @author: yurapif
 * Date: 26.05.2017
 */
class EloquentRestaurantRepository implements RestaurantRepository{
    public function create(array $data){
        return Restaurant::create($data);
    }

    public function getById(int $id){
        return Restaurant::findOrFail($id);
    }

    public function updateById(int $id, array $data){
        return Restaurant::findOrFail($id)->update($data);
    }

    public function deleteById(int $id){
        return Restaurant::findOrFail($id)->delete();
    }
}