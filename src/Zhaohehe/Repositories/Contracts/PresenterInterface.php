<?php
/*
 * Sometime too hot the eye of heaven shines
 */

namespace Zhaohehe\Repositories\Contracts;

/**
 *  Interface PresenterInterface
 */
interface PresenterInterface
{
    /**
     * Prepare data to present
     * @param $data
     *
     * @return mixed
     */
    public function present($data);
}