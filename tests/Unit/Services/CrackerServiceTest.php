<?php

namespace Tests\Unit\Services;

use Tests\TestCase;
use App\Repositories\CrackerRepository;
use App\Contracts\Services\CrackerServiceInterface;
use App\Contracts\Repositories\CrackerRepositoryInterface;

class CrackerServiceTest extends TestCase
{
    /**
     * Test if CrackerService uses the Category repository correctly.
     *
     * @return void
     */
    public function test_Category_repository_uses_Category_repository(): void
    {
        $this->app->instance(CrackerRepositoryInterface::class, new CrackerRepository());

        /** @var \App\Services\CrackerService $crackerService */
        $crackerService = app(CrackerServiceInterface::class);

        $this->assertInstanceOf(CrackerRepository::class, $crackerService->repository());
    }
}
