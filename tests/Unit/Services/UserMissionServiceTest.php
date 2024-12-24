<?php

namespace Tests\Unit\Services;

use Mockery;
use Tests\TestCase;
use Mockery\MockInterface;
use Illuminate\Support\Carbon;
use Illuminate\Database\Eloquent\Builder;
use App\Models\{User, Mission, UserMission};
use PHPUnit\Framework\Attributes\RunInSeparateProcess;
use App\Contracts\Services\UserMissionServiceInterface;
use App\Contracts\Repositories\UserMissionRepositoryInterface;

class UserMissionServiceTest extends TestCase
{
    /**
     * The user mission repository mock.
     *
     * @var \Mockery\MockInterface
     */
    private MockInterface $userMissionRepository;

    /**
     * The user mission service.
     *
     * @var \App\Contracts\Services\UserMissionServiceInterface
     */
    private UserMissionServiceInterface $userMissionService;

    /**
     * Setup new test environments.
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->userMissionRepository = Mockery::mock(UserMissionRepositoryInterface::class);

        $this->userMissionService = app(UserMissionServiceInterface::class);

        $this->app->instance(UserMissionRepositoryInterface::class, $this->userMissionRepository);
    }

    /**
     * Test if can mark a mission as complete.
     *
     * @return void
     */
    #[RunInSeparateProcess]
    public function test_if_can_mark_a_mission_as_complete(): void
    {
        $user = Mockery::mock(User::class);
        $mission = Mockery::mock(Mission::class);

        $user->shouldReceive('getAttribute')->with('id')->andReturn(1);
        $mission->shouldReceive('getAttribute')->with('id')->andReturn(1);

        /** @var \App\Models\User $user */
        /** @var \App\Models\Mission $mission */
        $verifiable = [
            'user_id' => $user->id,
            'mission_id' => $mission->id,
        ];

        $userMission = Mockery::mock('overload:' . UserMission::class);
        $userMission
            ->shouldReceive('updateOrCreate')
            ->once()
            ->with($verifiable, Mockery::on(function (array $value) {
                return $value['completed'] === true && $value['last_completed_at'] instanceof Carbon;
            }))->andReturnTrue();

        $this->userMissionService->markMissionComplete($user, $mission);

        $this->assertEquals(1, Mockery::getContainer()->mockery_getExpectationCount(), 'Mock expectations met.');
    }

    /**
     * Test if can check if user already completed mission.
     *
     * @return void
     */
    #[RunInSeparateProcess]
    public function test_if_can_check_if_user_already_completed_mission(): void
    {
        $userId = 1;
        $missionId = 1;

        $user = Mockery::mock(User::class);
        $mission = Mockery::mock(Mission::class);
        $builder = Mockery::mock(Builder::class);

        $user->shouldReceive('getAttribute')->with('id')->andReturn($userId);
        $mission->shouldReceive('getAttribute')->with('id')->andReturn($missionId);

        /** @var \App\Models\User $user */
        /** @var \App\Models\Mission $mission */
        $builder
            ->shouldReceive('where')
            ->once()
            ->with('user_id', $user->id)
            ->andReturnSelf();
        $builder
            ->shouldReceive('where')
            ->once()
            ->with('mission_id', $mission->id)
            ->andReturnSelf();
        $builder
            ->shouldReceive('where')
            ->once()
            ->with('completed', true)
            ->andReturnSelf();
        $builder
            ->shouldReceive('exists')
            ->once()
            ->andReturn(fake()->boolean());

        $userMission = Mockery::mock('overload:' . UserMission::class);
        $userMission->shouldReceive('query')->andReturn($builder);

        $this->userMissionService->userAlreadyCompletedMission($user, $mission);

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
