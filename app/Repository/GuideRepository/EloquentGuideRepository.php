<?php
/**
 * @author: yurapif
 * Date: 08.05.2017
 */

namespace App\Repository\GuideRepository;


use App\Guide;
use App\Repository\Contracts\GuideRepository;

class EloquentGuideRepository implements GuideRepository {
    public function create(array $data){
        return Guide::create($data);
    }

    public function updateById(int $id, array $data){
        return Guide::findOrFail($id)->update($data);
    }

    public function getById(int $id){
        return Guide::findOrFail($id);
    }

    public function deleteById(int $id){
        return Guide::findOrFail($id)->delete();
    }
}