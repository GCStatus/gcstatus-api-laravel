<?php

namespace Tests\Unit\Services;

use Mockery;
use Tests\TestCase;
use Mockery\MockInterface;
use Illuminate\Database\Eloquent\Collection;
use App\Models\{Mission, Rewardable, Title, User};
use App\Contracts\Strategies\RewardStrategyInterface;
use App\Contracts\Factories\RewardStrategyFactoryInterface;
use App\Contracts\Services\{
    LogServiceInterface,
    UserServiceInterface,
    AwardServiceInterface,
    WalletServiceInterface,
    UserTitleServiceInterface,
    UserMissionServiceInterface,
};

class AwardServiceTest extends TestCase
{
    /**
     * The award service.
     *
     * @var \App\Contracts\Services\AwardServiceInterface
     */
    private AwardServiceInterface $awardService;

    /**
     * The log service.
     *
     * @var \Mockery\MockInterface
     */
    private MockInterface $logService;

    /**
     * The user service.
     *
     * @var \Mockery\MockInterface
     */
    private MockInterface $userService;

    /**
     * The wallet service.
     *
     * @var \Mockery\MockInterface
     */
    private MockInterface $walletService;

    /**
     * The user title service.
     *
     * @var \Mockery\MockInterface
     */
    private MockInterface $userTitleService;

    /**
     * The user mission service.
     *
     * @var \Mockery\MockInterface
     */
    private MockInterface $userMissionService;

    /**
     * Setup test environments.
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->logService = Mockery::mock(LogServiceInterface::class);
        $this->userService = Mockery::mock(UserServiceInterface::class);
        $this->walletService = Mockery::mock(WalletServiceInterface::class);
        $this->userTitleService = Mockery::mock(UserTitleServiceInterface::class);
        $this->userMissionService = Mockery::mock(UserMissionServiceInterface::class);

        $this->app->instance(LogServiceInterface::class, $this->logService);
        $this->app->instance(UserServiceInterface::class, $this->userService);
        $this->app->instance(WalletServiceInterface::class, $this->walletService);
        $this->app->instance(UserTitleServiceInterface::class, $this->userTitleService);
        $this->app->instance(UserMissionServiceInterface::class, $this->userMissionService);

        $this->awardService = app(AwardServiceInterface::class);
    }

    /**
     * Test if can award coins and experience for the user.
     *
     * @return void
     */
    public function test_if_can_award_coins_and_experience_for_the_user(): void
    {
        $user = Mockery::mock(User::class);
        $mission = Mockery::mock(Mission::class);

        $user->shouldReceive('getAttribute')->with('id')->andReturn(1);
        $mission->shouldReceive('getAttribute')->with('coins')->andReturn(50);
        $mission->shouldReceive('getAttribute')->with('experience')->andReturn(100);

        /** @var \App\Models\User $user */
        /** @var \App\Models\Mission $mission */
        $this->userService
            ->shouldReceive('addExperience')
            ->once()
            ->with($user->id, $mission->experience);

        $this->walletService
            ->shouldReceive('addFunds')
            ->once()
            ->with($user, $mission->coins);

        $this->awardService->awardCoinsAndExperience($user, $mission);

        $this->assertEquals(2, Mockery::getContainer()->mockery_getExpectationCount(), 'Mock expectations meet.');
    }

    /**
     * Test if can handle mission completion when not completed yet.
     *
     * @return void
     */
    public function test_if_can_handle_mission_completion_when_not_completed_yet(): void
    {
        $user = Mockery::mock(User::class);
        $title = Mockery::mock(Title::class);
        $mission = Mockery::mock(Mission::class);
        $rewardable = Mockery::mock(Rewardable::class);

        $user->shouldReceive('getAttribute')->with('id')->andReturn(1);
        $mission->shouldReceive('getAttribute')->with('coins')->andReturn(50);
        $mission->shouldReceive('getAttribute')->with('experience')->andReturn(100);
        $rewardable->shouldReceive('getAttribute')->with('rewardable')->andReturn($title);
        $mission->shouldReceive('getAttribute')->with('rewards')->andReturn($rewardable);

        $mission->shouldReceive('load')
            ->with('rewards.rewardable')
            ->andReturnSelf();

        $rewardable
            ->shouldReceive('each')
            ->once();

        /** @var \App\Models\User $user */
        /** @var \App\Models\Mission $mission */
        $this->userMissionService
            ->shouldReceive('userAlreadyCompletedMission')
            ->once()
            ->with($user, $mission)
            ->andReturnFalse();

        $this->userService
            ->shouldReceive('addExperience')
            ->once()
            ->with($user->id, $mission->experience);

        $this->walletService
            ->shouldReceive('addFunds')
            ->once()
            ->with($user, $mission->coins);

        $this->userMissionService
            ->shouldReceive('markMissionComplete')
            ->once()
            ->with($user, $mission);

        $this->awardService->handleMissionCompletion($user, $mission);

        $this->assertEquals(5, Mockery::getContainer()->mockery_getExpectationCount(), 'Mock expectations meet.');
    }

    /**
     * Test if can handle mission completion when already completed.
     *
     * @return void
     */
    public function test_if_can_handle_mission_completion_when_already_completed(): void
    {
        $user = Mockery::mock(User::class);
        $mission = Mockery::mock(Mission::class);

        $user->shouldReceive('getAttribute')->with('id')->andReturn(1);
        $mission->shouldReceive('getAttribute')->with('coins')->andReturn(50);
        $mission->shouldReceive('getAttribute')->with('experience')->andReturn(100);

        /** @var \App\Models\User $user */
        /** @var \App\Models\Mission $mission */
        $this->userMissionService
            ->shouldReceive('userAlreadyCompletedMission')
            ->once()
            ->with($user, $mission)
            ->andReturnTrue();

        $this->walletService->shouldNotReceive('addFunds');
        $this->userService->shouldNotReceive('addExperience');
        $this->userMissionService->shouldNotReceive('markMissionComplete');

        $this->awardService->handleMissionCompletion($user, $mission);

        $this->assertEquals(4, Mockery::getContainer()->mockery_getExpectationCount(), 'Mock expectations meet.');
    }

    /**
     * Test if award rewards calls resolved strategy award method.
     *
     * @return void
     */
    public function test_if_award_rewards_calls_resolved_strategy_award_method(): void
    {
        $rewardStrategyFactoryMock = Mockery::mock(RewardStrategyFactoryInterface::class);
        $rewardStrategyMock = Mockery::mock(RewardStrategyInterface::class);

        $rewardable = Mockery::mock(Rewardable::class);
        $rewardable->shouldReceive('getAttribute')->with('rewardable_type')->andReturn(Title::class);

        $user = Mockery::mock(User::class);
        $mission = Mockery::mock(Mission::class);
        $mission->shouldReceive('getAttribute')->with('rewards')->andReturn(Collection::make([$rewardable]));

        $mission->shouldReceive('load')
            ->with('rewards.rewardable')
            ->andReturnSelf();

        $rewardStrategyFactoryMock
            ->shouldReceive('resolve')
            ->once()
            ->with($rewardable)
            ->andReturn($rewardStrategyMock);

        $rewardStrategyMock
            ->shouldReceive('award')
            ->once()
            ->with($user, $rewardable);

        $this->app->instance(RewardStrategyFactoryInterface::class, $rewardStrategyFactoryMock);

        $awardService = app(AwardServiceInterface::class);

        /** @var \App\Models\User $user */
        /** @var \App\Models\Mission $mission */
        $awardService->awardRewards($user, $mission);

        $this->assertEquals(2, Mockery::getContainer()->mockery_getExpectationCount(), 'Mock expectations met.');
    }

    public function test_if_award_rewards_handles_exceptions(): void
    {
        $rewardStrategyFactoryMock = Mockery::mock(RewardStrategyFactoryInterface::class);
        $rewardStrategyMock = Mockery::mock(RewardStrategyInterface::class);

        $rewardable = Mockery::mock(Rewardable::class);
        $rewardable->shouldReceive('getAttribute')->with('rewardable_type')->andReturn(Title::class);

        $user = Mockery::mock(User::class);
        $mission = Mockery::mock(Mission::class);
        $mission->shouldReceive('getAttribute')->with('rewards')->andReturn(Collection::make([$rewardable]));

        $mission->shouldReceive('load')
            ->with('rewards.rewardable')
            ->andReturnSelf();

        $rewardStrategyFactoryMock
            ->shouldReceive('resolve')
            ->once()
            ->with($rewardable)
            ->andReturn($rewardStrategyMock);

        $rewardStrategyMock
            ->shouldReceive('award')
            ->once()
            ->with($user, $rewardable)
            ->andThrow(new \Exception('Test exception'));

        $this->logService
            ->shouldReceive('error')
            ->once()
            ->withAnyArgs();

        $this->app->instance(RewardStrategyFactoryInterface::class, $rewardStrategyFactoryMock);

        $awardService = app(AwardServiceInterface::class);

        /** @var \App\Models\User $user */
        /** @var \App\Models\Mission $mission */
        $awardService->awardRewards($user, $mission);

        $this->assertEquals(3, Mockery::getContainer()->mockery_getExpectationCount(), 'Mock expectations met.');
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
