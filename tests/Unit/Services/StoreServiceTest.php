<?php

namespace Tests\Unit\Services;

use Tests\TestCase;
use App\Repositories\StoreRepository;
use App\Contracts\Services\StoreServiceInterface;
use App\Contracts\Repositories\StoreRepositoryInterface;

class StoreServiceTest extends TestCase
{
    /**
     * Test if RequirementableService uses the Requirementable repository correctly.
     *
     * @return void
     */
    public function test_Requirementable_repository_uses_Requirementable_repository(): void
    {
        $this->app->instance(StoreRepositoryInterface::class, new StoreRepository());

        /** @var \App\Services\StoreService $storeService */
        $storeService = app(StoreServiceInterface::class);

        $this->assertInstanceOf(StoreRepository::class, $storeService->repository());
    }
}
