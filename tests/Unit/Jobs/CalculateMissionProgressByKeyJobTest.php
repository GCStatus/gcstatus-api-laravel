<?php

namespace Tests\Unit\Jobs;

use Mockery;
use Tests\TestCase;
use Illuminate\Support\Facades\{Bus, Queue};
use Illuminate\Database\Eloquent\Collection;
use App\Models\{User, Mission, MissionRequirement};
use App\Jobs\{GiveMissionRewardsJob, CalculateMissionProgressByKeyJob};
use App\Contracts\Services\{
    AwardServiceInterface,
    MissionRequirementServiceInterface,
    ProgressCalculatorServiceInterface,
    UserMissionProgressServiceInterface,
};

class CalculateMissionProgressByKeyJobTest extends TestCase
{
    /**
     * The award service.
     *
     * @var \Mockery\MockInterface
     */
    private $awardService;

    /**
     * The progress calculator service.
     *
     * @var \Mockery\MockInterface
     */
    private $progressCalculatorService;

    /**
     * The mission requirement service.
     *
     * @var \Mockery\MockInterface
     */
    private $missionRequirementService;

    /**
     * The user mission progress service.
     *
     * @var \Mockery\MockInterface
     */
    private $userMissionProgressService;

    /**
     * Setup new test environments.
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->awardService = Mockery::mock(AwardServiceInterface::class);
        $this->missionRequirementService = Mockery::mock(MissionRequirementServiceInterface::class);
        $this->progressCalculatorService = Mockery::mock(ProgressCalculatorServiceInterface::class);
        $this->userMissionProgressService = Mockery::mock(UserMissionProgressServiceInterface::class);

        $this->app->instance(AwardServiceInterface::class, $this->awardService);
        $this->app->instance(ProgressCalculatorServiceInterface::class, $this->progressCalculatorService);
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
            ->withArgs(function ($userArg, $requirementArg) use ($user, $missionRequirement1, $missionRequirement2) {
                return $userArg === $user && in_array($requirementArg, [$missionRequirement1, $missionRequirement2]);
            });

        $mission1 = Mockery::mock(Mission::class);
        $mission2 = Mockery::mock(Mission::class);

        $missionRequirement1->shouldReceive('getAttribute')->with('mission')->andReturn($mission1);
        $missionRequirement2->shouldReceive('getAttribute')->with('mission')->andReturn($mission2);

        $this->progressCalculatorService
            ->shouldReceive('isMissionComplete')
            ->twice()
            ->withArgs(function (User $userArg, Mission $missionArg) use ($user, $mission1, $mission2) {
                return $userArg === $user && in_array($missionArg, [$mission1, $mission2]);
            })->andReturnFalse();

        $job = new CalculateMissionProgressByKeyJob($key, $user);

        $job->handle();

        $this->assertEquals(3, Mockery::getContainer()->mockery_getExpectationCount(), 'Mock expectations match.');
    }

    /**
     * Test if can correctly dispatch chain job to give mission rewards on mission complete.
     *
     * @return void
     */
    public function test_if_can_correctly_dispatch_chain_job_to_give_mission_rewards_on_mission_complete(): void
    {
        Bus::fake();

        $key = 'mock_missions_key';
        $user = Mockery::mock(User::class)->makePartial();
        $user->shouldReceive('getAttribute')->with('id')->andReturn(1);

        $missionRequirement1 = Mockery::mock(MissionRequirement::class);

        $missionRequirements = Collection::make([$missionRequirement1]);

        $this->missionRequirementService
            ->shouldReceive('findByKey')
            ->once()
            ->with($key)
            ->andReturn($missionRequirements);

        /** @var \App\Models\User $user */
        $this->userMissionProgressService
            ->shouldReceive('updateProgress')
            ->once()
            ->withArgs(function ($userArg, $requirementArg) use ($user, $missionRequirement1) {
                return $userArg === $user && in_array($requirementArg, [$missionRequirement1]);
            });

        $mission1 = Mockery::mock(Mission::class);
        $mission1->shouldReceive('getAttribute')->with('id')->andReturn(1);

        $missionRequirement1->shouldReceive('getAttribute')->with('mission')->andReturn($mission1);

        $this->progressCalculatorService
            ->shouldReceive('isMissionComplete')
            ->once()
            ->withArgs(function (User $userArg, Mission $missionArg) use ($user, $mission1) {
                return $userArg === $user && in_array($missionArg, [$mission1]);
            })->andReturnTrue();

        $job = new CalculateMissionProgressByKeyJob($key, $user);

        $job->handle();

        Bus::assertDispatched(GiveMissionRewardsJob::class, function (GiveMissionRewardsJob $job) use ($user, $mission1) {
            /** @var \App\Models\Mission $mission1 */
            return $job->user->id === $user->id && $job->mission->id === $mission1->id;
        });

        $this->assertEquals(3, Mockery::getContainer()->mockery_getExpectationCount(), 'Mock expectations match.');
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
