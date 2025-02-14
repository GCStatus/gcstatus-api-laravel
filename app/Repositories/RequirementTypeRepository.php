<?php

namespace App\Repositories;

use App\Models\RequirementType;
use App\Contracts\Repositories\RequirementTypeRepositoryInterface;

class RequirementTypeRepository extends AbstractRepository implements RequirementTypeRepositoryInterface
{
    /**
     * The requirement type model.
     *
     * @return \App\Models\RequirementType
     */
    public function model(): RequirementType
    {
        return new RequirementType();
    }
}
