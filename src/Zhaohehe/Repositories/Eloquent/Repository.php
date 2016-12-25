<?php

namespace Zhaohehe\Repositories\Eloquent;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Container\Container as App;
use Zhaohehe\Repositories\Contracts\ModelEventInterface;
use Zhaohehe\Repositories\Contracts\RepositoryEventInterface;
use Zhaohehe\Repositories\Criteria\Criteria;
use Zhaohehe\Repositories\Presenter\Presenter;
use Zhaohehe\Repositories\Contracts\Transformable;
use Zhaohehe\Repositories\Contracts\CriteriaInterface;
use Zhaohehe\Repositories\Contracts\RepositoryInterface;
use Zhaohehe\Repositories\Exceptions\RepositoryException;
use Zhaohehe\Repositories\Traits\RepositoryEventTraits;


/**
 * Class Repository
 *
 * @package Zhaohehe\Repositories\Eloquent
 */
abstract class Repository implements RepositoryInterface, CriteriaInterface, RepositoryEventInterface
{

    use RepositoryEventTraits;


    /**
     * @var Container
     */
    protected $app;

    /**
     * @var Collection
     */
    protected $criteria;

    /**
     * @var
     */
    protected $presenter;
    /**
     * @var
     */
    protected $transformer;

    /**
     * @var Model
     */
    protected $model;

    /**
     * @var bool
     */
    protected $skipCriteria = false;

    /**
     * @var bool
     */
    protected $skipTransformer = false;


    /**
     * Repository constructor.
     * @param App $app
     * @param Collection $collection
     */
    public function __construct(App $app, Collection $collection, Presenter $presenter)
    {
        $this->app = $app;
        $this->criteria = $collection;
        $this->presenter = $presenter;

        $this->resetScope();
        $this->makeModel();
        $this->makeTransformer();
        $this->boot();
    }


    /**
     * boot
     */
    public function boot()
    {

    }


    /**
     * Specify Transformer class name
     *
     * @return string
     */
    public function transformer()
    {
        return null;
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

        if ($model instanceof ModelEventInterface) {
            $model->setRepository($this);    //set repository
        }

        if ( ! $model instanceof Model ) {
            throw new RepositoryException('Class {$model} must be an instance of Illuminate\\Database\\Eloquent\\Model', 201);
        }

        return $this->model = $model;
    }


    public function setTransformer($transformer)
    {
        $this->makeTransformer($transformer);

        return $this;
    }


    public function makeTransformer($transformer = null)
    {
        $transformer = !is_null($transformer) ? $transformer : $this->transformer();

        if (!is_null($transformer)) {
            $this->transformer = is_string($transformer) ? $this->app->make($transformer) : $transformer;    //string or object

         /*   if (!$this->transformer instanceof ...) {
                throw
            }*/

         return $this->transformer;
        }

        return null;
    }

    /**
     * reset model
     */
    public function resetModel()
    {
        $this->makeModel();
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
     * Skip Presenter Wrapper
     * @param bool $status
     *
     * @return $this
     */
    public function skipTransformer($status = true)
    {
        $this->skipTransformer = $status;

        return $this;
    }



    /**
     * @param array $columns
     *
     * @return mixed
     */
    public function all($columns = ['*'])
    {
        $this->applyCriteria();

        $result = $this->model->get($columns);
        $this->resetModel();

        return $this->parserResult($result);
    }


    /**
     * @param array $relations
     *
     * @return $this
     */
    public function with(array $relations)
    {
        $this->model = $this->model->with($relations);

        return $this;
    }


    /**
     * @param int $perPage
     * @param array $columns
     *
     * @return mixed
     */
    public function paginate($perPage = 15, $columns = ['*'])
    {
        $this->applyCriteria();

        $result = $this->model->paginate($perPage, $columns);
        $this->resetModel();

        return $this->parserResult($result);
    }


    /**
     * @param array $data
     *
     * @return mixed
     */
    public function create(array $data)
    {
        $model = $this->model->create($data);

        return $this->parserResult($model);
    }


    /**
     * @param array $data
     *
     * @return mixed
     */
    public function save(array $data)
    {
        foreach ($data as $key => $value) {
            $this->model->$key = $value;
        }

        $model = $this->model->save();

        return $this->parserResult($model);
    }


    /**
     * @param $id
     *
     * @return mixed
     */
    public function delete($id)
    {
        return $this->model->destroy($id);
    }


    /**
     * @param array $data
     * @param $id
     *
     * @return mixed
     */
    public function update(array $data, $id, $field = 'id')
    {
        $temporarySkipTransformer = $this->skipTransformer;

        $this->skipTransformer(true);

        $model = $this->model->where($field, '=', $id)->update($data);

        $this->skipTransformer($temporarySkipTransformer);
        $this->resetModel();

        return $this->parserResult($model);
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
        $model = $this->model->findOrFail($id, $columns);
        $this->resetModel();

        return $this->parserResult($model);
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
        $this->applyCriteria();

        $result = $this->model->where($field, '=', $value)->get($columns);
        $this->resetModel();

        return $this->parserResult($result);
    }


    /**
     * @param $where
     * @param array $columns
     *
     * @return mixed
     */
    public function findWhere($where, $columns = ['*'], $or = false)
    {
        $this->applyCriteria();

        foreach ($where as $field => $value) {
            if ($value instanceof \Closure) {
                $this->model = (! $or) ? $this->model->where($value) : $this->model->orWhere($value);
            } elseif (is_array($value)) {
                if (count($value) == 3) {    // 'id' ,'>', 100
                    list($field, $operator, $search) = $value;
                    $this->model = (! $or) ? $this->model->where($field, $operator, $search) : $this->model->oeWhere($field, $operator, $search);
                } elseif (count($value) == 2) {    // 'name', 'zhaohehe'
                    list($field, $search) = $value;
                    $this->model = (! $or) ? $this->model->where($field, $search) : $this->model->orWhere($field, $search);
                }
            } else {
                $this->model = (! $or) ? $this->model->where($field, '=', $value) : $this->model->orWhere($field, $value);
            }
        }

        $result = $this->model->get($columns);
        $this->resetModel();

        return $this->parserResult($result);
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


    /**
     * Wrapper result data
     * @param $result
     *
     * @return mixed|void
     */
    public function parserResult($result)
    {
        $this->presenter->transformer = $this->transformer;

        if ($result instanceof Collection || $result instanceof LengthAwarePaginator) {
            $result->each(function ($model) {
                if ($model instanceof Transformable) {
                    $model->setPresenter($this->presenter);
                }

                return $model;
            });
        } elseif ($result instanceof Transformable) {
            $result = $result->setPresenter($this->presenter);
        }

        if (!$this->skipTransformer && $this->transformer) {
            return $this->presenter->present($result);
        }

        return $result;
    }
}