<?php

namespace App\Repositories;

use App\Models\Wallet;
use App\Contracts\Repositories\WalletRepositoryInterface;

class WalletRepository extends AbstractRepository implements WalletRepositoryInterface
{
    /**
     * The wallet model.
     *
     * @return \App\Models\Wallet
     */
    public function model(): Wallet
    {
        return new Wallet();
    }

    /**
     * @inheritDoc
     */
    public function increment(Wallet $wallet, int $amount): void
    {
        $wallet->increment('balance', $amount);
    }

    /**
     * @inheritDoc
     */
    public function decrement(Wallet $wallet, int $amount): void
    {
        $wallet->decrement('balance', $amount);
    }
}
