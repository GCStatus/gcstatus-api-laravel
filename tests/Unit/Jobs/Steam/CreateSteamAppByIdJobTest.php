<?php

namespace Tests\Unit\Jobs\Steam;

use Mockery;
use Tests\TestCase;
use Mockery\MockInterface;
use App\Jobs\Steam\CreateSteamAppByIdJob;
use Illuminate\Support\Facades\{Bus, Queue};
use App\Contracts\Services\SteamServiceInterface;

class CreateSteamAppByIdJobTest extends TestCase
{
    /**
     * The mock steam service.
     *
     * @var \Mockery\MockInterface
     */
    private MockInterface $steamService;

    /**
     * Setup new application tests.
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->steamService = Mockery::mock(SteamServiceInterface::class);

        $this->app->instance(SteamServiceInterface::class, $this->steamService);
    }

    /**
     * Test if job can be dispatched.
     *
     * @return void
     */
    public function test_if_the_job_can_be_dispatched(): void
    {
        Bus::fake();

        $appId = '123';

        Bus::dispatch(new CreateSteamAppByIdJob($appId));

        Bus::assertDispatched(CreateSteamAppByIdJob::class, function (CreateSteamAppByIdJob $job) use ($appId) {
            return $job->appId === $appId;
        });
    }

    /**
     * Test if job can be queued.
     *
     * @return void
     */
    public function test_if_the_job_can_be_queued(): void
    {
        Queue::fake();

        $appId = '123';

        Bus::dispatch(new CreateSteamAppByIdJob($appId));

        Queue::assertPushed(CreateSteamAppByIdJob::class, function (CreateSteamAppByIdJob $job) use ($appId) {
            return $job->appId === $appId;
        });
    }

    /**
     * Test if can save a steam app by app id on job dispatch.
     *
     * @return void
     */
    public function test_if_can_save_a_steam_app_by_app_id_on_job_dispatch(): void
    {
        $appId = '123';

        $this->steamService
            ->shouldReceive('saveSteamApp')
            ->once()
            ->with($appId)
            ->andReturnNull();

        $job = new CreateSteamAppByIdJob($appId);

        $job->handle();

        $this->assertEquals(1, Mockery::getContainer()->mockery_getExpectationCount(), 'Mock expectations match.');
    }

    /**
     * Tear down application tests.
     *
     * @return void
     */
    public function tearDown(): void
    {
        Mockery::close();

        parent::tearDown();
    }
}
