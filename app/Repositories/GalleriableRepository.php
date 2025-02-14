<?php

namespace App\Repositories;

use App\Models\Galleriable;
use App\Contracts\Repositories\GalleriableRepositoryInterface;

class GalleriableRepository extends AbstractRepository implements GalleriableRepositoryInterface
{
    /**
     * The gallery model.
     *
     * @return \App\Models\Galleriable
     */
    public function model(): Galleriable
    {
        return new Galleriable();
    }
}
