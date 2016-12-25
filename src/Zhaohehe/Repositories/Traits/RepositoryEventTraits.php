<?php
/*
 * Sometime too hot the eye of heaven shines
 */

namespace Zhaohehe\Repositories\Traits;

/**
 * Whenever a new model is saved for the first time,
 * the creating and created events will fire.
 * If a model already existed in the database and the save method is called,
 * the updating / updated events will fire.
 * However, in both cases, the saving / saved events will fire.
 *
 * @CREATE: saving > creating > created > saved
 * @UPDATE: saving > updating > updated > saved
 *
 */


trait RepositoryEventTraits
{
    public function onCreating()
    {
    }

    public function onCreated()
    {
    }

    public function onUpdating()
    {
    }

    public function onUpdated()
    {
    }

    public function onSaving()
    {
    }

    public function onSaved()
    {
    }

    public function onDeleting()
    {
    }

    public function onDeleted()
    {
    }
}