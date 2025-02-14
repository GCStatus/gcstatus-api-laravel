<?php

namespace Tests\Unit\Repositories;

use Tests\TestCase;
use App\Models\Requirementable;
use App\Contracts\Repositories\RequirementableRepositoryInterface;

class RequirementableRepositoryTest extends TestCase
{
    /**
     * The Requirementable repository.
     *
     * @var \App\Contracts\Repositories\RequirementableRepositoryInterface
     */
    private RequirementableRepositoryInterface $requirementableRepository;

    /**
     * Setup new test environments.
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->requirementableRepository = app(RequirementableRepositoryInterface::class);
    }

    /**
     * Test if RequirementableRepository uses the Requirementable model correctly.
     *
     * @return void
     */
    public function test_Requirementable_repository_uses_Requirementable_model(): void
    {
        /** @var \App\Repositories\RequirementableRepository $requirementableRepository */
        $requirementableRepository = $this->requirementableRepository;

        $this->assertInstanceOf(Requirementable::class, $requirementableRepository->model());
    }
}
