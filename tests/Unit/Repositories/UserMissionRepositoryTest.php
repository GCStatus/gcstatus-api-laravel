<?php

namespace Tests\Unit\Repositories;

use Mockery;
use Tests\TestCase;
use Illuminate\Database\Eloquent\Builder;
use PHPUnit\Framework\Attributes\RunInSeparateProcess;
use App\Contracts\Repositories\UserMissionRepositoryInterface;
use App\Models\{Mission, User, MissionRequirement, UserMission};

class UserMissionRepositoryTest extends TestCase
{
    /**
     * The user mission repository.
     *
     * @var \App\Contracts\Repositories\UserMissionRepositoryInterface
     */
    private UserMissionRepositoryInterface $userMissionRepository;

    /**
     * Setup new test environments.
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->userMissionRepository = app(UserMissionRepositoryInterface::class);
    }

    /**
     * Test if can update or create user mission.
     *
     * @return void
     */
    #[RunInSeparateProcess]
    public function test_if_can_update_or_create_user_mission(): void
    {
        $user = Mockery::mock(User::class);
        $mission = Mockery::mock(Mission::class);
        $requirement = Mockery::mock(MissionRequirement::class);

        $userMissionMock = Mockery::mock('overload:' . UserMission::class);

        $user
            ->shouldReceive('getAttribute')
            ->with('id')
            ->andReturn(1);

        $requirement
            ->shouldReceive('getAttribute')
            ->with('id')
            ->andReturn(1);
        $requirement
            ->shouldReceive('getAttribute')
            ->with('goal')
            ->andReturn(5);

        $mission
            ->shouldReceive('getAttribute')
            ->with('id')
            ->andReturn(1);

        /** @var \App\Models\User $user */
        /** @var \App\Models\Mission $mission */
        $payload = [
            'verifiable' => [
                'user_id' => $user->id,
                'mission_id' => $mission->id,
            ],
            'updatable' => [
                'completed' => true,
                'last_completed_at' => now(),
            ],
        ];

        $userMissionMock
            ->shouldReceive('updateOrCreate')
            ->once()
            ->with(
                $payload['verifiable'],
                $payload['updatable']
            )->andReturnTrue();

        $this->userMissionRepository->updateOrCreate(
            $payload['verifiable'],
            $payload['updatable']
        );

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
        $userMissionMock = Mockery::mock('overload:' . UserMission::class);

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
            ->shouldReceive('exists')
            ->once()
            ->andReturn(fake()->boolean());

        $userMissionMock->shouldReceive('query')->andReturn($builder);

        $this->userMissionRepository->userAlreadyCompletedMission($user->id, $mission->id);

        $this->assertEquals(3, Mockery::getContainer()->mockery_getExpectationCount(), 'Mock expectations met.');
    }

    /**
     * Tear down tests.
     *
     * @return void
     */
    public function tearDown(): void
    {
        Mockery::close();

        parent::tearDown();
    }
}
