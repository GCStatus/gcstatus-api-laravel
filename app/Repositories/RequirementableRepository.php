<?php

namespace App\Repositories;

use App\Models\Requirementable;
use App\Contracts\Repositories\RequirementableRepositoryInterface;

class RequirementableRepository extends AbstractRepository implements RequirementableRepositoryInterface
{
    /**
     * The requirementable model.
     *
     * @return \App\Models\Requirementable
     */
    public function model(): Requirementable
    {
        return new Requirementable();
    }
}
