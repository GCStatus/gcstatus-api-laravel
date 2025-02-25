<?php

namespace App\Repositories;

use App\Models\TransactionType;
use App\Contracts\Repositories\TransactionTypeRepositoryInterface;

class TransactionTypeRepository extends AbstractRepository implements TransactionTypeRepositoryInterface
{
    /**
     * @inheritDoc
     */
    public function model(): TransactionType
    {
        return new TransactionType();
    }
}
