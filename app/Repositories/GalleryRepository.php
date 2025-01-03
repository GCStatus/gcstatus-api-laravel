<?php

namespace App\Repositories;

use App\Models\Galleriable;
use App\Contracts\Repositories\GalleryRepositoryInterface;

class GalleryRepository extends AbstractRepository implements GalleryRepositoryInterface
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
