<?php

namespace Tests\Unit\Repositories;

use Tests\TestCase;
use App\Models\Platform;
use App\Contracts\Repositories\PlatformRepositoryInterface;

class PlatformRepositoryTest extends TestCase
{
    /**
     * The Platform repository.
     *
     * @var \App\Contracts\Repositories\PlatformRepositoryInterface
     */
    private PlatformRepositoryInterface $platformRepository;

    /**
     * Setup new test environments.
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->platformRepository = app(PlatformRepositoryInterface::class);
    }

    /**
     * Test if PlatformRepository uses the Platform model correctly.
     *
     * @return void
     */
    public function test_Platform_repository_uses_Platform_model(): void
    {
        /** @var \App\Repositories\PlatformRepository $platformRepository */
        $platformRepository = $this->platformRepository;

        $this->assertInstanceOf(Platform::class, $platformRepository->model());
    }
}
