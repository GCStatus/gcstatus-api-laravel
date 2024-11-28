<?php

namespace Tests\Traits;

use App\Models\{User, Transaction};
use Illuminate\Database\Eloquent\Collection;

trait HasDummyTransaction
{
    /**
     * Create dummy transaction.
     *
     * @param array<string, mixed> $data
     * @return \App\Models\Transaction
     */
    public function createDummyTransaction(array $data = []): Transaction
    {
        /** @var \App\Models\Transaction $transaction */
        $transaction = Transaction::factory()->create($data);

        return $transaction;
    }

    /**
     * Create dummy transactions.
     *
     * @param int $times
     * @param array<string, mixed> $data
     * @return \Illuminate\Database\Eloquent\Collection<int, Transaction>
     */
    public function createDummyTransactions(int $times, array $data = []): Collection
    {
        return Transaction::factory($times)->create($data);
    }

    /**
     * Create a dummy transaction to given user.
     *
     * @param \App\Models\User $user
     * @param array<string, mixed> $data
     * @return \App\Models\Transaction
     */
    public function createDummyTransactionToUser(User $user, array $data = []): Transaction
    {
        $transaction = $this->createDummyTransaction($data);

        $transaction->user()->associate($user)->save();

        return $transaction;
    }
}
