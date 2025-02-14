<?php

namespace App\Repositories;

use App\Models\Publisherable;
use App\Contracts\Repositories\PublisherableRepositoryInterface;

class PublisherableRepository extends AbstractRepository implements PublisherableRepositoryInterface
{
    /**
     * The publisherable model.
     *
     * @return \App\Models\Publisherable
     */
    public function model(): Publisherable
    {
        return new Publisherable();
    }
}
