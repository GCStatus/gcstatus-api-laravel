<?php

namespace App\Repositories;

use App\Models\Genreable;
use App\Contracts\Repositories\GenreableRepositoryInterface;

class GenreableRepository extends AbstractRepository implements GenreableRepositoryInterface
{
    /**
     * The genreable model.
     *
     * @return \App\Models\Genreable
     */
    public function model(): Genreable
    {
        return new Genreable();
    }
}
