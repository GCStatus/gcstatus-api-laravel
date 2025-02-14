<?php

namespace App\Services;

use App\Contracts\Services\StoreServiceInterface;
use App\Contracts\Repositories\StoreRepositoryInterface;

class StoreService extends AbstractService implements StoreServiceInterface
{
    /**
     * The store repository.
     *
     * @return \App\Contracts\Repositories\StoreRepositoryInterface
     */
    public function repository(): StoreRepositoryInterface
    {
        return app(StoreRepositoryInterface::class);
    }
}
