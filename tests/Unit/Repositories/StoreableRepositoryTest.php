<?php

namespace Tests\Unit\Repositories;

use Tests\TestCase;
use App\Models\Storeable;
use App\Contracts\Repositories\StoreableRepositoryInterface;

class StoreableRepositoryTest extends TestCase
{
    /**
     * The Storeable repository.
     *
     * @var \App\Contracts\Repositories\StoreableRepositoryInterface
     */
    private StoreableRepositoryInterface $storeableRepository;

    /**
     * Setup new test environments.
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->storeableRepository = app(StoreableRepositoryInterface::class);
    }

    /**
     * Test if StoreableRepository uses the Storeable model correctly.
     *
     * @return void
     */
    public function test_Storeable_repository_uses_Storeable_model(): void
    {
        /** @var \App\Repositories\StoreableRepository $storeableRepository */
        $storeableRepository = $this->storeableRepository;

        $this->assertInstanceOf(Storeable::class, $storeableRepository->model());
    }
}
