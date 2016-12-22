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
use Zhaohehe\Repositories\Console\Commands\Creators\CriteriaCreator;
use Zhaohehe\Repositories\Console\Commands\Creators\RepositoryCreator;

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

        $this->registerMakeRepositoryCommand();

        $this->registerMakeCriteriaCommand();

        $this->commands(['command.repository.make', 'command.criteria.make']);

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

    }


    /**
     * Register the make:repository command.
     */
    protected function registerMakeRepositoryCommand()
    {
        // Make repository command.
        $this->app['command.repository.make'] = $this->app->share(
            function($app)
            {
                return new MakeRepositoryCommand($app['RepositoryCreator'], $app['Composer']);
            }
        );
    }


    /**
     * Register the make:criteria command.
     */
    protected function registerMakeCriteriaCommand()
    {
        // Make criteria command.
        $this->app['command.criteria.make'] = $this->app->share(
            function($app)
            {
                return new MakeCriteriaCommand($app['CriteriaCreator'], $app['Composer']);
            }
        );
    }


    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return [
            'command.repository.make',
            'command.criteria.make'
        ];
    }
}