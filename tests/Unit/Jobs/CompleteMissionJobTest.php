<?php

namespace Tests\Unit\Jobs;

use Mockery;
use Tests\TestCase;
use Mockery\MockInterface;
use Illuminate\Support\Facades\{Bus, Queue};
use Illuminate\Database\Eloquent\Collection;
use App\Models\{User, Mission, MissionRequirement};
use App\Jobs\{GiveMissionRewardsJob, CompleteMissionJob};
use App\Contracts\Services\UserMissionProgressServiceInterface;

class CompleteMissionJobTest extends TestCase
{
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

        $this->userMissionProgressService = Mockery::mock(UserMissionProgressServiceInterface::class);

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
        $mission = Mockery::mock(Mission::class);

        /** @var \App\Models\User $user */
        /** @var \App\Models\Mission $mission */
        Bus::dispatch(new CompleteMissionJob($user, $mission));

        Bus::assertDispatched(CompleteMissionJob::class, 1);
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
        $mission = Mockery::mock(Mission::class);

        /** @var \App\Models\User $user */
        /** @var \App\Models\Mission $mission */
        Bus::dispatch(new CompleteMissionJob($user, $mission));

        Queue::assertPushed(CompleteMissionJob::class, 1);
    }

    /**
     * Test the handle method processes mission requirements and dispatches rewards job.
     *
     * @return void
     */
    public function test_handle_method(): void
    {
        $user = Mockery::mock(User::class);
        $mission = Mockery::mock(Mission::class);
        $requirement1 = Mockery::mock(MissionRequirement::class);
        $requirement2 = Mockery::mock(MissionRequirement::class);

        $requirements = new Collection([$requirement1, $requirement2]);

        $mission->shouldReceive('load')->once()->with('requirements')->andReturnSelf();
        $mission->shouldReceive('getAttribute')->with('requirements')->andReturn($requirements);

        $this->userMissionProgressService
            ->shouldReceive('updateProgress')
            ->once()->with($user, $requirement1);

        $this->userMissionProgressService
            ->shouldReceive('updateProgress')
            ->once()->with($user, $requirement2);

        Bus::fake();

        /** @var \App\Models\User $user */
        /** @var \App\Models\Mission $mission */
        $job = new CompleteMissionJob($user, $mission);

        $job->handle();

        Bus::assertDispatchedSync(GiveMissionRewardsJob::class, function ($dispatchedJob) use ($user, $mission) {
            return $dispatchedJob->user === $user && $dispatchedJob->mission === $mission;
        });
    }


    /**
     * Test the handle method with no mission requirements.
     *
     * @return void
     */
    public function test_handle_method_with_no_requirements(): void
    {
        $user = Mockery::mock(User::class);
        $mission = Mockery::mock(Mission::class);

        $requirements = new Collection([]);

        $mission->shouldReceive('load')->once()->with('requirements')->andReturnSelf();
        $mission->shouldReceive('getAttribute')->with('requirements')->andReturn($requirements);

        $this->userMissionProgressService
            ->shouldNotReceive('updateProgress');

        Bus::fake();

        /** @var \App\Models\User $user */
        /** @var \App\Models\Mission $mission */
        $job = new CompleteMissionJob($user, $mission);

        $job->handle();

        Bus::assertDispatchedSync(GiveMissionRewardsJob::class, function ($dispatchedJob) use ($user, $mission) {
            return $dispatchedJob->user === $user && $dispatchedJob->mission === $mission;
        });
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
