<?php

namespace App\Services;

use App\Contracts\Services\CategoryServiceInterface;
use App\Contracts\Repositories\CategoryRepositoryInterface;

class CategoryService extends AbstractService implements CategoryServiceInterface
{
    /**
     * The category repository.
     *
     * @return \App\Contracts\Repositories\CategoryRepositoryInterface
     */
    public function repository(): CategoryRepositoryInterface
    {
        return app(CategoryRepositoryInterface::class);
    }
}
