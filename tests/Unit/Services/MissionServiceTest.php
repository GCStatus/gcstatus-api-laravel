<?php

namespace Tests\Unit\Services;

use Mockery;
use Tests\TestCase;
use Mockery\MockInterface;
use App\Jobs\CompleteMissionJob;
use Illuminate\Support\Facades\Bus;
use App\Models\{User, Mission, Status};
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Contracts\Repositories\MissionRepositoryInterface;
use App\Contracts\Services\{
    AuthServiceInterface,
    MissionServiceInterface,
    ProgressCalculatorServiceInterface,
};
use App\Exceptions\Mission\{
    MissionIsNotAvailableException,
    MissionIsNotCompletedException,
    UserDoesntBelongsToMissionException,
};

class MissionServiceTest extends TestCase
{
    /**
     * The mission repository mock.
     *
     * @var \Mockery\MockInterface
     */
    private MockInterface $missionRepository;

    /**
     * The auth service mock.
     *
     * @var \Mockery\MockInterface
     */
    private MockInterface $authService;

    /**
     * The progress calculator.
     *
     * @var \Mockery\MockInterface
     */
    private MockInterface $progressCalculator;

    /**
     * The mission service.
     *
     * @var \App\Contracts\Services\MissionServiceInterface
     */
    private MissionServiceInterface $missionService;

    /**
     * Setup new test environments.
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->authService = Mockery::mock(AuthServiceInterface::class);
        $this->missionRepository = Mockery::mock(MissionRepositoryInterface::class);
        $this->progressCalculator = Mockery::mock(ProgressCalculatorServiceInterface::class);

        $this->app->instance(AuthServiceInterface::class, $this->authService);
        $this->app->instance(MissionRepositoryInterface::class, $this->missionRepository);
        $this->app->instance(ProgressCalculatorServiceInterface::class, $this->progressCalculator);

        $this->missionService = app(MissionServiceInterface::class);
    }

    /**
     * Test if can get all missions for authenticated user.
     *
     * @return void
     */
    public function test_if_can_get_all_missions_for_authenticated_user(): void
    {
        $perPage = 10;
        $currentPage = 1;

        $user = Mockery::mock(User::class);
        $mission = Mockery::mock(Mission::class);

        $missions = Collection::make([$mission, $mission, $mission]);

        $missionCollection = new LengthAwarePaginator(
            $missions->slice(($currentPage - 1) * $perPage, $perPage),
            $missions->count(),
            $perPage,
            $currentPage,
        );

        $this->authService
            ->shouldReceive('getAuthUser')
            ->once()
            ->withNoArgs()
            ->andReturn($user);

        $this->missionRepository
            ->shouldReceive('allForUser')
            ->once()
            ->with($user)
            ->andReturn($missionCollection);

        $this->missionService->allForUser();

        $this->assertEquals(2, Mockery::getContainer()->mockery_getExpectationCount(), 'Mock expectations met.');
    }

    /**
     * Test if can complete mission if conditions are satisfied.
     *
     * @return void
     */
    public function test_if_can_complete_mission_if_conditions_are_satisfied(): void
    {
        Bus::fake();

        $missionId = 1;

        $user = Mockery::mock(User::class);
        $mission = Mockery::mock(Mission::class);

        $mission->shouldReceive('getAttribute')->with('for_all')->andReturnTrue();
        $mission->shouldReceive('getAttribute')->with('id')->andReturn($missionId);
        $mission->shouldReceive('getAttribute')->with('status_id')->andReturn(Status::AVAILABLE_STATUS_ID);

        $this->authService
            ->shouldReceive('getAuthUser')
            ->once()
            ->withNoArgs()
            ->andReturn($user);

        $this->missionRepository
            ->shouldReceive('findOrFail')
            ->once()
            ->with($missionId)
            ->andReturn($mission);

        $mission->shouldReceive('load')
            ->once()
            ->with('users');

        $this->progressCalculator
            ->shouldReceive('isMissionComplete')
            ->once()
            ->with($user, $mission)
            ->andReturnTrue();

        $this->missionService->complete($missionId);

        Bus::assertDispatched(CompleteMissionJob::class, function (CompleteMissionJob $job) use ($user, $mission) {
            return $job->user === $user && $job->mission === $mission;
        });

        $this->assertEquals(4, Mockery::getContainer()->mockery_getExpectationCount(), 'Mock expectations met.');
    }

    /**
     * Test if can complete mission if mission is not for all but user is target.
     *
     * @return void
     */
    public function test_if_can_complete_mission_if_mission_is_not_for_all_but_user_is_taget(): void
    {
        Bus::fake();

        $userId = 2;
        $missionId = 1;

        $user = Mockery::mock(User::class)->makePartial();
        $mission = Mockery::mock(Mission::class)->makePartial();
        $pivotMock = Mockery::mock();

        /** @phpstan-ignore property.notFound */
        $pivotMock->user_id = $userId;

        /** @phpstan-ignore property.notFound */
        $user->pivot = $pivotMock;

        $user->shouldReceive('getAttribute')->with('id')->andReturn($userId);

        $mission->shouldReceive('getAttribute')->with('id')->andReturn($missionId);
        $mission->shouldReceive('getAttribute')->with('for_all')->andReturnFalse();
        $mission->shouldReceive('getAttribute')->with('status_id')->andReturn(Status::AVAILABLE_STATUS_ID);

        $this->authService
            ->shouldReceive('getAuthUser')
            ->once()
            ->withNoArgs()
            ->andReturn($user);

        $this->missionRepository
            ->shouldReceive('findOrFail')
            ->once()
            ->with($missionId)
            ->andReturn($mission);

        $this->progressCalculator
            ->shouldReceive('isMissionComplete')
            ->once()
            ->with($user, $mission)
            ->andReturnTrue();

        $mission->shouldReceive('load')
            ->once()
            ->with('users');

        $mission->shouldReceive('getAttribute')
            ->once()
            ->with('users')
            ->andReturn(Collection::make([$user]));

        $this->missionService->complete($missionId);

        Bus::assertDispatched(CompleteMissionJob::class, function (CompleteMissionJob $job) use ($user, $mission) {
            return $job->user === $user && $job->mission === $mission;
        });

        $this->assertEquals(5, Mockery::getContainer()->mockery_getExpectationCount(), 'Mock expectations met.');
    }

    /**
     * Test if can fail if given mission doesn't exist on complete.
     *
     * @return void
     */
    public function test_if_can_fail_if_given_mission_doesnt_exist_on_complete(): void
    {
        $missionId = 1;

        $user = Mockery::mock(User::class);
        $mission = Mockery::mock(Mission::class);

        $mission->shouldReceive('getAttribute')->with('id')->andReturn(2);
        $mission->shouldReceive('getAttribute')->with('for_all')->andReturnTrue();
        $mission->shouldReceive('getAttribute')->with('status_id')->andReturn(Status::AVAILABLE_STATUS_ID);

        $this->authService
            ->shouldReceive('getAuthUser')
            ->once()
            ->withNoArgs()
            ->andReturn($user);

        $this->missionRepository
            ->shouldReceive('findOrFail')
            ->once()
            ->with($missionId)
            ->andThrow(ModelNotFoundException::class);

        $this->expectException(ModelNotFoundException::class);

        $this->progressCalculator->shouldNotReceive('isMissionComplete');

        $mission->shouldNotReceive('load');

        $this->missionService->complete($missionId);

        $this->assertEquals(4, Mockery::getContainer()->mockery_getExpectationCount(), 'Mock expectations met.');
    }

    /**
     * Test if can fail if given mission is not available on complete.
     *
     * @return void
     */
    public function test_if_can_fail_if_given_mission_is_not_available_on_complete(): void
    {
        $missionId = 1;

        $user = Mockery::mock(User::class);
        $mission = Mockery::mock(Mission::class);

        $mission->shouldReceive('getAttribute')->with('for_all')->andReturnTrue();
        $mission->shouldReceive('getAttribute')->with('id')->andReturn($missionId);
        $mission->shouldReceive('getAttribute')->with('status_id')->andReturn(Status::UNAVAILABLE_STATUS_ID);

        $this->authService
            ->shouldReceive('getAuthUser')
            ->once()
            ->withNoArgs()
            ->andReturn($user);

        $this->missionRepository
            ->shouldReceive('findOrFail')
            ->once()
            ->with($missionId)
            ->andReturn($mission);

        $this->expectException(MissionIsNotAvailableException::class);
        $this->expectExceptionMessage('The given mission is not available.');

        $this->progressCalculator->shouldNotReceive('isMissionComplete');

        $mission->shouldNotReceive('load');

        $this->missionService->complete($missionId);

        $this->assertEquals(4, Mockery::getContainer()->mockery_getExpectationCount(), 'Mock expectations met.');
    }

    /**
     * Test if can fail if given mission is not complete on complete.
     *
     * @return void
     */
    public function test_if_can_fail_if_given_mission_is_not_complete_on_complete(): void
    {
        $missionId = 1;

        $user = Mockery::mock(User::class);
        $mission = Mockery::mock(Mission::class);

        $mission->shouldReceive('getAttribute')->with('for_all')->andReturnTrue();
        $mission->shouldReceive('getAttribute')->with('id')->andReturn($missionId);
        $mission->shouldReceive('getAttribute')->with('status_id')->andReturn(Status::AVAILABLE_STATUS_ID);

        $this->authService
            ->shouldReceive('getAuthUser')
            ->once()
            ->withNoArgs()
            ->andReturn($user);

        $this->missionRepository
            ->shouldReceive('findOrFail')
            ->once()
            ->with($missionId)
            ->andReturn($mission);

        $mission->shouldReceive('load')
            ->once()
            ->with('users');

        $this->progressCalculator
            ->shouldReceive('isMissionComplete')
            ->once()
            ->with($user, $mission)
            ->andReturnFalse();

        $this->expectException(MissionIsNotCompletedException::class);
        $this->expectExceptionMessage('You did not complete this mission yet. Please, double check it and try again later!');

        $this->missionService->complete($missionId);

        $this->assertEquals(4, Mockery::getContainer()->mockery_getExpectationCount(), 'Mock expectations met.');
    }

    /**
     * Test if can fail if given mission is not for_all and user is not target on complete.
     *
     * @return void
     */
    public function test_if_can_fail_if_given_mission_is_not_for_all_and_user_is_not_target_on_complete(): void
    {
        $userId = 2;
        $missionId = 1;

        $user = Mockery::mock(User::class);
        $mission = Mockery::mock(Mission::class);

        $user->shouldReceive('getAttribute')->with('id')->andReturn($userId);

        $mission->shouldReceive('getAttribute')->with('id')->andReturn($missionId);
        $mission->shouldReceive('getAttribute')->with('for_all')->andReturnFalse();
        $mission->shouldReceive('getAttribute')->with('status_id')->andReturn(Status::AVAILABLE_STATUS_ID);

        $this->authService
            ->shouldReceive('getAuthUser')
            ->once()
            ->withNoArgs()
            ->andReturn($user);

        $this->missionRepository
            ->shouldReceive('findOrFail')
            ->once()
            ->with($missionId)
            ->andReturn($mission);

        $this->progressCalculator->shouldNotReceive('isMissionComplete');

        $mission->shouldReceive('load')
            ->once()
            ->with('users');

        $mission->shouldReceive('getAttribute')
            ->once()
            ->with('users')
            ->andReturn(Collection::make([]));

        $this->expectException(UserDoesntBelongsToMissionException::class);
        $this->expectExceptionMessage('Ops! Something wrong happened: you can not complete the given mission.');

        $this->missionService->complete($missionId);

        $this->assertEquals(4, Mockery::getContainer()->mockery_getExpectationCount(), 'Mock expectations met.');
    }

    /**
     * Tear down test environments.
     *
     * @return void
     */
    public function tearDown(): void
    {
        Mockery::close();

        parent::tearDown();
    }
}
