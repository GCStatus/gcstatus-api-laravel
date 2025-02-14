<?php

namespace Tests\Unit\Repositories;

use Tests\TestCase;
use App\Models\Store;
use App\Contracts\Repositories\StoreRepositoryInterface;

class StoreRepositoryTest extends TestCase
{
    /**
     * The Store repository.
     *
     * @var \App\Contracts\Repositories\StoreRepositoryInterface
     */
    private StoreRepositoryInterface $storeRepository;

    /**
     * Setup new test environments.
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->storeRepository = app(StoreRepositoryInterface::class);
    }

    /**
     * Test if StoreRepository uses the Store model correctly.
     *
     * @return void
     */
    public function test_Store_repository_uses_Store_model(): void
    {
        /** @var \App\Repositories\StoreRepository $storeRepository */
        $storeRepository = $this->storeRepository;

        $this->assertInstanceOf(Store::class, $storeRepository->model());
    }
}
