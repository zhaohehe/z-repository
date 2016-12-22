<?php

/*
 * Sometime too hot the eye of heaven shines
 */

namespace Zhaohehe\Repositories\Console\Commands\Creators;

use Illuminate\Support\Facades\Config;
use Doctrine\Common\Inflector\Inflector;


/**
 * Class RepositoryCreator
 *
 * @package Zhaohehe\Repositories\Console\Commands\Creators
 */
class RepositoryCreator extends Creator
{

    /**
     * @var repository directory
     */
    protected $directory = 'repository.repository_path';

    /**
     * @var stub
     */
    protected $stub = 'repository.stub';


    /**
     * @return string
     */
    protected function getModelName()
    {
        // Check if the model isset.
        if(isset($model) && !empty($model))
        {
            $model_name = $model;
        } else {
            // Set the model name by the stripped repository name.
            $model_name = Inflector::singularize($this->stripRepositoryName());
        }
        return $model_name;
    }


    /**
     * Get the stripped repository name.
     *
     * @return string
     */
    protected function stripRepositoryName()
    {
        $stripped   = str_replace("repository", "", strtolower($this->class));    // Remove repository from the string.

        $result = ucfirst($stripped);       // Uppercase repository name.

        return $result;
    }


    /**
     * @return int
     */
    protected function createClass()
    {
        $filename = (!strpos($this->class, 'Repository')) ? $this->class.'Repository' : $this->class;

        $path = $this->directory . DIRECTORY_SEPARATOR . $filename . '.php';

        $model_path           = Config::get('repository.model_namespace');
        $repository_namespace = Config::get('repository.repository_namespace');

        $populate_data = [
            'repository_namespace' => $repository_namespace,
            'repository_class'     => $filename,
            'model_path'           => $model_path,
            'model_class'          => $this->getModelName()
        ];

        $stub = $this->getStub();

        foreach ($populate_data as $key => $value) {

            $stub = str_replace($key, $value, $stub);
        }

        return $this->files->put($path, $stub);
    }
}