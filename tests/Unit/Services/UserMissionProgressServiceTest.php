<?php

namespace Tests\Unit\Services;

use Mockery;
use Tests\TestCase;
use Mockery\MockInterface;
use PHPUnit\Framework\Attributes\RunInSeparateProcess;
use App\Models\{User, MissionRequirement, UserMissionProgress};
use App\Contracts\Services\{
    ProgressCalculatorServiceInterface,
    UserMissionProgressServiceInterface,
};

class UserMissionProgressServiceTest extends TestCase
{
    /**
     * The progress calculator service.
     *
     * @var \Mockery\MockInterface
     */
    private MockInterface $progressCalculatorService;

    /**
     * The user mission progress service.
     *
     * @var \App\Contracts\Services\UserMissionProgressServiceInterface
     */
    private UserMissionProgressServiceInterface $userMissionProgressService;

    /**
     * Setup new test environments.
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->progressCalculatorService = Mockery::mock(ProgressCalculatorServiceInterface::class);

        $this->app->instance(ProgressCalculatorServiceInterface::class, $this->progressCalculatorService);

        $this->userMissionProgressService = app(UserMissionProgressServiceInterface::class);
    }

    /**
     * Test if can update a progress for given user and mission requirement.
     *
     * @return void
     */
    #[RunInSeparateProcess]
    public function test_if_can_update_a_progress_for_given_user_and_mission_requirement(): void
    {
        $userId = 1;

        $user = Mockery::mock(User::class);
        $missionRequirement = Mockery::mock(MissionRequirement::class);

        $user->shouldReceive('getAttribute')->with('id')->andReturn($userId);
        $missionRequirement->shouldReceive('getAttribute')->with('id')->andReturn(1);
        $missionRequirement->shouldReceive('getAttribute')->with('goal')->andReturn(5);

        $this->progressCalculatorService
            ->shouldReceive('determineProgress')
            ->once()
            ->with($user, $missionRequirement)
            ->andReturn(5);

        /** @var \App\Models\User $user */
        /** @var \App\Models\MissionRequirement $missionRequirement */
        $verifiable = [
            'user_id' => $user->id,
            'mission_requirement_id' => $missionRequirement->id,
        ];

        $userMissionProgress = Mockery::mock('overload:' . UserMissionProgress::class);
        $userMissionProgress
            ->shouldReceive('updateOrCreate')
            ->once()
            ->with($verifiable, Mockery::on(function (array $value) {
                return $value['progress'] === 5 && $value['completed'] === true;
            }))->andReturnTrue();

        $this->userMissionProgressService->updateProgress($user, $missionRequirement);

        $this->assertEquals(2, Mockery::getContainer()->mockery_getExpectationCount(), 'Mock expectations met.');
    }

    /**
     * Tear down test mockery.
     *
     * @return void
     */
    public function tearDown(): void
    {
        Mockery::close();

        parent::tearDown();
    }
}
