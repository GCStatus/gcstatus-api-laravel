<?php

namespace App\Observers;

use App\Jobs\CalculateMissionProgressByKeyJob;
use App\Models\{Transaction, MissionRequirement};
use App\Contracts\Services\TransactionNotificationServiceInterface;

class TransactionObserver
{
    /**
     * The transaction notification service.
     *
     * @var \App\Contracts\Services\TransactionNotificationServiceInterface
     */
    private TransactionNotificationServiceInterface $transactionNotificationService;

    /**
     * Create a new class instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->transactionNotificationService = app(TransactionNotificationServiceInterface::class);
    }

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

        $this->transactionNotificationService->notifyNewTransaction($user, $transaction);

        CalculateMissionProgressByKeyJob::dispatch(
            MissionRequirement::TRANSACTIONS_COUNT_STRATEGY_KEY,
            $user,
        );
    }
}
