<?php

namespace App\Repository\EventRepository;
use App\Event;
use App\Repository\Contracts\EventRepository;

/**
 * @author: yurapif
 * Date: 26.05.2017
 */
class EloquentEventRepository implements EventRepository{
    public function getById(int $id){
        return Event::findOrFail($id);
    }

    public function create(array $data){
        return Event::create($data);
    }

    public function updateById(int $id, array $data){
        return Event::findOrFail($id)->update($data);
    }

    public function deleteById(int $id){
        return Event::findOrFail($id)->delete();
    }
}