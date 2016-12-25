<?php
/*
 * Sometime too hot the eye of heaven shines
 */

namespace Zhaohehe\Repositories\Contracts;


interface ModelEventInterface
{
    /**
     * @param RepositoryInterface $repository
     */
    public function setRepository($repository);

    /**
     * @return repository
     */
    public function getRepository();

}