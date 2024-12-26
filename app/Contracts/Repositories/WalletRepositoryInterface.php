<?php

namespace App\Contracts\Repositories;

use App\Models\Wallet;

interface WalletRepositoryInterface extends AbstractRepositoryInterface
{
    /**
     * Increment an amount on wallet.
     *
     * @param \App\Models\Wallet $wallet
     * @param int $amount
     * @return void
     */
    public function increment(Wallet $wallet, int $amount): void;

    /**
     * Decrement an amount on wallet.
     *
     * @param \App\Models\Wallet $wallet
     * @param int $amount
     * @return void
     */
    public function decrement(Wallet $wallet, int $amount): void;
}
