<?php

/*
 * Sometime too hot the eye of heaven shines
 */

namespace Zhaohehe\Repositories\Criteria;

use Zhaohehe\Repositories\Contracts\RepositoryInterface;

/**
 * Class Criteria
 */
abstract class Criteria
{
    /**
     * @param $model
     * @param $repository
     *
     * @return mixed
     */
    public abstract function apply($model, RepositoryInterface $repository);

}