<?php

namespace Mcms\Products;


use Mcms\Products\StartUp\RegisterAdminPackage;
use Mcms\Products\StartUp\RegisterEvents;
use Mcms\Products\StartUp\RegisterFacades;
use Mcms\Products\StartUp\RegisterMiddleware;
use Mcms\Products\StartUp\RegisterServiceProviders;
use Mcms\Products\StartUp\RegisterSettingsManager;
use Mcms\Products\StartUp\RegisterWidgets;
use Illuminate\Support\ServiceProvider;
use \App;
use \Installer, \Widget;
use Illuminate\Contracts\Events\Dispatcher as DispatcherContract;
use Illuminate\Contracts\Auth\Access\Gate as GateContract;
use Illuminate\Routing\Router;

class ProductsServiceProvider extends ServiceProvider
{
    /**
     * @var array
     */
    protected $commands = [
        \Mcms\Products\Console\Commands\Install::class,
        \Mcms\Products\Console\Commands\RefreshAssets::class,
    ];
    
    public $packageName = 'package-products';
    
    /**
     * Perform post-registration booting of services.
     *
     * @return void
     */
    public function boot(DispatcherContract $events, GateContract $gate, Router $router)
    {
        $this->publishes([
            __DIR__ . '/../config/config.php' => config_path('products.php'),
            __DIR__ . '/../config/product_settings.php' => config_path('product_settings.php'),
        ], 'config');

        $this->publishes([
            __DIR__ . '/../database/migrations/' => database_path('migrations')
        ], 'migrations');

        $this->publishes([
            __DIR__ . '/../database/seeds/' => database_path('seeds')
        ], 'seeds');

        $this->publishes([
            __DIR__ . '/../resources/views' => resource_path('views/products'),
        ], 'views');

        $this->publishes([
            __DIR__ . '/../resources/lang' => resource_path('lang'),
        ], 'lang');

        $this->publishes([
            __DIR__ . '/../resources/public' => public_path('vendor/mcms/products'),
        ], 'public');

        $this->publishes([
            __DIR__ . '/../resources/assets' => public_path('vendor/mcms/products'),
        ], 'assets');

        $this->publishes([
            __DIR__ . '/../config/admin.package.json' => storage_path('app/package-products/admin.package.json'),
        ], 'admin-package');
        

        if (!$this->app->routesAreCached()) {
            $router->group([
                'middleware' => 'web',
            ], function ($router) {
                require __DIR__.'/Http/routes.php';
            });

            $this->loadViewsFrom(__DIR__ . '/../resources/views', 'products');
        }

        /**
         * Register any widgets
         */
        (new RegisterWidgets())->handle();

        /**
         * Register Events
         */
//        parent::boot($events);
        (new RegisterEvents())->handle($this, $events);

        /*
         * Register dependencies
        */
        (new RegisterServiceProviders())->handle();

        /*
         * Register middleware
        */
        (new RegisterMiddleware())->handle($this, $router);


        /**
         * Register admin package
         */
        (new RegisterAdminPackage())->handle($this);

        (new RegisterSettingsManager())->handle($this);
    }

    /**
     * Register any package services.
     *
     * @return void
     */
    public function register()
    {
        /*
        * Register Commands
        */
        $this->commands($this->commands);

        /**
         * Register Facades
         */
        (new RegisterFacades())->handle($this);


        /**
         * Register installer
         */
        Installer::register(\Mcms\Products\Installer\Install::class);

    }
}
