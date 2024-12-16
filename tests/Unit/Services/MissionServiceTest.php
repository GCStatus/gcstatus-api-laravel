<?php

namespace Tests\Unit\Services;

use Mockery;
use Tests\TestCase;
use Mockery\MockInterface;
use App\Models\{User, Mission};
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use App\Contracts\Repositories\MissionRepositoryInterface;
use App\Contracts\Services\{
    AuthServiceInterface,
    MissionServiceInterface,
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

        $this->app->instance(AuthServiceInterface::class, $this->authService);
        $this->app->instance(MissionRepositoryInterface::class, $this->missionRepository);

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
