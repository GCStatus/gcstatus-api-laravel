<?php

namespace Tests\Unit\Repositories;

use Mockery;
use Tests\TestCase;
use PHPUnit\Framework\Attributes\RunInSeparateProcess;
use App\Models\{User, MissionRequirement, UserMissionProgress};
use App\Contracts\Repositories\UserMissionProgressRepositoryInterface;

class UserMissionProgressRepositoryTest extends TestCase
{
    /**
     * The user mission progress repository.
     *
     * @var \App\Contracts\Repositories\UserMissionProgressRepositoryInterface
     */
    private UserMissionProgressRepositoryInterface $userMissionProgressRepository;

    /**
     * Setup new test environments.
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->userMissionProgressRepository = app(UserMissionProgressRepositoryInterface::class);
    }

    /**
     * Test if can update or create user mission progress.
     *
     * @return void
     */
    #[RunInSeparateProcess]
    public function test_if_can_update_or_create_user_mission_progress(): void
    {
        $user = Mockery::mock(User::class);
        $requirement = Mockery::mock(MissionRequirement::class);

        $userMissionProgressMock = Mockery::mock('overload:' . UserMissionProgress::class);

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

        /** @var \App\Models\User $user */
        /** @var \App\Models\MissionRequirement $requirement */
        $payload = [
            'verifiable' => ['user_id' => $user->id, 'mission_requirement_id' => $requirement->id],
            'updatable' => ['progress' => 3, 'completed' => false],
        ];

        $userMissionProgressMock
            ->shouldReceive('updateOrCreate')
            ->once()
            ->with(
                $payload['verifiable'],
                $payload['updatable']
            )->andReturnTrue();

        $this->userMissionProgressRepository->updateOrCreate(
            $payload['verifiable'],
            $payload['updatable']
        );

        $this->assertEquals(1, Mockery::getContainer()->mockery_getExpectationCount(), 'Mock expectations meet.');
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
