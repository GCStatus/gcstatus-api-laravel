<?php

namespace Tests\Unit\Services;

use Tests\TestCase;
use App\Repositories\PlatformRepository;
use App\Contracts\Services\PlatformServiceInterface;
use App\Contracts\Repositories\PlatformRepositoryInterface;

class PlatformServiceTest extends TestCase
{
    /**
     * Test if PlatformService uses the Category repository correctly.
     *
     * @return void
     */
    public function test_Category_repository_uses_Category_repository(): void
    {
        $this->app->instance(PlatformRepositoryInterface::class, new PlatformRepository());

        /** @var \App\Services\PlatformService $platformService */
        $platformService = app(PlatformServiceInterface::class);

        $this->assertInstanceOf(PlatformRepository::class, $platformService->repository());
    }
}
