<?php

namespace App\Repositories;

use App\Models\Storeable;
use App\Contracts\Repositories\StoreableRepositoryInterface;

class StoreableRepository extends AbstractRepository implements StoreableRepositoryInterface
{
    /**
     * The storeable model.
     *
     * @return \App\Models\Storeable
     */
    public function model(): Storeable
    {
        return new Storeable();
    }
}
