<?php

namespace Tests\Unit\Repositories;

use Tests\TestCase;
use App\Models\Dlc;
use App\Contracts\Repositories\DlcRepositoryInterface;

class DlcRepositoryTest extends TestCase
{
    /**
     * The dlc repository.
     *
     * @var \App\Contracts\Repositories\DlcRepositoryInterface
     */
    private DlcRepositoryInterface $dlcRepository;

    /**
     * Setup new test environments.
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->dlcRepository = app(DlcRepositoryInterface::class);
    }

    /**
     * Test if DlcRepository uses the Dlc model correctly.
     *
     * @return void
     */
    public function test_Dlc_repository_uses_Dlc_model(): void
    {
        /** @var \App\Repositories\DlcRepository $dlcRepository */
        $dlcRepository = $this->dlcRepository;

        $this->assertInstanceOf(Dlc::class, $dlcRepository->model());
    }
}
