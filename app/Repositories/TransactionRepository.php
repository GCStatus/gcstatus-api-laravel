<?php

namespace App\Repositories;

use App\Models\{User, Transaction};
use Illuminate\Database\Eloquent\Collection;
use App\Contracts\Repositories\TransactionRepositoryInterface;

class TransactionRepository extends AbstractRepository implements TransactionRepositoryInterface
{
    /**
     * The transaction model.
     *
     * @return \App\Models\Transaction
     */
    public function model(): Transaction
    {
        return new Transaction();
    }

    /**
     * Get all transactions for user.
     *
     * @return \Illuminate\Database\Eloquent\Collection<int, \Illuminate\Database\Eloquent\Model>
     */
    public function allForAuth(User $user): Collection
    {
        return $this->findAllBy('user_id', $user->id);
    }
}
