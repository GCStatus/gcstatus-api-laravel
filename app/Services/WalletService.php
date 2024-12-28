<?php

namespace App\Services;

use App\Models\{TransactionType, User, Wallet};
use App\Contracts\Services\WalletServiceInterface;
use App\Contracts\Repositories\WalletRepositoryInterface;
use App\Exceptions\Wallet\WalletHasntBalanceEnoughException;

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
     * @inheritDoc
     */
    public function addFunds(User $user, int $amount, string $description): void
    {
        /** @var \App\Models\Wallet $wallet */
        $wallet = $user->wallet;

        $this->repository()->increment($wallet, $amount);

        $this->createOperationTransaction(
            $wallet,
            $amount,
            $description,
            TransactionType::ADDITION_TYPE_ID,
        );
    }

    /**
     * @inheritDoc
     */
    public function deductFunds(User $user, int $amount, string $description): void
    {
        /** @var \App\Models\Wallet $wallet */
        $wallet = $user->wallet;

        $this->assertCanDeduct($wallet, $amount);

        $this->repository()->decrement($wallet, $amount);

        $this->createOperationTransaction(
            $wallet,
            $amount,
            $description,
            TransactionType::SUBTRACTION_TYPE_ID,
        );
    }

    /**
     * Create a transaction for a wallet change.
     *
     * @param \App\Models\Wallet $wallet
     * @param int $amount
     * @param string $description
     * @return void
     */
    private function createOperationTransaction(
        Wallet $wallet,
        int $amount,
        string $description,
        int $type,
    ): void {
        /** @var \App\Models\User $user */
        $user = $wallet->user;

        transactionService()->create([
            'amount' => $amount,
            'user_id' => $user->id,
            'description' => $description,
            'transaction_type_id' => $type,
        ]);
    }

    /**
     * Assert can deduct funds.
     *
     * @param \App\Models\Wallet $wallet
     * @param int $amount
     * @throws \App\Exceptions\Wallet\WalletHasntBalanceEnoughException
     * @return void
     */
    private function assertCanDeduct(Wallet $wallet, int $amount): void
    {
        $wallet->refresh();

        if ($amount > $wallet->balance) {
            throw new WalletHasntBalanceEnoughException();
        }
    }
}
