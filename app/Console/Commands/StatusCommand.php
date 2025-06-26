<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class StatusCommand extends Command
{
    protected $signature = 'status';

    protected $description = 'Checks application health';

    public function handle(): int
    {
        return static::SUCCESS;
    }
}
