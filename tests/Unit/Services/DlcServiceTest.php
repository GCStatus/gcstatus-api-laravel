<?php

namespace Tests\Unit\Services;

use Tests\TestCase;
use App\Repositories\DlcRepository;
use App\Contracts\Services\DlcServiceInterface;
use App\Contracts\Repositories\DlcRepositoryInterface;

class DlcServiceTest extends TestCase
{
    /**
     * Test if DlcService uses the Dlc repository correctly.
     *
     * @return void
     */
    public function test_Dlc_repository_uses_Dlc_repository(): void
    {
        $this->app->instance(DlcRepositoryInterface::class, new DlcRepository());

        /** @var \App\Services\DlcService $DlcService */
        $DlcService = app(DlcServiceInterface::class);

        $this->assertInstanceOf(DlcRepository::class, $DlcService->repository());
    }
}
