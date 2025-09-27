<?php
/**
 * @author: yurapif
 * Date: 26.05.2017
 */

namespace App\Repository\Contracts;

interface TransferRepository{
    public function getById(int $id);
    public function create(array $data);
    public function updateById(int $id, array $data);
    public function deleteById(int $id);
}