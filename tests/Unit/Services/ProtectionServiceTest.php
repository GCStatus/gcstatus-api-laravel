<?php

namespace Tests\Unit\Services;

use Tests\TestCase;
use App\Repositories\ProtectionRepository;
use App\Contracts\Services\ProtectionServiceInterface;
use App\Contracts\Repositories\ProtectionRepositoryInterface;

class ProtectionServiceTest extends TestCase
{
    /**
     * Test if ProtectionService uses the Category repository correctly.
     *
     * @return void
     */
    public function test_Category_repository_uses_Category_repository(): void
    {
        $this->app->instance(ProtectionRepositoryInterface::class, new ProtectionRepository());

        /** @var \App\Services\ProtectionService $protectionService */
        $protectionService = app(ProtectionServiceInterface::class);

        $this->assertInstanceOf(ProtectionRepository::class, $protectionService->repository());
    }
}
