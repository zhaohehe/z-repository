<?php

/*
 * Sometime too hot the eye of heaven shines
 */

namespace Zhaohehe\Repositories\Presenter;

use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Pagination\AbstractPaginator;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use League\Fractal\Manager;
use League\Fractal\Pagination\IlluminatePaginatorAdapter;
use League\Fractal\Resource\Collection;
use League\Fractal\Resource\Item;
use Zhaohehe\Repositories\Contracts\PresenterInterface;


class Presenter implements PresenterInterface
{
    protected $fractal;


    public $transformer;

    protected $resource = null;

    protected $resourceKeyItem = null;

    protected $resourceKeyCollection = null;


    public function __construct()
    {
        $this->fractal = new Manager();
        //todo : parseIncludes and setupSerializer
    }

    public function present($data)
    {
        if ($data instanceof EloquentCollection) {
            $this->resource = $this->transformCollection($data);
        } elseif ($data instanceof AbstractPaginator) {
            $this->resource = $this->transformPaginator($data);
        } else {
            $this->resource = $this->transformItem($data);
        }

        return $this->fractal->createData($this->resource)->toArray();
    }


    /**
     * @param $data
     *
     * @return Item
     */
    protected function transformItem($data)
    {
        return new Item($data, $this->transformer, $this->resourceKeyItem);
    }


    /**
     * @param $data
     *
     * @return \League\Fractal\Resource\Collection
     */
    protected function transformCollection($data)
    {
        return new Collection($data, $this->transformer, $this->resourceKeyCollection);
    }



    /**
     * @param AbstractPaginator|LengthAwarePaginator|Paginator $paginator
     *
     * @return \League\Fractal\Resource\Collection
     */
    protected function transformPaginator($paginator)
    {
        $collection = $paginator->getCollection();
        $resource = new Collection($collection, $this->transformer, $this->resourceKeyCollection);
        $resource->setPaginator(new IlluminatePaginatorAdapter($paginator));

        return $resource;
    }
}