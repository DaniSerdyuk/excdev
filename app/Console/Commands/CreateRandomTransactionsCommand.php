<?php

namespace App\Console\Commands;

use App\Enums\TransactionType;
use App\Jobs\ProcessTransactionJob;
use Random\RandomException;
use Faker\Factory as Faker;

class CreateRandomTransactionsCommand extends InteractiveTransactionCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'transaction:many';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create many new transactions for a user';

    /**
     * Execute the console command.
     *
     * @throws RandomException
     */
    public function handle(): int
    {
        $user = $this->askValidEmail();
        $types = [TransactionType::DEBIT->value, TransactionType::CREDIT->value];
        $faker = Faker::create();

        for ($i = 0; $i < 20; $i++) {
            $key = array_rand([TransactionType::DEBIT->value, TransactionType::CREDIT->value]);

            ProcessTransactionJob::dispatch(
                userId: $user->id,
                amount: random_int(20000, 8000000),
                type: $types[$key],
                description: $faker->sentence(),
                date: $faker->dateTime(),
            );
        }

        $this->info('Seeding complete.');

        return self::SUCCESS;
    }
}
