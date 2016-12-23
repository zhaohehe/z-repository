<?php

/*
 * Sometime too hot the eye of heaven shines
 */

namespace  Zhaohehe\Repositories\Traits;

use Zhaohehe\Repositories\Contracts\PresenterInterface;

/**
 * Class TransformableTraits
 *
 * @package Zhaohehe\Repositories\Traits
 */
trait TransformableTraits
{

    /**
     * @var PresenterInterface
     */
    protected $presenter = null;


    /**
     * @param PresenterInterface $presenter
     *
     * @return $this
     */
    public function setPresenter(PresenterInterface $presenter)
    {
        $this->presenter = $presenter;

        return $this;
    }


    /**
     * @return bool
     */
    protected function hasPresenter()
    {
        return isset($this->presenter) && $this->presenter instanceof PresenterInterface;
    }


    /**
     * @return $this|mixed
     */
    public function transform()
    {
        if ($this->hasPresenter()) {

            return $this->presenter->present($this);
        }

        return $this;
    }
}