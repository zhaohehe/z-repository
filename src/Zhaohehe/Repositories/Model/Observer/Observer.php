<?php

/*
 * Sometime too hot the eye of heaven shines
 */

namespace Zhaohehe\Repositories\Model\Observer;

use Zhaohehe\Repositories\Contracts\ModelEventInterface;

class Observer
{

    public function creating(ModelEventInterface $model)
    {
        $repository = $model->getRepository();
        $repository->onCreating();
    }

    public function created(ModelEventInterface $model)
    {
        $repository = $model->getRepository();
        $repository->onCreated();
    }

    public function updating(ModelEventInterface $model)
    {
        $repository = $model->getRepository();
        $repository->onUpdating();
    }

    public function updated(ModelEventInterface $model)
    {
        $repository = $model->getRepository();
        $repository->onUpdated();
    }

    public function saving(ModelEventInterface $model)
    {
        $repository = $model->getRepository();
        $repository->onSaving();
    }

    public function saved(ModelEventInterface $model)
    {
        $repository = $model->getRepository();
        $repository->onSaved();
    }

    public function deleting(ModelEventInterface $model)
    {
        $repository = $model->getRepository();
        $repository->onDeleting();
    }

    public function deleted(ModelEventInterface $model)
    {
        $repository = $model->getRepository();
        $repository->onDeleted();
    }
}