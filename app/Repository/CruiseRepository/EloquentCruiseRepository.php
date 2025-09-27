<?php
/**
 * @author: yurapif
 * Date: 19.05.2017
 */

namespace App\Repository\CruiseRepository;


use App\Cruises;
use App\Repository\Contracts\CruiseRepository;

class EloquentCruiseRepository implements CruiseRepository {
    /**
     * @inheritDoc
     */
    public function getAll(){
        return Cruises::all();
    }

    /**
     * @inheritDoc
     */
    public function showById(int $id){
        return Cruises::findOrFail($id);
    }

    /**
     * @inheritDoc
     */
    public function create(array $data){
        return Cruises::create($data);
    }

    /**
     * @inheritDoc
     */
    public function destroy(int $id){
        return Cruises::destroy($id);
    }

    /**
     * @inheritDoc
     */
    public function updateById(int $id, array $data){
        return Cruises::findOrFail($id)->update($data);
    }

}