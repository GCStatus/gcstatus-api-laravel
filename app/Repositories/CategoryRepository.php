<?php

namespace App\Repositories;

use App\Models\Category;
use App\Contracts\Repositories\CategoryRepositoryInterface;

class CategoryRepository extends AbstractRepository implements CategoryRepositoryInterface
{
    /**
     * The category repository.
     *
     * @return \App\Models\Category
     */
    public function model(): Category
    {
        return new Category();
    }
}
