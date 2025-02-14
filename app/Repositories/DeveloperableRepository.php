<?php

namespace App\Repositories;

use App\Models\Developerable;
use App\Contracts\Repositories\DeveloperableRepositoryInterface;

class DeveloperableRepository extends AbstractRepository implements DeveloperableRepositoryInterface
{
    /**
     * The developerable repository.
     *
     * @return \App\Models\Developerable
     */
    public function model(): Developerable
    {
        return new Developerable();
    }
}
