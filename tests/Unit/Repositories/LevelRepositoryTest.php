<?php

namespace Tests\Unit\Repositories;

use Mockery;
use Tests\TestCase;
use App\Models\Level;
use App\Contracts\Repositories\LevelRepositoryInterface;

class LevelRepositoryTest extends TestCase
{
    /**
     * The level repository.
     *
     * @var \App\Contracts\Repositories\LevelRepositoryInterface
     */
    private $levelRepository;

    /**
     * Setup new test environments.
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->levelRepository = app(LevelRepositoryInterface::class);
    }

    /**
     * Test if LevelRepository uses the Level model correctly.
     *
     * @return void
     */
    public function test_level_repository_uses_level_model(): void
    {
        /** @var \App\Repositories\LevelRepository $levelRepository */
        $levelRepository = $this->levelRepository;

        $this->assertInstanceOf(Level::class, $levelRepository->model());
    }

    /**
     * Cleanup Mockery after each test.
     *
     * @return void
     */
    public function tearDown(): void
    {
        Mockery::close();

        parent::tearDown();
    }
}
