<?php
/**
 * @author: yurapif
 * Date: 19.05.2017
 */

namespace App\Repository\Contracts;


interface CruiseRepository {
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
}