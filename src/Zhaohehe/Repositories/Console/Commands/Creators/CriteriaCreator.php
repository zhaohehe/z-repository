<?php
/*
 * Sometime too hot the eye of heaven shines
 */

namespace Zhaohehe\Repositories\Console\Commands\Creators;

use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Facades\Config;
use Doctrine\Common\Inflector\Inflector;

/**
 * Class CriteriaCreator
 *
 * @package Zhaohehe\Repositories\Console\Commands\Creators
 */
class CriteriaCreator
{

    /**
     * @var Filesystem
     */
    protected $files;

    /**
     * @var Criteria
     */
    protected $criteria;

    /**
     * @var model
     */
    protected $model;

    /**
     * @var criteria directory
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


    public function create($criteria, $model)
    {
        $this->model = $model;
        $this->criteria = $criteria;

        $this->createDirectory();

        $this->createClass();
    }


    /**
     * create criteria directory
     */
    protected function createDirectory()
    {
        $this->directory = Config::get('repository.criteria_path');

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
            // Set the model name by the stripped criteria name.
            $model_name = Inflector::singularize($this->stripRepositoryName());
        }
        return $model_name;
    }


    /**
     * Get the stripped criteria name.
     *
     * @return string
     */
    protected function stripRepositoryName()
    {
        $stripped   = str_replace("criteria", "", strtolower($this->criteria));    // Remove criteria from the string.

        $result = ucfirst($stripped);       // Uppercase criteria name.

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

        $stub = $this->files->get($stub_path . "criteria.stub");

        return $stub;
    }


    /**
     * @return int
     */
    protected function createClass()
    {
        $filename = (!strpos($this->criteria, 'Criteria')) ? $this->criteria.'Criteria' : $this->criteria;

        $path = $this->directory . DIRECTORY_SEPARATOR . $filename . '.php';

        $model_path           = Config::get('repository.model_namespace');
        $criteria_namespace = Config::get('repository.criteria_namespace');

        $populate_data = [
            'criteria_namespace' => $criteria_namespace,
            'criteria_class'     => $filename,
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