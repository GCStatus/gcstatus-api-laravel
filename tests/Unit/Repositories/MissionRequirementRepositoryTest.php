<?php

namespace Tests\Unit\Repositories;

use Mockery;
use Tests\TestCase;
use App\Models\{Status, MissionRequirement};
use PHPUnit\Framework\Attributes\RunInSeparateProcess;
use Illuminate\Database\Eloquent\{Builder, Collection};
use App\Contracts\Repositories\MissionRequirementRepositoryInterface;

class MissionRequirementRepositoryTest extends TestCase
{
    /**
     * The mission requirement repository.
     *
     * @var \App\Contracts\Repositories\MissionRequirementRepositoryInterface
     */
    private MissionRequirementRepositoryInterface $missionRequirementRepository;

    /**
     * Setup new test environments.
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->missionRequirementRepository = app(MissionRequirementRepositoryInterface::class);
    }

    /**
     * Test if can get mission requirements for given key when available.
     *
     * @return void
     */
    #[RunInSeparateProcess]
    public function test_if_can_get_mission_requirements_for_given_key_when_available(): void
    {
        $key = 'mock_mission_key';
        $queryBuilderMock = Mockery::mock(Builder::class);
        $missionRequirementMock = Mockery::mock('overload:' . MissionRequirement::class);

        $queryBuilderMock->shouldReceive('where')
            ->with('key', $key)
            ->once()
            ->andReturnSelf();

        $queryBuilderMock->shouldReceive('whereHas')
            ->once()
            ->with('mission', Mockery::on(function (callable $closure) {
                $missionQueryMock = Mockery::mock(Builder::class);
                $missionQueryMock->shouldReceive('where')
                    ->with('status_id', Status::AVAILABLE_STATUS_ID)
                    ->andReturnSelf();

                $closure($missionQueryMock);

                return true;
            }))->andReturnSelf();

        $queryBuilderMock->shouldReceive('get')->once()->andReturn(
            $expected = Collection::make([$missionRequirementMock])
        );

        $missionRequirementMock->shouldReceive('query')->once()->andReturn($queryBuilderMock);

        $results = $this->missionRequirementRepository->findByKey($key);

        $this->assertEquals($results, $expected);
        $this->assertEquals(4, Mockery::getContainer()->mockery_getExpectationCount(), 'Mock expectations meet.');
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
