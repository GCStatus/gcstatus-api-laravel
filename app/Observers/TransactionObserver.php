<?php

namespace App\Observers;

use App\Jobs\CalculateMissionProgressByKeyJob;
use App\Models\{Transaction, MissionRequirement};

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

        CalculateMissionProgressByKeyJob::dispatch(
            MissionRequirement::TRANSACTIONS_COUNT_STRATEGY_KEY,
            $user,
        );
    }
}
