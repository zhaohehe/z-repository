<?php
/*
 * Sometime too hot the eye of heaven shines
 */

namespace Zhaohehe\Repositories\Console\Commands\Creators;

use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Facades\Config;

abstract class Creator
{

    /**
     * @var Filesystem
     */
    protected $files;

    /**
     * @var modelName
     */
    protected $model;

    /**
     * @var className
     */
    protected $class;

    /**
     * @var string
     */
    protected $stub_path = __DIR__ . '/../../../../../../resources/stubs/';


    /**
     * Creator constructor.
     *
     * @param Filesystem $filesystem
     */
    public function __construct(Filesystem $filesystem)
    {
        $this->files = $filesystem;
    }


    /**
     * @param $class
     *
     * @param $model
     */
    public function create($class, $model)
    {
        $this->model = $model;
        $this->class = $class;

        $this->createDirectory();

        $this->createClass();
    }



    /**
     * get stub
     *
     * @return string
     */
    protected function getStub()
    {
        $stub = $this->files->get($this->stub_path . $this->stub);

        return $stub;
    }


    /**
     * create  directory
     */
    protected function createDirectory()
    {
        $this->directory = Config::get($this->directory);

        if (!$this->files->isDirectory($this->directory)) {    // Create the directory if not.

            $this->files->makeDirectory($this->directory, 0755, true);
        }
    }


    /**
     * @return mixed
     */
    protected abstract function createClass();

}