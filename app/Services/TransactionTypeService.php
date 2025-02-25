<?php

namespace App\Services;

use App\Contracts\Services\TransactionTypeServiceInterface;
use App\Contracts\Repositories\TransactionTypeRepositoryInterface;

class TransactionTypeService extends AbstractService implements TransactionTypeServiceInterface
{
    /**
     * @inheritDoc
     */
    public function repository(): TransactionTypeRepositoryInterface
    {
        return app(TransactionTypeRepositoryInterface::class);
    }
}
