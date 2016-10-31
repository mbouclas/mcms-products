<?php

namespace Mcms\Products\StartUp;


use Mcms\Products\Menu\ProductsInterfaceMenuConnector;
use Mcms\Products\Models\Product;
use Illuminate\Support\ServiceProvider;
use ModuleRegistry, ItemConnector;

class RegisterAdminPackage
{
    public function handle(ServiceProvider $serviceProvider)
    {
        ModuleRegistry::registerModule($serviceProvider->packageName . '/admin.package.json');
        try {
            ItemConnector::register((new ProductsInterfaceMenuConnector())->run()->toArray());
        } catch (\Exception $e){

        }
    }
}