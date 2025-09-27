<?php

namespace App\Repository\Contracts;

/**
 * @author: yurapif
 * Date: 08.05.2017
 */
interface HotelRepository
{
    public function all();
    public function byId(int $id);
    public function create(array $data);
    public function updateById(int $id, array $data);
    public function deleteById(int $id);
}