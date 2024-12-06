<?php

namespace App\Contracts\Repositories;

interface WalletRepositoryInterface extends AbstractRepositoryInterface
{
    /**
     * Increment an amount on wallet.
     *
     * @param mixed $id
     * @param int $amount
     * @return void
     */
    public function increment(mixed $id, int $amount): void;

    /**
     * Decrement an amount on wallet.
     *
     * @param mixed $id
     * @param int $amount
     * @return void
     */
    public function decrement(mixed $id, int $amount): void;
}
