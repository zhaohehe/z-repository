<?php

/*
 * Sometime too hot the eye of heaven shines
 */

namespace Zhaohehe\Repositories\Console\Commands\Creators;

use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Facades\Config;
use Doctrine\Common\Inflector\Inflector;


class RepositoryCreator
{

    /**
     * @var Filesystem
     */
    protected $files;

    /**
     * @var Repository
     */
    protected $repository;

    /**
     * @var model
     */
    protected $model;

    /**
     * @var repository directory
     */
    protected $directory;



    /**
     * RepositoryCreator constructor.
     *
     * @param Filesystem $filesystem
     */
    public function __construct(Filesystem $filesystem)
    {
        $this->files = $filesystem;
    }


    public function create($repository, $model)
    {
        $this->model = $model;
        $this->repository = $repository;

        $this->createDirectory();

        $this->createClass();
    }


    /**
     * create repository directory
     */
    protected function createDirectory()
    {
        $this->directory = Config::get('repository.repository_path');

        if (!$this->files->isDirectory($this->directory)) {    // Create the directory if not.

            $this->files->makeDirectory($this->directory, 0755, true);
        }
    }


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
        $stripped   = str_replace("repository", "", strtolower($this->repository));    // Remove repository from the string.

        $result = ucfirst($stripped);       // Uppercase repository name.

        return $result;
    }


    /**
     * get stub
     *
     * @return string
     */
    protected function getStub()
    {
        $stub_path = __DIR__ . '/../../../../../../resources/stubs/';

        $stub = $this->files->get($stub_path . "repository.stub");

        return $stub;
    }


    /**
     * @return int
     */
    protected function createClass()
    {
        $filename = (!strpos($this->repository, 'Repository')) ? $this->repository.'Repository' : $this->repository;

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