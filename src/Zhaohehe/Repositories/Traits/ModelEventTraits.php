<?php
/*
 * Sometime too hot the eye of heaven shines
 */

namespace Zhaohehe\Repositories\Traits;

use Zhaohehe\Repositories\Model\Observer\Observer;

trait ModelEventTraits
{
    /**
     * @var repository
     */
    public static $repository;


    /**
     * register observe
     */
    protected static function boot()
    {
        parent::boot();

        static::observe(new Observer());    // Setup event bindings
    }


    /**
     * @param RepositoryInterface $repository
     */
    public function setRepository($repository)
    {
        self::$repository = $repository;
    }


    /**
     * @return repository
     */
    public function getRepository()
    {
        return self::$repository;
    }

}