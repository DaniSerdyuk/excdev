<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;

class CreateUserCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'user:create';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new user';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $first = $this->ask('First name');
        $last = $this->ask('Last name');
        $email = $this->askValidEmail();
        $password = $this->secret('Password (leave blank for "secret")') ?: 'secret';

        $user = User::query()->create([
            'first_name' => $first,
            'last_name' => $last,
            'email' => $email,
            'password' => Hash::make($password),
        ]);

        $user->balance()->create(['amount' => 0]);

        $this->info(sprintf('User created: %s', $user->email));

        return self::SUCCESS;
    }

    /**
     * @return string
     */
    private function askValidEmail(): string
    {
        do {
            $email = $this->ask('Email');

            if (! filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $this->error('Invalid email format.');

                continue;
            }

            if (User::query()->where('email', $email)->exists()) {
                $this->error(sprintf('Email %s already exists.', $email));

                continue;
            }

            return $email;

        } while (true);
    }
}
