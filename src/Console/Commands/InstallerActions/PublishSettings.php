<?php

namespace Mcms\Products\Console\Commands\InstallerActions;


use Illuminate\Console\Command;


/**
 * @example php artisan vendor:publish --provider="Mcms\Products\ProductsServiceProvider" --tag=config
 * Class PublishSettings
 * @package Mcms\Products\Console\Commands\InstallerActions
 */
class PublishSettings
{
    /**
     * @param Command $command
     */
    public function handle(Command $command)
    {
        $command->call('vendor:publish', [
            '--provider' => 'Mcms\Products\ProductsServiceProvider',
            '--tag' => ['config'],
        ]);

        $command->comment('* Settings published');
    }
}