<?php

namespace Mcms\Products\StartUp;

use Mcms\Core\Services\SettingsManager\SettingsManagerService;
use Illuminate\Support\ServiceProvider;

class RegisterSettingsManager
{
    public function handle(ServiceProvider $serviceProvider)
    {
        SettingsManagerService::register('products', 'product_settings.products');
        SettingsManagerService::register('productCategories', 'product_settings.categories');
    }
}