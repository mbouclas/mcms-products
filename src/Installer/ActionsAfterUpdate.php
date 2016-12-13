<?php

namespace Mcms\Products\Installer;


use Mcms\Products\Installer\AfterUpdate\CreateMissingTable;
use Mcms\Products\Installer\AfterUpdate\PublishMissingConfig;
use Mcms\Products\Installer\AfterUpdate\PublishMissingMigrations;
use Mcms\Core\Exceptions\ErrorDuringUpdateException;
use Mcms\Core\Helpers\Installer;
use Mcms\Core\UpdatesLog\UpdatesLog;
use Illuminate\Console\Command;

class ActionsAfterUpdate
{
    protected $module;
    protected $version;

    public function __construct()
    {
        $this->module = 'package-products';
        $this->version = 1;
    }

    public function handle(Command $command)
    {
        /*
         * publish the missing migrations
         * publish the missing config
         * create the missing table media_library
         */
/*        ALTER TABLE `lara`.`related`
ADD COLUMN `dest_model` VARCHAR(100) NOT NULL AFTER `updated_at`;*/

        $actions = [
            'PublishMissingMigrations' => PublishMissingMigrations::class,
            'PublishMissingConfig' => PublishMissingConfig::class,
            'CreateMissingTable' => CreateMissingTable::class,
        ];

        try {
            (new UpdatesLog($command, $this->module, $actions, $this->version))->process();
        }
        catch (ErrorDuringUpdateException $e){
            $command->error('Error during updating ' . $this->module);
        }

        return true;
    }
}