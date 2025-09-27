<?php
/**
 * @author: yurapif
 * Date: 08.05.2017
 */

namespace App\Repository\Contracts;


interface FlightRepository {
    public function getAll();
    public function create(array $data);
    public function getById(int $id);
    public function updateById(int $id,array $data);
    public function deleteById(int $id);
}