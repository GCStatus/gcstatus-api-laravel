<?php

namespace App\Services;

use App\Models\{User, Transaction};
use App\Notifications\DatabaseNotification;
use App\Contracts\Services\TransactionNotificationServiceInterface;

class TransactionNotificationService implements TransactionNotificationServiceInterface
{
    /**
     * @inheritDoc
     */
    public function notifyNewTransaction(User $user, Transaction $transaction): void
    {
        $notification = [
            'userId' => (string)$user->id,
            'icon' => 'FaDollarSign',
            'title' => 'You have a new transaction.',
            'actionUrl' => "/profile/?section=transactions&id={$transaction->id}",
        ];

        $user->notify(new DatabaseNotification($notification));
    }
}
