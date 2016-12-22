<?php
/*
 * Sometime too hot the eye of heaven shines
 */

namespace Zhaohehe\Repositories\Contracts;

/**
 * Interface RepositoryInterface
 */
interface RepositoryInterface
{

    /**
     * @param array $columns
     *
     * @return mixed
     */
    public function all($columns = ['*']);


    /**
     * @param int $perPage
     * @param array $columns
     *
     * @return mixed
     */
    public function paginate($perPage = 15, $columns = ['*']);


    /**
     * @param array $data
     *
     * @return mixed
     */
    public function create(array $data);


    /**
     * @param array $data
     *
     * @return mixed
     */
    public function save(array $data);


    /**
     * @param $id
     *
     * @return mixed
     */
    public function delete($id);


    /**
     * @param array $data
     * @param $id
     *
     * @return mixed
     */
    public function update(array $data, $id);


    /**
     * @param $id
     * @param array $columns
     *
     * @return mixed
     */
    public function find($id, $columns = ['*']);


    /**
     * @param $field
     * @param $value
     * @param array $columns
     *
     * @return mixed
     */
    public function findBy($field, $value, $columns = ['*']);


    /**
     * @param $where
     * @param array $columns
     *
     * @return mixed
     */
    public function findWhere($where, $columns = ['*']);

}