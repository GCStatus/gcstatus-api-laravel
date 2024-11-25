<?php

namespace Tests\Unit\Services;

use Mockery;
use Tests\TestCase;
use App\Services\LevelService;
use App\Repositories\LevelRepository;
use Illuminate\Database\Eloquent\Collection;
use App\Contracts\Repositories\LevelRepositoryInterface;
use App\Contracts\Services\{CacheServiceInterface, LevelServiceInterface};

class LevelServiceTest extends TestCase
{
    /**
     * The level service.
     *
     * @var \App\Contracts\Services\LevelServiceInterface
     */
    private $levelService;

    /**
     * The levels cache key.
     *
     * @var string
     */
    private const LEVELS_CACHE_KEY = 'gcstatus_levels_key';

    /**
     * Setup new test environments.
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->levelService = app(LevelServiceInterface::class);
    }

    /**
     * Test if LevelService uses the Level model correctly.
     *
     * @return void
     */
    public function test_level_repository_uses_level_model(): void
    {
        /** @var \App\Services\LevelService $levelService */
        $levelService = $this->levelService;

        $this->assertInstanceOf(LevelRepository::class, $levelService->repository());
    }

    /**
     * Test if can get all levels from cache correctly.
     *
     * @return void
     */
    public function test_if_can_get_all_levels_from_cache_correctly(): void
    {
        $cacheServiceMock = Mockery::mock(CacheServiceInterface::class);

        $fakeLevels = Collection::make(['level1', 'level2']);

        $cacheServiceMock->shouldReceive('has')->with(self::LEVELS_CACHE_KEY)->andReturnTrue();
        $cacheServiceMock->shouldReceive('get')->with(self::LEVELS_CACHE_KEY)->andReturn($fakeLevels);

        $mockService = Mockery::mock(LevelServiceInterface::class);
        $mockService->shouldReceive('all')
            ->once()
            ->withNoArgs()
            ->andReturn($fakeLevels);

        /** @var \App\Contracts\Services\LevelServiceInterface $mockService */
        $this->assertEquals($fakeLevels, $mockService->all());
    }

    /**
     * Test if can retrieve all levels from repository and save on cache.
     *
     * @return void
     */
    public function testAllCachesAndReturnsLevelsFromRepository(): void
    {
        $key = '';

        $cacheServiceMock = Mockery::mock(CacheServiceInterface::class);
        $repositoryMock = Mockery::mock(LevelRepositoryInterface::class);

        $fakeLevels = Collection::make(['level1', 'level2']);

        $cacheServiceMock->shouldReceive('has')->with(self::LEVELS_CACHE_KEY)->andReturnFalse();
        $repositoryMock->shouldReceive('all')->once()->withNoArgs()->andReturn($fakeLevels);

        $cacheServiceMock
            ->shouldReceive('forever')
            ->once()
            ->with(self::LEVELS_CACHE_KEY, $fakeLevels)
            ->andReturnTrue();

        $levelService = $this->getMockBuilder(LevelService::class)
            ->setConstructorArgs([$cacheServiceMock])
            ->onlyMethods(['repository'])
            ->getMock();

        $levelService->method('repository')->willReturn($repositoryMock);

        $this->assertEquals($fakeLevels, $levelService->all());
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
