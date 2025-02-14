<?php

namespace Tests\Unit\Repositories;

use Tests\TestCase;
use App\Models\RequirementType;
use App\Contracts\Repositories\RequirementTypeRepositoryInterface;

class RequirementTypeRepositoryTest extends TestCase
{
    /**
     * The RequirementType repository.
     *
     * @var \App\Contracts\Repositories\RequirementTypeRepositoryInterface
     */
    private RequirementTypeRepositoryInterface $requirementTypeRepository;

    /**
     * Setup new test environments.
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->requirementTypeRepository = app(RequirementTypeRepositoryInterface::class);
    }

    /**
     * Test if RequirementTypeRepository uses the RequirementType model correctly.
     *
     * @return void
     */
    public function test_RequirementType_repository_uses_RequirementType_model(): void
    {
        /** @var \App\Repositories\RequirementTypeRepository $requirementTypeRepository */
        $requirementTypeRepository = $this->requirementTypeRepository;

        $this->assertInstanceOf(RequirementType::class, $requirementTypeRepository->model());
    }
}
