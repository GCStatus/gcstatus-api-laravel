<?php

namespace App\Repositories;

use App\Models\Store;
use App\Contracts\Repositories\StoreRepositoryInterface;

class StoreRepository extends AbstractRepository implements StoreRepositoryInterface
{
    /**
     * The store model.
     *
     * @return \App\Models\Store
     */
    public function model(): Store
    {
        return new Store();
    }
}
