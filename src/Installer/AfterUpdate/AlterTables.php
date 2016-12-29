<?php

namespace Mcms\Products\Installer\AfterUpdate;

use Mcms\Core\Models\UpdatesLog;
use Illuminate\Console\Command;
use Products\Core\Installer\AfterUpdate\AlterTables\AddSkuToProducts;

class AlterTables
{
    public function handle(Command $command, UpdatesLog $item)
    {
        $classes = [
            AddSkuToProducts::class
        ];

        foreach ($classes as $class) {
            (new $class())->handle($command);
        }


        $item->result = true;
        $item->save();
        $command->comment('All done in AlterTables');
    }
}