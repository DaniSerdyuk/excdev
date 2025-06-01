<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

class CreateUsersCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'user:many';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create many new users';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        Artisan::call('db:seed', ['--force' => true]);

        $this->info('Seeding complete.');

        return self::SUCCESS;
    }
}
