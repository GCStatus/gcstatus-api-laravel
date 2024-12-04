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
     * Increment an balance on user wallet.
     *
     * @param mixed $id
     * @param int $amount
     * @return void
     */
    public function increment(mixed $id, int $amount): void
    {
        $this->findOrFail($id)->increment('balance', $amount);
    }

    /**
     * Decrement an balance on user wallet.
     *
     * @param mixed $id
     * @param int $amount
     * @return void
     */
    public function decrement(mixed $id, int $amount): void
    {
        $this->findOrFail($id)->decrement('balance', $amount);
    }
}
