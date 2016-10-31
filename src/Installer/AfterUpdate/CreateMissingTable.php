<?php

namespace Mcms\Products\Installer\AfterUpdate;


use IdeaSeven\Core\Models\UpdatesLog;
use Illuminate\Console\Command;

class CreateMissingTable
{
    public function handle(Command $command, UpdatesLog $item)
    {
        $item->result = true;
        $item->save();
        $command->comment('All done in CreateMissingTable');
    }
}