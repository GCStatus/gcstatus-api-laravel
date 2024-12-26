<?php

namespace Tests\Unit\Repositories;

use Mockery;
use Tests\TestCase;
use App\Models\Level;
use App\Repositories\LevelRepository;
use Illuminate\Database\Eloquent\{Builder, Collection};
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
     * Test if can find levels above given id if levels exist.
     *
     * @return void
     */
    public function test_if_can_find_a_next_level_by_id_if_level_exists(): void
    {
        $level = 1;

        $levelMock = Mockery::mock(Level::class);
        $builderMock = Mockery::mock(Builder::class);

        $collection = Collection::make([$levelMock]);

        $builderMock->shouldReceive('where')
            ->once()
            ->with('level', '>', $level)
            ->andReturnSelf();

        $builderMock->shouldReceive('orderBy')
            ->once()
            ->with('level', 'asc')
            ->andReturnSelf();

        $builderMock->shouldReceive('get')
            ->once()
            ->withNoArgs()
            ->andReturn($collection);

        $levelMock->shouldReceive('query')->andReturn($builderMock);

        $repoMock = Mockery::mock(LevelRepository::class)
            ->makePartial()
            ->shouldAllowMockingProtectedMethods();

        $repoMock->shouldReceive('model')
            ->once()
            ->andReturn($levelMock);

        /** @var \App\Contracts\Repositories\LevelRepositoryInterface $repoMock */
        $result = $repoMock->getLevelsAboveByLevel($level);

        $this->assertEquals($collection, $result);
        $this->assertInstanceOf(Collection::class, $result);

        $this->assertEquals(4, Mockery::getContainer()->mockery_getExpectationCount(), 'Mock expectations met.');
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
