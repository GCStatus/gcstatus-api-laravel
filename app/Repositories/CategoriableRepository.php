<?php

namespace App\Repositories;

use App\Models\Categoriable;
use App\Contracts\Repositories\CategoriableRepositoryInterface;

class CategoriableRepository extends AbstractRepository implements CategoriableRepositoryInterface
{
    /**
     * The categoriable repository.
     *
     * @return \App\Models\Categoriable
     */
    public function model(): Categoriable
    {
        return new Categoriable();
    }
}
