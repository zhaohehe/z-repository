<?php
/*
 * Sometime too hot the eye of heaven shines
 */

namespace Zhaohehe\Repositories\Creators\Creators;

use Illuminate\Support\Facades\Config;
use Doctrine\Common\Inflector\Inflector;

class TransformerCreator extends Creator
{
    
    /**
     * @var transformer directory
     */
    protected $directory = 'repository.transformer_path';

    /**
     * @var stub
     */
    protected $stub = 'transformer.stub';


    /**
     * @return string
     */
    protected function getModelName()
    {
        $model = $this->model;

        if(isset($model) && !empty($model))    // Check if the model isset.
        {
            $model_name = $model;
        } else {
            $model_name = Inflector::singularize($this->stripRepositoryName());     // Set the model name by the stripped transformer name.
        }
        return $model_name;
    }


    /**
     * Get the stripped transformer name.
     *
     * @return string
     */
    protected function stripRepositoryName()
    {
        $stripped   = str_replace("transformer", "", strtolower($this->class));    // Remove transformer from the string.

        $result = ucfirst($stripped);       // Uppercase transformer name.

        return $result;
    }


    /**
     * @return int
     */
    protected function createClass()
    {
        $filename = (!strpos($this->class, 'Transformer')) ? $this->class.'Transformer' : $this->class;

        $path = $this->directory . DIRECTORY_SEPARATOR . $filename . '.php';

        $model_path           = Config::get('repository.model_namespace');
        $transformer_namespace = Config::get('repository.transformer_namespace');

        $populate_data = [
            'transformer_namespace' => $transformer_namespace,
            'transformer_class'     => $filename,
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