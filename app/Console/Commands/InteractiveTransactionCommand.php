<?php

namespace App\Console\Commands;

use App\Enums\TransactionType;
use App\Jobs\ProcessTransactionJob;
use App\Models\User;
use Illuminate\Console\Command;

class InteractiveTransactionCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'interactive:add {--sync : Run job synchronously}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Add a credit or debit transaction for a user';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $isSync = $this->option('sync');

        $isSync
            ? $this->info('Started process synchronously.')
            : $this->info('Started process async.');

        $user = $this->askValidEmail();
        $amount = $this->askValidAmount();
        $type = $this->askValidType();
        $description = $this->askValidDescription();

        if ($isSync) {
            ProcessTransactionJob::dispatchSync(
                userId: $user->id,
                amount: $amount,
                type: $type,
                description: $description,
                date: now()
            );

            $this->info(sprintf(
                "Transaction [%s] of %s processed synchronously for %s.",
                $type,
                number_format($amount / 100, 2),
                $user->email
            ));

            return self::SUCCESS;
        }

        ProcessTransactionJob::dispatch(
            userId: $user->id,
            amount: $amount,
            type: $type,
            description: $description,
            date: now()
        );

        $this->info(sprintf(
            "Transaction job [%s] of %s dispatched for %s. Check success on horizon monitoring.",
            $type,
            number_format($amount / 100, 2),
            $user->email
        ));

        return self::SUCCESS;
    }

    /**
     * @return User
     */
    protected function askValidEmail(): User
    {
        do {
            $email = $this->ask('User email');

            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $this->error('Invalid email format.');

                continue;
            }

            /** @var User $user */
            if (!$user = User::query()->where('email', $email)->first()) {
                $this->error(sprintf('User with email %s not found.', $email));

                continue;
            }

            return $user;
        } while (true);
    }

    /**
     * @return null|string
     */
    protected function askValidDescription(): ?string
    {
        do {
            $description = $this->ask('Description (max length 255)');

            if (strlen($description) >= 255) {
                $this->error('Max length 255.');

                continue;
            }

            return $description;
        } while (true);
    }

    /**
     * @return int
     */
    protected function askValidAmount(): int
    {
        do {
            $input = $this->ask('Amount in cents (e.g. 1234 = 12.34)');

            if (!ctype_digit($input) || (int)$input <= 0) {
                $this->error('Amount must be a positive integer (in cents).');

                continue;
            }

            return (int) $input;
        } while (true);
    }

    /**
     * @return string
     */
    protected function askValidType(): string
    {
        do {
            $input = $this->ask('Type: 1 - credit / 0 - debit');

            if ($input === '1') {
                return TransactionType::CREDIT->value;
            }

            if ($input === '0') {
                return TransactionType::DEBIT->value;
            }

            $this->error('Please enter 1 for credit or 0 for debit.');
        } while (true);
    }
}
