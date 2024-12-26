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
     * @param string $description
     * @return void
     */
    public function addFunds(User $user, int $amount, string $description): void;

    /**
     * Deduct funds from given user wallet.
     *
     * @param \App\Models\User $user
     * @param int $amount
     * @param string $description
     * @return void
     */
    public function deductFunds(User $user, int $amount, string $description): void;
}
