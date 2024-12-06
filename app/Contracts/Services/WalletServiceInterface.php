<?php

namespace App\Contracts\Services;

use App\Models\User;

interface WalletServiceInterface extends AbstractServiceInterface
{
    /**
     * Add funds to given user wallet.
     *
     * @param \App\Models\User $user
     * @param int $amount
     * @return void
     */
    public function addFunds(User $user, int $amount): void;

    /**
     * Deduct funds from given user wallet.
     *
     * @param \App\Models\User $user
     * @param int $amount
     * @return void
     */
    public function deductFunds(User $user, int $amount): void;
}
