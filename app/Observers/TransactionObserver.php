<?php

namespace App\Observers;

use App\Models\Transaction;
use App\Jobs\CalculateMissionProgressByKeyJob;

class TransactionObserver
{
    /**
     * Handle the Transaction "created" event.
     *
     * @param \App\Models\Transaction $transaction
     * @return void
     */
    public function created(Transaction $transaction): void
    {
        /** @var \App\Models\User $user */
        $user = $transaction->user;

        CalculateMissionProgressByKeyJob::dispatch('make_transactions', $user);
    }
}
