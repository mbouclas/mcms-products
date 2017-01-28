<?php

namespace Mcms\Products\StartUp;



use Mcms\Products\Middleware\PublishProduct;
use Illuminate\Routing\Router;
use Illuminate\Support\ServiceProvider;

/**
 * Class RegisterMiddleware
 * @package Mcms\Products\StartUp
 */
class RegisterMiddleware
{

    /**
     * Register all your middleware here
     * @param ServiceProvider $serviceProvider
     * @param Router $router
     */
    public function handle(ServiceProvider $serviceProvider, Router $router)
    {
        $router->aliasMiddleware('publishProduct', PublishProduct::class);
    }
}