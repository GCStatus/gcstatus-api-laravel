<?php

namespace Tests\Unit\Repositories;

use Mockery;
use Tests\TestCase;
use App\Models\Developer;
use App\Repositories\DeveloperRepository;
use Illuminate\Database\Eloquent\Builder;
use App\Contracts\Repositories\DeveloperRepositoryInterface;

class DeveloperRepositoryTest extends TestCase
{
    /**
     * The developer repository.
     *
     * @var \App\Contracts\Repositories\DeveloperRepositoryInterface
     */
    private DeveloperRepositoryInterface $developerRepository;

    /**
     * Setup new test environments.
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->developerRepository = app(DeveloperRepositoryInterface::class);
    }

    /**
     * Test if DeveloperRepository uses the Developer model correctly.
     *
     * @return void
     */
    public function test_Developer_repository_uses_Developer_model(): void
    {
        /** @var \App\Repositories\DeveloperRepository $developerRepository */
        $developerRepository = $this->developerRepository;

        $this->assertInstanceOf(Developer::class, $developerRepository->model());
    }

    /**
     * Test if can check if developer exists by name.
     *
     * @return void
     */
    public function test_if_can_check_if_developer_exists_by_name(): void
    {
        $name = fake()->name();

        $builder = Mockery::mock(Builder::class);
        $developer = Mockery::mock(Developer::class);

        $builder
            ->shouldReceive('where')
            ->once()
            ->with('name', $name)
            ->andReturnSelf();

        $builder
            ->shouldReceive('exists')
            ->once()
            ->withNoArgs()
            ->andReturnTrue();

        $developer
            ->shouldReceive('query')
            ->once()
            ->withNoArgs()
            ->andReturn($builder);

        $repoMock = Mockery::mock(DeveloperRepository::class)
            ->makePartial()
            ->shouldAllowMockingProtectedMethods();

        $repoMock->shouldReceive('model')
            ->once()
            ->andReturn($developer);

        /** @var \App\Contracts\Repositories\DeveloperRepositoryInterface $repoMock */
        $repoMock->existsByName($name);

        $this->assertEquals(4, Mockery::getContainer()->mockery_getExpectationCount(), 'Mock expectations met.');
    }

    /**
     * Tear down application tests.
     *
     * @return void
     */
    public function tearDown(): void
    {
        Mockery::close();

        parent::tearDown();
    }
}
