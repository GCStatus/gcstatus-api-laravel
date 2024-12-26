<?php

namespace Tests\Unit\Jobs;

use Mockery;
use Tests\TestCase;
use Mockery\MockInterface;
use App\Models\{User, Mission};
use App\Jobs\GiveMissionRewardsJob;
use Illuminate\Support\Facades\{Bus, Queue};
use App\Contracts\Services\MissionServiceInterface;

class GiveMissionRewardsJobTest extends TestCase
{
    /**
     * The mission service.
     *
     * @var \Mockery\MockInterface
     */
    private MockInterface $missionService;

    /**
     * Setup new test environments.
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->missionService = Mockery::mock(MissionServiceInterface::class);

        $this->app->instance(MissionServiceInterface::class, $this->missionService);
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
        Bus::dispatch(new GiveMissionRewardsJob($user, $mission));

        Bus::assertDispatched(GiveMissionRewardsJob::class, 1);
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
        Bus::dispatch(new GiveMissionRewardsJob($user, $mission));

        Queue::assertPushed(GiveMissionRewardsJob::class, 1);
    }

    /**
     * Test if can get correct job arguments.
     *
     * @return void
     */
    public function test_if_can_get_correct_job_arguments(): void
    {
        Bus::fake();

        $user = Mockery::mock(User::class);
        $mission = Mockery::mock(Mission::class);

        $user->shouldReceive('getAttribute')->with('id')->andReturn(1);
        $mission->shouldReceive('getAttribute')->with('id')->andReturn(1);

        /** @var \App\Models\User $user */
        /** @var \App\Models\Mission $mission */
        Bus::dispatch(new GiveMissionRewardsJob($user, $mission));

        Bus::assertDispatched(GiveMissionRewardsJob::class, function (GiveMissionRewardsJob $job) use ($user, $mission) {
            return $job->user->id === $user->id && $job->mission->id === $mission->id;
        });
    }

    /**
     * Test if can call the handle mission completion on job call.
     *
     * @return void
     */
    public function test_if_can_call_the_handle_mission_completion_on_job_call(): void
    {
        $user = Mockery::mock(User::class);
        $mission = Mockery::mock(Mission::class);

        $this->missionService
            ->shouldReceive('handleMissionCompletion')
            ->once()
            ->with($user, $mission);

        /** @var \App\Models\User $user */
        /** @var \App\Models\Mission $mission */
        $job = new GiveMissionRewardsJob($user, $mission);

        $job->handle();

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
