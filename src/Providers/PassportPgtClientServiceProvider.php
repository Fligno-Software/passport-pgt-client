<?php

namespace Fld3\PassportPgtClient\Providers;

use Fld3\PassportPgtClient\Services\PassportPgtClient;
use Fligno\StarterKit\Abstracts\BaseStarterKitServiceProvider as ServiceProvider;

/**
 * Class PassportPgtClientServiceProvider
 *
 * @author James Carlo Luchavez <jamescarlo.luchavez@fligno.com>
 */
class PassportPgtClientServiceProvider extends ServiceProvider
{
    /**
     * Publishable Environment Variables
     *
     * @example [ 'HELLO_WORLD' => true ]
     *
     * @var array
     */
    protected array $env_vars = [
        'PPC_PGC_ID' => null,
        'PPC_PGC_SECRET' => null,
        'PPC_PASSPORT_URL' => '${APP_URL}',
    ];

    /**
     * Register any package services.
     *
     * @return void
     */
    public function register(): void
    {
        // Register the service the package provides.
        $this->app->singleton('passport-pgt-client', function ($app, $params) {
            return new PassportPgtClient(collect($params)->get('auth_client_controller'));
        });

        parent::register();
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides(): array
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

    /**
     * @param  bool  $is_api
     * @return array
     */
    public function getDefaultRouteMiddleware(bool $is_api): array
    {
        return []; // Must be blank since middleware should be setup on Passport PGT Server.
    }
}
