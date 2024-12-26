<?php

namespace Tests\Unit\Services;

use Mockery;
use Tests\TestCase;
use App\Services\LevelService;
use App\Repositories\LevelRepository;
use Illuminate\Database\Eloquent\Collection;
use App\Contracts\Repositories\LevelRepositoryInterface;
use App\Contracts\Services\{
    AwardServiceInterface,
    CacheServiceInterface,
    LevelNotificationServiceInterface,
    LevelServiceInterface,
};
use App\Models\Level;
use App\Models\Rewardable;
use App\Models\User;
use ArrayIterator;
use Illuminate\Support\Facades\DB;
use Mockery\MockInterface;

class LevelServiceTest extends TestCase
{
    /**
     * The award service.
     *
     * @var \Mockery\MockInterface
     */
    private MockInterface $awardService;

    /**
     * The level repository.
     *
     * @var \Mockery\MockInterface
     */
    private MockInterface $levelRepository;

    /**
     * The level notification service.
     *
     * @var \Mockery\MockInterface
     */
    private MockInterface $levelNotificationService;

    /**
     * The level service.
     *
     * @var \App\Contracts\Services\LevelServiceInterface
     */
    private LevelServiceInterface $levelService;

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

        $this->awardService = Mockery::mock(AwardServiceInterface::class);
        $this->levelRepository = Mockery::mock(LevelRepositoryInterface::class);
        $this->levelNotificationService = Mockery::mock(LevelNotificationServiceInterface::class);

        $this->app->instance(AwardServiceInterface::class, $this->awardService);
        $this->app->instance(LevelRepositoryInterface::class, $this->levelRepository);
        $this->app->instance(LevelNotificationServiceInterface::class, $this->levelNotificationService);

        $this->levelService = app(LevelServiceInterface::class);
    }

    /**
     * Test if LevelService uses the Level model correctly.
     *
     * @return void
     */
    public function test_level_repository_uses_level_model(): void
    {
        $this->app->instance(LevelRepositoryInterface::class, new LevelRepository());

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
    public function test_if_can_retrieve_all_levels_from_repository_and_save_on_cache(): void
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
     * Test if can handle user level up.
     *
     * @return void
     */
    public function test_if_can_handle_user_level_up(): void
    {
        $level = Mockery::mock(Level::class);
        $level->shouldReceive('getAttribute')->with('level')->andReturn(1);

        $user = Mockery::mock(User::class);
        $user->shouldReceive('getAttribute')->with('level_id')->andReturn(1);
        $user->shouldReceive('getAttribute')->with('level')->andReturn($level);
        $user->shouldReceive('getAttribute')->with('experience')->andReturn(2000);
        $user->shouldReceive('setAttribute')->andReturnSelf();
        $user->shouldReceive('save')->once();

        $reward = Mockery::mock(Rewardable::class);

        /** @var \Illuminate\Database\Eloquent\Collection<int, \App\Models\Rewardable> $rewards */
        $rewards = Collection::make([$reward]);

        $nextLevel1 = Mockery::mock(Level::class);
        $nextLevel1->shouldReceive('getAttribute')->with('id')->andReturn(2);
        $nextLevel1->shouldReceive('getAttribute')->with('level')->andReturn(2);
        $nextLevel1->shouldReceive('getAttribute')->with('experience')->andReturn(1000);
        $nextLevel1->shouldReceive('getAttribute')->with('coins')->andReturn(100);
        $nextLevel1->shouldReceive('getAttribute')->with('rewards')->andReturn($rewards);

        $nextLevel2 = Mockery::mock(Level::class);
        $nextLevel2->shouldReceive('getAttribute')->with('id')->andReturn(3);
        $nextLevel2->shouldReceive('getAttribute')->with('level')->andReturn(3);
        $nextLevel2->shouldReceive('getAttribute')->with('experience')->andReturn(1500);
        $nextLevel2->shouldReceive('getAttribute')->with('coins')->andReturn(150);
        $nextLevel2->shouldReceive('getAttribute')->with('rewards')->andReturn(new Collection([]));

        $levels = Mockery::mock(Collection::class);
        $levels->shouldReceive('load')->with('rewards.rewardable')->andReturnSelf();
        $levels->shouldReceive('toArray')->andReturn([$nextLevel1, $nextLevel2]);
        $levels->shouldReceive('getIterator')->andReturn(new ArrayIterator([$nextLevel1, $nextLevel2]));

        $this->levelRepository
            ->shouldReceive('getLevelsAboveByLevel')
            ->once()
            ->with(1)
            ->andReturn($levels);

        $levels->shouldReceive('load')->with('rewards.rewardable')->andReturnSelf();

        $this->awardService
            ->shouldReceive('awardCoins')
            ->twice()
            ->with($user, Mockery::type('int'), Mockery::type('string'));

        /** @var \App\Models\Level $nextLevel1 */
        $this->awardService
            ->shouldReceive('awardRewards')
            ->once()
            ->with($user, $nextLevel1->rewards);

        $this->levelNotificationService
            ->shouldReceive('notifyLevelUp')
            ->twice()
            ->with($user, Mockery::type(Level::class));

        DB::shouldReceive('transaction')->once()->andReturnUsing(function (callable $callback) {
            $callback();
        });

        /** @var \App\Models\User $user */
        $this->levelService->handleLevelUp($user);

        $this->assertEquals(6, Mockery::getContainer()->mockery_getExpectationCount(), 'Mock expectations met.');
    }

    /**
     * Test if can't level up the user if user hasn't experience enough to.
     *
     * @return void
     */
    public function test_if_cant_level_up_the_user_if_user_hasnt_experience_enough_to(): void
    {
        $level = Mockery::mock(Level::class);
        $level->shouldReceive('getAttribute')->with('level')->andReturn(1);

        $user = Mockery::mock(User::class);
        $user->shouldReceive('getAttribute')->with('level_id')->andReturn(1);
        $user->shouldReceive('getAttribute')->with('level')->andReturn($level);
        $user->shouldReceive('getAttribute')->with('experience')->andReturn(0);
        $user->shouldReceive('setAttribute')->andReturnSelf();
        $user->shouldReceive('save')->once();

        $reward = Mockery::mock(Rewardable::class);

        /** @var \Illuminate\Database\Eloquent\Collection<int, \App\Models\Rewardable> $rewards */
        $rewards = Collection::make([$reward]);

        $nextLevel1 = Mockery::mock(Level::class);
        $nextLevel1->shouldReceive('getAttribute')->with('id')->andReturn(2);
        $nextLevel1->shouldReceive('getAttribute')->with('level')->andReturn(2);
        $nextLevel1->shouldReceive('getAttribute')->with('experience')->andReturn(1000);
        $nextLevel1->shouldReceive('getAttribute')->with('coins')->andReturn(100);
        $nextLevel1->shouldReceive('getAttribute')->with('rewards')->andReturn($rewards);

        $nextLevel2 = Mockery::mock(Level::class);
        $nextLevel2->shouldReceive('getAttribute')->with('id')->andReturn(3);
        $nextLevel2->shouldReceive('getAttribute')->with('level')->andReturn(3);
        $nextLevel2->shouldReceive('getAttribute')->with('experience')->andReturn(1500);
        $nextLevel2->shouldReceive('getAttribute')->with('coins')->andReturn(150);
        $nextLevel2->shouldReceive('getAttribute')->with('rewards')->andReturn(new Collection([]));

        $levels = Mockery::mock(Collection::class);
        $levels->shouldReceive('load')->with('rewards.rewardable')->andReturnSelf();
        $levels->shouldReceive('toArray')->andReturn([$nextLevel1, $nextLevel2]);
        $levels->shouldReceive('getIterator')->andReturn(new ArrayIterator([$nextLevel1, $nextLevel2]));

        $this->levelRepository
            ->shouldReceive('getLevelsAboveByLevel')
            ->once()
            ->with(1)
            ->andReturn($levels);

        $levels->shouldReceive('load')->with('rewards.rewardable')->andReturnSelf();

        $this->awardService->shouldNotReceive('awardCoins');

        $this->awardService->shouldNotReceive('awardRewards');

        $this->levelNotificationService->shouldNotReceive('notifyLevelUp');

        DB::shouldReceive('transaction')->once()->andReturnUsing(function (callable $callback) {
            $callback();
        });

        /** @var \App\Models\User $user */
        $this->levelService->handleLevelUp($user);

        $this->assertEquals(6, Mockery::getContainer()->mockery_getExpectationCount(), 'Mock expectations met.');
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
