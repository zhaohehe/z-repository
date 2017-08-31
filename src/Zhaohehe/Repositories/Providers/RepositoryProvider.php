<?php

/*
 * Sometime too hot the eye of heaven shines
 */
namespace Zhaohehe\Repositories\Providers;

use Illuminate\Support\Composer;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\ServiceProvider;
use Zhaohehe\Repositories\Console\Commands\MakeCriteriaCommand;
use Zhaohehe\Repositories\Console\Commands\MakeRepositoryCommand;
use Zhaohehe\Repositories\Console\Commands\MakeTransformerCommand;
use Zhaohehe\Repositories\Creators\Creators\CriteriaCreator;
use Zhaohehe\Repositories\Creators\Creators\RepositoryCreator;
use Zhaohehe\Repositories\Creators\Creators\TransformerCreator;

class RepositoryProvider extends ServiceProvider
{
    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = true;


    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        $config_path = __DIR__.'/../../../../config/repository.php';

        $this->publishes(
            [$config_path => config_path('repository.php')],
            'repository'
        );
    }


    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $this->registerBindings();

        $this->commands(MakeRepositoryCommand::class);    // Register the make:repository command.

        $this->commands(MakeCriteriaCommand::class);      // Register the make:criteria command.

        $this->commands(MakeTransformerCommand::class);   // Register the make:transformer command.

        $config_path = __DIR__.'/../../../../config/repository.php';

        $this->mergeConfigFrom(
            $config_path,
            'repository'
        );
    }

    protected function registerBindings()
    {
        $this->app->instance('FileSystem', new Filesystem());

        $this->app->bind('Composer', function ($app) {
            return new Composer($app['FileSystem']);
        });

        $this->app->singleton('RepositoryCreator', function ($app) {
            return new RepositoryCreator($app['FileSystem']);
        });

        $this->app->singleton('CriteriaCreator', function ($app) {
            return new CriteriaCreator($app['FileSystem']);
        });

        $this->app->singleton('TransformerCreator', function ($app) {
            return new TransformerCreator($app['FileSystem']);
        });

    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return [

        ];
    }
}