<?php

namespace App\Contracts\Services;

use App\Models\{User, Transaction};

interface TransactionNotificationServiceInterface
{
    /**
     * Send a new transaction notification for given user.
     *
     * @param \App\Models\User $user
     * @param \App\Models\Transaction $transaction
     * @return void
     */
    public function notifyNewTransaction(User $user, Transaction $transaction): void;
}
