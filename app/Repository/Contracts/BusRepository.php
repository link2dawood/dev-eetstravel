<?php
/**
 * Created by PhpStorm.
 * User: alex
 * Date: 27.10.17
 * Time: 15:34
 */

namespace App\Repository\Contracts;


interface BusRepository
{
    public function all();
    public function byId(int $id);
    public function create(array $data);
    public function updateById(int $id, array $data);
    public function deleteById(int $id);
}