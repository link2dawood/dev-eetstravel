<?php

namespace App\Repository\ClientRepository;
use App\Client;
use App\Repository\Contracts\ClientRepository;


class EloquentClientRepository implements ClientRepository{
    public function getById(int $id){
        return Client::findOrFail($id);
    }

    public function create(array $data){
        return Client::create($data);
    }

    public function updateById(int $id, array $data){
        return Client::findOrFail($id)->update($data);
    }

    public function deleteById(int $id){
        return Client::findOrFail($id)->delete();
    }
}