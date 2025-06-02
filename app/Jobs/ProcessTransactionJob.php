<?php

namespace App\Jobs;

use App\Models\User;
use App\Models\Balance;
use App\Enums\TransactionType;
use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\DB;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class ProcessTransactionJob implements ShouldQueue
{
    use Dispatchable, Queueable, SerializesModels;

    public function __construct(
        protected int $userId,
        protected int $amount,
        protected string $type,
        protected \DateTime $date,
        protected ?string $description = null,
    ) {}

    public function handle(): void
    {
        DB::transaction(function () {
            $user = User::query()->findOrFail($this->userId);

            /** @var Balance $balance */
            $balance = $user->balance()->lockForUpdate()->first();

            if ($this->type === TransactionType::DEBIT->value && $balance->amount < $this->amount) {
                throw new \RuntimeException('Insufficient funds.');
            }

            $user->transactions()->create([
                'amount' => $this->amount,
                'type' => $this->type,
                'description' => $this->description,
                'created_at' => $this->date,
            ]);

            $balance->update([
                'amount' => $this->type === TransactionType::CREDIT->value
                    ? $balance->amount + $this->amount
                    : $balance->amount - $this->amount,
            ]);
        });
    }
}

