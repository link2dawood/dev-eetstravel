<?php
namespace App\Repository\TransferRepository;

use App\Repository\Contracts\TransferRepository;
use App\Transfer;

/**
 * @author: yurapif
 * Date: 26.05.2017
 */
class EloquentTransferRepository implements TransferRepository{
    public function getById(int $id){
        return Transfer::findOrFail($id);
    }

    public function create(array $data){
        return Transfer::create($data);
    }

    public function updateById(int $id, array $data){
        return Transfer::findOrFail($id)->update($data);
    }

    public function deleteById(int $id){
        return Transfer::findOrFail($id)->delete();
    }
}