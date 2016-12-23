<?php
/*
 * Sometime too hot the eye of heaven shines
 */

namespace Zhaohehe\Repositories\Creators\Creators;

use Illuminate\Support\Facades\Config;

/**
 * Class CriteriaCreator
 *
 * @package Zhaohehe\Repositories\Console\Commands\Creators
 */
class CriteriaCreator extends Creator
{

    /**
     * @var string
     */
    protected $stub = 'criteria.stub';


    /**
     * @var criteria directory
     */
    protected $directory = 'repository.criteria_path';



    /**
     * @return int
     */
    protected function createClass()
    {
        $filename = (!strpos($this->class, 'Criteria')) ? $this->class.'Criteria' : $this->class;

        $path = $this->directory . DIRECTORY_SEPARATOR . $filename . '.php';

        $criteria_namespace = Config::get('repository.criteria_namespace');

        $populate_data = [
            'criteria_namespace' => $criteria_namespace,
            'criteria_class'     => $filename
        ];

        $stub = $this->getStub();

        foreach ($populate_data as $key => $value) {

            $stub = str_replace($key, $value, $stub);
        }

        return $this->files->put($path, $stub);
    }
}