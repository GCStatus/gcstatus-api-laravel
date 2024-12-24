<?php

namespace Tests\Unit\Repositories;

use Mockery;
use Tests\TestCase;
use App\Models\{User, Mission};
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\HasOne;
use PHPUnit\Framework\Attributes\RunInSeparateProcess;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use App\Contracts\Repositories\MissionRepositoryInterface;

class MissionRepositoryTest extends TestCase
{
    /**
     * The mission repository.
     *
     * @var \App\Contracts\Repositories\MissionRepositoryInterface
     */
    private MissionRepositoryInterface $missionRepository;

    /**
     * Setup new test environmnets.
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->missionRepository = app(MissionRepositoryInterface::class);
    }

    /**
     * Test if can get all missions for auth user.
     *
     * @return void
     */
    #[RunInSeparateProcess]
    public function test_if_can_get_all_missions_for_auth_user(): void
    {
        $userId = 1;

        $user = Mockery::mock(User::class);
        $user->shouldReceive('getAttribute')->with('id')->andReturn($userId);

        $paginatedResult = Mockery::mock(LengthAwarePaginator::class);

        $builder = Mockery::mock(Builder::class);
        $builder->shouldReceive('paginate')->once()->with(10)->andReturn($paginatedResult);

        $mission = Mockery::mock('overload:' . Mission::class);
        $mission->shouldReceive('query')->once()->andReturn($builder);

        $builder->shouldReceive('with')
            ->once()
            ->with(Mockery::on(function (array $relationships) use ($userId) {
                $userMissionCallback = $relationships['userMission'];
                $userMissionQuery = Mockery::mock(HasOne::class);
                $userMissionQuery->shouldReceive('where')->with('user_id', $userId)->andReturnSelf();
                $userMissionCallback($userMissionQuery);

                $userProgressCallback = $relationships['requirements.userProgress'];
                $userProgressQuery = Mockery::mock(HasOne::class);
                $userProgressQuery->shouldReceive('where')->with('user_id', $userId)->andReturnSelf();
                $userProgressCallback($userProgressQuery);

                return true;
            }))->andReturnSelf();

        $builder->shouldReceive('where')
            ->once()
            ->with(Mockery::on(function (callable $callback) use ($userId) {
                $query = Mockery::mock(Builder::class);

                $query->shouldReceive('where')->with('for_all', true)->andReturnSelf();
                $query->shouldReceive('orWhereHas')
                    ->with('users', Mockery::on(function (callable $callback) use ($userId) {
                        $nestedQuery = Mockery::mock(Builder::class);
                        $nestedQuery->shouldReceive('where')->with('user_id', $userId)->andReturnSelf();
                        $callback($nestedQuery);
                        return true;
                    }))->andReturnSelf();

                $callback($query);
                return true;
            }))->andReturnSelf();

        /** @var \App\Models\User $user */
        $result = $this->missionRepository->allForUser($user);

        $this->assertEquals($paginatedResult, $result);
    }

    /**
     * Test if can find or fail the mission by id.
     *
     * @return void
     */
    #[RunInSeparateProcess]
    public function test_if_can_find_or_fail_the_mission_by_id(): void
    {
        $missionId = 1;

        $mission = Mockery::mock('overload:' . Mission::class);
        $mission->shouldReceive('getAttribute')->with('id')->andReturn($missionId);

        $mission->shouldReceive('findOrFail')
            ->once()
            ->with($missionId)
            ->andReturn($mission);

        $result = $this->missionRepository->findOrFail($missionId);

        $this->assertEquals($result, $mission);
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
