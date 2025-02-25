<?php

namespace Tests\Unit\Services;

use Tests\TestCase;
use App\Repositories\CriticRepository;
use App\Contracts\Services\CriticServiceInterface;
use App\Contracts\Repositories\CriticRepositoryInterface;

class CriticServiceTest extends TestCase
{
    /**
     * Test if CriticService uses the Category repository correctly.
     *
     * @return void
     */
    public function test_Category_repository_uses_Category_repository(): void
    {
        $this->app->instance(CriticRepositoryInterface::class, new CriticRepository());

        /** @var \App\Services\CriticService $criticService */
        $criticService = app(CriticServiceInterface::class);

        $this->assertInstanceOf(CriticRepository::class, $criticService->repository());
    }
}
