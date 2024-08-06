<?php

namespace Spatie\LaravelPasskeys\Commands;

use Illuminate\Console\Command;

class LaravelPasskeysCommand extends Command
{
    public $signature = 'laravel-passkeys';

    public $description = 'My command';

    public function handle(): int
    {
        $this->comment('All done');

        return self::SUCCESS;
    }
}
