<?php namespace Efficiently\Larasset;

use Illuminate\Support\ServiceProvider;

class LarassetServiceProvider extends ServiceProvider
{

    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = false;

    /**
     * Bootstrap the application events.
     *
     * @return void
     */
    public function boot()
    {
        // Publish config
        $this->publishes([
            __DIR__ . '/../../config/config.php' => config_path('larasset.php')
        ], 'config');

        $this->mergeConfigFrom(
            __DIR__ . '/../../config/config.php',
            'larasset'
        );

        // Init assets
        $this->app->make('asset', [public_path()."/assets"]);
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton('asset', function ($app, $parameters = []) {
            if (count($parameters) < 2) {
                $parameters = array_merge($parameters, [null]);
            }
            list($dir, $path) = $parameters;

            return new Asset($dir, $path);
        });

        $this->app->bind('manifest', function ($app, $parameters = []) {
            if (count($parameters) < 2) {
                $parameters = array_merge($parameters, [null]);
            }
            list($dir, $path) = $parameters;

            return new Manifest($dir, $path);
        });

        // TODO: Allow to register or not Larasset commands in production env with a config option
        if ($this->app->environment() !== 'production' && $this->app['config']->get('app.debug')) {
            // For security reasons Larasset commands aren't available in production environment
            $this->registerCommands();
        }

        // TODO: Allow to publish default package.json in the config path of the package
    }

    protected function registerCommands()
    {
        $this->app->bind('larasset:precompile', function ($app) {
            return new Commands\PrecompileAssetsCommand();
        });

        $this->app->bind('larasset:clean', function ($app) {
            return new Commands\CleanAssetsCommand();
        });

        $this->app->bind('larasset:setup', function ($app) {
            return new Commands\SetupAssetsCommand();
        });

        $this->app->bind('larasset:serve', function ($app) {
            return new Commands\ServeAssetsCommand();
        });

        $this->app->bind('server', function ($app) {
            return new Commands\ServerCommand();
        });

        $this->commands([
            'larasset:precompile', 'larasset:clean', 'larasset:setup',
            'larasset:serve', 'server'
        ]);
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return [];
    }
}
