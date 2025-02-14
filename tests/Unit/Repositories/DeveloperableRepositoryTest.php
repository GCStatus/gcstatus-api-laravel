<?php

namespace Tests\Unit\Repositories;

use Tests\TestCase;
use App\Models\Developerable;
use App\Contracts\Repositories\DeveloperableRepositoryInterface;

class DeveloperableRepositoryTest extends TestCase
{
    /**
     * The developerable repository.
     *
     * @var \App\Contracts\Repositories\DeveloperableRepositoryInterface
     */
    private DeveloperableRepositoryInterface $developerableRepository;

    /**
     * Setup new test environments.
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->developerableRepository = app(DeveloperableRepositoryInterface::class);
    }

    /**
     * Test if DeveloperableRepository uses the Developerable model correctly.
     *
     * @return void
     */
    public function test_Developerable_repository_uses_Developerable_model(): void
    {
        /** @var \App\Repositories\DeveloperableRepository $developerableRepository */
        $developerableRepository = $this->developerableRepository;

        $this->assertInstanceOf(Developerable::class, $developerableRepository->model());
    }
}
