<?php

namespace Zhaohehe\Repositories\Eloquent;

use Illuminate\Support\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Container\Container as App;
use Zhaohehe\Repositories\Contracts\RepositoryInterface;
use Zhaohehe\Repositories\Exceptions\RepositoryException;


/**
 * Class Repository
 *
 * @package Zhaohehe\Repositories\Eloquent
 */
abstract class Repository implements RepositoryInterface
{

    /**
     * @var Container
     */
    protected $app;

    /**
     * @var Collection
     */
    protected $criteria;

    /**
     * @var Model
     */
    protected $model;

    /**
     * @var bool
     */
    protected $skipCriteria = false;

    public function __construct(App $app, Collection $collection)
    {
        $this->app = $app;
        $this->criteria = $collection;

        $this->resetScope();
        $this->makeModel();
    }


    /**
     * Specify Model
     *
     * @return mixed
     */
    public abstract function model();


    /**
     * @return mixed
     */
    public function makeModel()
    {
        $eloquentModel = $this->model();

        return $this->setModel($eloquentModel);
    }


    public function setModel($eloquentModel)
    {
        $model = $this->app->make($eloquentModel);

        if ( ! $model instanceof Model ) {
            //throw new Exception
        }
        return $this->model = $model;
    }


    /**
     * @return $this
     */
    public function resetScope()
    {
        $this->skipCriteria(false);

        return $this;
    }


    /**
     * set skipCriteria
     *
     * @param bool $status
     */
    public function skipCriteria($status = true)
    {
        $this->skipCriteria = $status;

        return $this;
    }



    /**
     * @param array $columns
     *
     * @return mixed
     */
    public function all($columns = ['*'])
    {

    }


    /**
     * @param int $perPage
     * @param array $columns
     *
     * @return mixed
     */
    public function paginate($perPage = 15, $columns = ['*'])
    {

    }


    /**
     * @param array $data
     *
     * @return mixed
     */
    public function create(array $data)
    {

    }


    /**
     * @param array $data
     *
     * @return mixed
     */
    public function save(array $data)
    {

    }


    /**
     * @param $id
     *
     * @return mixed
     */
    public function delete($id)
    {

    }


    /**
     * @param array $data
     * @param $id
     *
     * @return mixed
     */
    public function update(array $data, $id)
    {

    }


    /**
     * @param $id
     * @param array $columns
     *
     * @return mixed
     */
    public function find($id, $columns = ['*'])
    {
        $this->applyCriteria();

        return $this->model->find($id, $columns);
    }


    /**
     * @param $field
     * @param $value
     * @param array $columns
     *
     * @return mixed
     */
    public function findBy($field, $value, $columns = ['*'])
    {

    }


    /**
     * @param $where
     * @param array $columns
     *
     * @return mixed
     */
    public function findWhere($where, $columns = ['*'])
    {

    }


    public function applyCriteria()
    {
        if ($this->skipCriteria == true) {
            return $this;
        }

    }
}