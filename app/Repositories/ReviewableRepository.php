<?php

namespace App\Repositories;

use App\Models\Reviewable;
use App\Contracts\Repositories\ReviewableRepositoryInterface;

class ReviewableRepository extends AbstractRepository implements ReviewableRepositoryInterface
{
    /**
     * The reviewable model.
     *
     * @return \App\Models\Reviewable
     */
    public function model(): Reviewable
    {
        return new Reviewable();
    }
}
