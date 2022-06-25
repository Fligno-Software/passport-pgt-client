<?php

namespace Fld3\PassportPgtClient;

use Illuminate\Support\ServiceProvider;

class PassportPgtClientServiceProvider extends ServiceProvider
{
    /**
     * Perform post-registration booting of services.
     *
     * @return void
     */
    public function boot(): void
    {
        // $this->loadTranslationsFrom(__DIR__.'/../resources/lang', 'fld3');
        // $this->loadViewsFrom(__DIR__.'/../resources/views', 'fld3');
        // $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
        // $this->loadRoutesFrom(__DIR__.'/routes.php');

        // Publishing is only necessary when using the CLI.
        if ($this->app->runningInConsole()) {
            $this->bootForConsole();
        }
    }

    /**
     * Register any package services.
     *
     * @return void
     */
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__.'/../config/passport-pgt-client.php', 'passport-pgt-client');

        // Register the service the package provides.
        $this->app->singleton('passport-pgt-client', function ($app) {
            return new PassportPgtClient;
        });
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return ['passport-pgt-client'];
    }

    /**
     * Console-specific booting.
     *
     * @return void
     */
    protected function bootForConsole(): void
    {
        // Publishing the configuration file.
        $this->publishes([
            __DIR__.'/../config/passport-pgt-client.php' => config_path('passport-pgt-client.php'),
        ], 'passport-pgt-client.config');

        // Publishing the views.
        /*$this->publishes([
            __DIR__.'/../resources/views' => base_path('resources/views/vendor/fld3'),
        ], 'passport-pgt-client.views');*/

        // Publishing assets.
        /*$this->publishes([
            __DIR__.'/../resources/assets' => public_path('vendor/fld3'),
        ], 'passport-pgt-client.views');*/

        // Publishing the translation files.
        /*$this->publishes([
            __DIR__.'/../resources/lang' => resource_path('lang/vendor/fld3'),
        ], 'passport-pgt-client.views');*/

        // Registering package commands.
        // $this->commands([]);
    }
}
