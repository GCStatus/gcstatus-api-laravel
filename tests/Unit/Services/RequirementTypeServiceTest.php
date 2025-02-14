<?php

namespace Tests\Unit\Services;

use Tests\TestCase;
use App\Repositories\RequirementTypeRepository;
use App\Contracts\Services\RequirementTypeServiceInterface;
use App\Contracts\Repositories\RequirementTypeRepositoryInterface;

class RequirementTypeServiceTest extends TestCase
{
    /**
     * Test if RequirementableService uses the Requirementable repository correctly.
     *
     * @return void
     */
    public function test_Requirementable_repository_uses_Requirementable_repository(): void
    {
        $this->app->instance(RequirementTypeRepositoryInterface::class, new RequirementTypeRepository());

        /** @var \App\Services\RequirementTypeService $requirementTypeService */
        $requirementTypeService = app(RequirementTypeServiceInterface::class);

        $this->assertInstanceOf(RequirementTypeRepository::class, $requirementTypeService->repository());
    }
}
