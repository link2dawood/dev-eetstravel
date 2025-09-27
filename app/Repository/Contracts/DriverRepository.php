<?php
/**
 * Created by PhpStorm.
 * User: alex
 * Date: 29.11.17
 * Time: 12:29
 */

namespace App\Repository\Contracts;


interface DriverRepository
{
    public function all();
    public function byId(int $id);
    public function create(array $data);
    public function updateById(int $id, array $data);
    public function deleteById(int $id);
}