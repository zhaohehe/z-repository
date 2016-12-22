<?php

namespace Zhaohehe\Repositories\Eloquent;

use Illuminate\Support\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Container\Container as App;
use Zhaohehe\Repositories\Criteria\Criteria;
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
            throw new RepositoryException('Class {$model} must be an instance of Illuminate\\Database\\Eloquent\\Model', 201);
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



    /**
     * @return Collection
     */
    public function getCriteria()
    {
        return $this->criteria;
    }


    /**
     * @param Criteria $criteria
     *
     * @return $this
     */
    public function getByCriteria(Criteria $criteria)
    {
        $this->model = $criteria->apply($this->model, $this);

        return $this;
    }


    /**
     * @param Criteria $criteria
     *
     * @return $this
     */
    public function pushCriteria(Criteria $criteria)
    {
        //remove if criteria existed
        $key = $this->criteria->search(function ($item) use ($criteria) {
            return is_object($item) && (get_class($item) == get_class($criteria));
        });
        if (is_int($key)) {
            $this->criteria->offsetUnset($key);
        }

        $this->criteria->push($criteria);
        return $this;
    }


    /**
     * apply Criteria
     *
     * @return $this
     */
    public function applyCriteria()
    {
        if ($this->skipCriteria == true) {
            return $this;
        }

        foreach ($this->getCriteria() as $criteria) {
            if ($criteria instanceof Criteria) {
                $this->model = $criteria->apply($this->model, $this);
            }
        }

        return $this;
    }
}