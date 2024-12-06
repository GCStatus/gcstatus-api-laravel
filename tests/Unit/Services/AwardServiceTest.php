<?php

namespace Tests\Unit\Services;

use Mockery;
use Tests\TestCase;
use Mockery\MockInterface;
use App\Models\{Mission, User, Wallet};
use App\Contracts\Services\{
    UserServiceInterface,
    AwardServiceInterface,
    WalletServiceInterface,
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

        $this->userService = Mockery::mock(UserServiceInterface::class);
        $this->walletService = Mockery::mock(WalletServiceInterface::class);
        $this->userMissionService = Mockery::mock(UserMissionServiceInterface::class);

        $this->app->instance(UserServiceInterface::class, $this->userService);
        $this->app->instance(WalletServiceInterface::class, $this->walletService);
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
     * Test if can handle mission completion.
     *
     * @return void
     */
    public function test_if_can_handle_mission_completion(): void
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

        $this->userMissionService
            ->shouldReceive('markMissionComplete')
            ->once()
            ->with($user, $mission);

        $this->awardService->handleMissionCompletion($user, $mission);

        $this->assertEquals(3, Mockery::getContainer()->mockery_getExpectationCount(), 'Mock expectations meet.');
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
