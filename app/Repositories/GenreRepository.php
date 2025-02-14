<?php

namespace App\Repositories;

use App\Models\Genre;
use App\Contracts\Repositories\GenreRepositoryInterface;

class GenreRepository extends AbstractRepository implements GenreRepositoryInterface
{
    /**
     * The genre model.
     *
     * @return \App\Models\Genre
     */
    public function model(): Genre
    {
        return new Genre();
    }
}
