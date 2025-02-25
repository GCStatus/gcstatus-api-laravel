<?php

namespace Tests\Unit\Repositories;

use Tests\TestCase;
use App\Models\Cracker;
use App\Contracts\Repositories\CrackerRepositoryInterface;

class CrackerRepositoryTest extends TestCase
{
    /**
     * The Cracker repository.
     *
     * @var \App\Contracts\Repositories\CrackerRepositoryInterface
     */
    private CrackerRepositoryInterface $crackerRepository;

    /**
     * Setup new test environments.
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->crackerRepository = app(CrackerRepositoryInterface::class);
    }

    /**
     * Test if CrackerRepository uses the Cracker model correctly.
     *
     * @return void
     */
    public function test_Cracker_repository_uses_Cracker_model(): void
    {
        /** @var \App\Repositories\CrackerRepository $crackerRepository */
        $crackerRepository = $this->crackerRepository;

        $this->assertInstanceOf(Cracker::class, $crackerRepository->model());
    }
}
