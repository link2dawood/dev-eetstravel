<?php
/**
 * @author: yurapif
 * Date: 08.05.2017
 */

namespace App\Repository\FlightRepository;


use App\Flight;
use App\Repository\Contracts\FlightRepository;

class EloquentFlightRepository implements FlightRepository {
    public function getAll(){
        return Flight::paginate(15);
    }

    public function create(array $data){
        return Flight::create($data);
    }

    public function getById(int $id){
        return Flight::findOrFail($id);
    }

    public function updateById(int $id, array $data){
        return Flight::findOrFail($id)->update($data);
    }

    public function deleteById(int $id){
        return Flight::findOrFail($id)->delete();
    }
}