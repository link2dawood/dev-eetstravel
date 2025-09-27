<?php

namespace App\Repository\Contracts;

interface ChatRepository
{
    /**
     * @return mixed
     */
    public function getAll();

    /**
     * @param int $id
     * @return mixed
     */
    public function showById(int $id);

    /**
     * @param array $data
     * @return mixed
     */
    public function create(array $data);

    /**
     * @param int $id
     * @return mixed
     */
    public function destroy(int $id);

    /**
     * @param int $id
     * @param array $data
     * @return mixed
     */
    public function updateById(int $id, array $data);

    /**
     * @return mixed
     */
    public function getMainChat();

    public function getDirectChats(int $userId);

    public function getCustomChats(int $userId);
}
