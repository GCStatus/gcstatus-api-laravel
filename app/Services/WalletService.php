<?php

namespace App\Services;

use App\Models\User;
use App\Contracts\Services\WalletServiceInterface;
use App\Contracts\Repositories\WalletRepositoryInterface;

class WalletService extends AbstractService implements WalletServiceInterface
{
    /**
     * The wallet repository.
     *
     * @return \App\Contracts\Repositories\WalletRepositoryInterface
     */
    public function repository(): WalletRepositoryInterface
    {
        return app(WalletRepositoryInterface::class);
    }

    /**
     * Add funds to given user wallet.
     *
     * @param \App\Models\User $user
     * @param int $amount
     * @return void
     */
    public function addFunds(User $user, int $amount): void
    {
        /** @var \App\Models\Wallet $wallet */
        $wallet = $this->repository()->findBy('user_id', $user->id);

        $this->repository()->increment($wallet->id, $amount);
    }

    /**
     * Deduct funds from given user wallet.
     *
     * @param \App\Models\User $user
     * @param int $amount
     * @return void
     */
    public function deductFunds(User $user, int $amount): void
    {
        /** @var \App\Models\Wallet $wallet */
        $wallet = $this->repository()->findBy('user_id', $user->id);

        $this->repository()->decrement($wallet->id, $amount);
    }
}
