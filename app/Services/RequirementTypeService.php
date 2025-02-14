<?php

namespace App\Services;

use App\Contracts\Services\RequirementTypeServiceInterface;
use App\Contracts\Repositories\RequirementTypeRepositoryInterface;

class RequirementTypeService extends AbstractService implements RequirementTypeServiceInterface
{
    /**
     * The requirement type repository.
     *
     * @return \App\Contracts\Repositories\RequirementTypeRepositoryInterface
     */
    public function repository(): RequirementTypeRepositoryInterface
    {
        return app(RequirementTypeRepositoryInterface::class);
    }
}
