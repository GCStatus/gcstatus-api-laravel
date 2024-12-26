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

        $this->app->instance(LogServiceInterface::class, $this->logService);
        $this->app->instance(UserServiceInterface::class, $this->userService);
        $this->app->instance(WalletServiceInterface::class, $this->walletService);

        $this->awardService = app(AwardServiceInterface::class);
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

        /** @var \Illuminate\Database\Eloquent\Collection<int, \App\Models\Rewardable> $rewards */
        $rewards = Collection::make([$rewardable]);

        /** @var \App\Models\User $user */
        $awardService->awardRewards($user, $rewards);

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

        /** @var \Illuminate\Database\Eloquent\Collection<int, \App\Models\Rewardable> $rewards */
        $rewards = Collection::make([$rewardable]);

        /** @var \App\Models\User $user */
        $awardService->awardRewards($user, $rewards);

        $this->assertEquals(3, Mockery::getContainer()->mockery_getExpectationCount(), 'Mock expectations met.');
    }

    /**
     * Test if can award experience for given user.
     *
     * @return void
     */
    public function test_if_can_award_experience_for_given_user(): void
    {
        $amount = 50;

        $user = Mockery::mock(User::class);

        $this->userService
            ->shouldReceive('addExperience')
            ->once()
            ->with($user, $amount);

        /** @var \App\Models\User $user */
        $this->awardService->awardExperience($user, $amount);

        $this->assertEquals(1, Mockery::getContainer()->mockery_getExpectationCount(), 'Mock expectations met.');
    }

    /**
     * Test if can award coins for given user.
     *
     * @return void
     */
    public function test_if_can_award_coins_for_given_user(): void
    {
        $amount = 50;
        $description = fake()->text();

        $user = Mockery::mock(User::class);

        $this->walletService
            ->shouldReceive('addFunds')
            ->once()
            ->with($user, $amount, $description);

        /** @var \App\Models\User $user */
        $this->awardService->awardCoins($user, $amount, $description);

        $this->assertEquals(1, Mockery::getContainer()->mockery_getExpectationCount(), 'Mock expectations met.');
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
