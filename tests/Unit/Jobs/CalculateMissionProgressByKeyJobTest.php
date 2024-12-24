<?php

namespace Tests\Unit\Jobs;

use Mockery;
use Tests\TestCase;
use Mockery\MockInterface;
use Illuminate\Support\Facades\{Bus, Queue};
use Illuminate\Database\Eloquent\Collection;
use App\Jobs\CalculateMissionProgressByKeyJob;
use App\Models\{User, Mission, MissionRequirement};
use App\Contracts\Services\{
    MissionRequirementServiceInterface,
    UserMissionProgressServiceInterface,
};

class CalculateMissionProgressByKeyJobTest extends TestCase
{
    /**
     * The mission requirement service.
     *
     * @var \Mockery\MockInterface
     */
    private MockInterface $missionRequirementService;

    /**
     * The user mission progress service.
     *
     * @var \Mockery\MockInterface
     */
    private MockInterface $userMissionProgressService;

    /**
     * Setup new test environments.
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->missionRequirementService = Mockery::mock(MissionRequirementServiceInterface::class);
        $this->userMissionProgressService = Mockery::mock(UserMissionProgressServiceInterface::class);

        $this->app->instance(MissionRequirementServiceInterface::class, $this->missionRequirementService);
        $this->app->instance(UserMissionProgressServiceInterface::class, $this->userMissionProgressService);
    }

    /**
     * Test if job can be dispatched.
     *
     * @return void
     */
    public function test_if_the_job_can_be_dispatched(): void
    {
        Bus::fake();

        $user = Mockery::mock(User::class);

        /** @var \App\Models\User $user */
        Bus::dispatch(new CalculateMissionProgressByKeyJob('mock_key', $user));

        Bus::assertDispatched(CalculateMissionProgressByKeyJob::class, 1);
    }

    /**
     * Test if job can be queued.
     *
     * @return void
     */
    public function test_if_the_job_can_be_queued(): void
    {
        Queue::fake();

        $user = Mockery::mock(User::class);

        /** @var \App\Models\User $user */
        Bus::dispatch(new CalculateMissionProgressByKeyJob('mock_key', $user));

        Queue::assertPushed(CalculateMissionProgressByKeyJob::class, 1);
    }

    /**
     * Test if can correctly calculate and update mission progress by key for given user.
     *
     * @return void
     */
    public function test_if_can_correctly_calculate_and_update_mission_progress_by_key_for_given_user(): void
    {
        $key = 'mock_missions_key';
        $user = Mockery::mock(User::class)->makePartial();
        $user->shouldReceive('getAttribute')->with('id')->andReturn(1);

        $missionRequirement1 = Mockery::mock(MissionRequirement::class);
        $missionRequirement2 = Mockery::mock(MissionRequirement::class);

        $missionRequirements = Collection::make([$missionRequirement1, $missionRequirement2]);

        $this->missionRequirementService
            ->shouldReceive('findByKey')
            ->once()
            ->with($key)
            ->andReturn($missionRequirements);

        /** @var \App\Models\User $user */
        $this->userMissionProgressService
            ->shouldReceive('updateProgress')
            ->twice()
            ->with(Mockery::on(fn (User $u) => $u->id === $user->id), Mockery::type(MissionRequirement::class))
            ->andReturnNull();

        $mission1 = Mockery::mock(Mission::class)->makePartial();
        $mission2 = Mockery::mock(Mission::class)->makePartial();
        $mission1->shouldReceive('getAttribute')->with('id')->andReturn(1);
        $mission2->shouldReceive('getAttribute')->with('id')->andReturn(2);

        $missionRequirement1->shouldReceive('getAttribute')->with('mission')->andReturn($mission1);
        $missionRequirement2->shouldReceive('getAttribute')->with('mission')->andReturn($mission2);

        $job = new CalculateMissionProgressByKeyJob($key, $user);

        $job->handle();

        $this->assertEquals(2, Mockery::getContainer()->mockery_getExpectationCount(), 'Mock expectations match.');
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
