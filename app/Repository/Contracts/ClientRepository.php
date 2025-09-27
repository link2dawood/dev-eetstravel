<?php

namespace App\Repository\Contracts;

interface ClientRepository{
    public function getById(int $id);
    public function create(array $data);
    public function updateById(int $id, array $data);
    public function deleteById(int $id);
}