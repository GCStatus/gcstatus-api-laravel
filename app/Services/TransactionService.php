<?php

namespace App\Services;

use App\Models\{User, Transaction};
use Illuminate\Database\Eloquent\Collection;
use App\Contracts\Services\TransactionServiceInterface;
use App\Exceptions\User\ResourceDoesntBelongsToUserException;
use App\Contracts\Repositories\TransactionRepositoryInterface;

class TransactionService extends AbstractService implements TransactionServiceInterface
{
    /**
     * Get the repository instance.
     *
     * @return \App\Contracts\Repositories\TransactionRepositoryInterface
     */
    public function repository(): TransactionRepositoryInterface
    {
        return app(TransactionRepositoryInterface::class);
    }

    /**
     * Get all transactions for user.
     *
     * @return \Illuminate\Database\Eloquent\Collection<int, \Illuminate\Database\Eloquent\Model>
     */
    public function allForAuth(User $user): Collection
    {
        return $this->repository()->allForAuth($user);
    }

    /**
     * Delete a specific transaction for user.
     *
     * @param \App\Models\User $user
     * @param mixed $id
     * @return void
     */
    public function deleteForUser(User $user, mixed $id): void
    {
        /** @var \App\Models\Transaction $transaction */
        $transaction = $this->repository()->findOrFail($id);

        $this->assertCanAct($user, $transaction);

        $transaction->delete();
    }

    /**
     * Check if transaction belongs to user.
     *
     * @param \App\Models\User $user
     * @param \App\Models\Transaction $transaction
     * @throws \App\Exceptions\User\ResourceDoesntBelongsToUserException
     * @return void
     */
    private function assertCanAct(User $user, Transaction $transaction): void
    {
        if ($transaction->user_id != $user->id) {
            throw new ResourceDoesntBelongsToUserException();
        }
    }
}
