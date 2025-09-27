<?php
/**
 * @author: yurapif
 * Date: 08.05.2017
 */

namespace App\Repository\Contracts;


interface GuideRepository {
    public function create(array $data);
    public function updateById(int $id, array $data);
    public function getById(int $id);
    public function deleteById(int $id);
}