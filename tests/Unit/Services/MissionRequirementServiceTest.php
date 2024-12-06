<?php

namespace Tests\Unit\Services;

use Mockery;
use Tests\TestCase;
use Mockery\MockInterface;
use App\Models\MissionRequirement;
use Illuminate\Database\Eloquent\Collection;
use App\Contracts\Services\MissionRequirementServiceInterface;
use App\Contracts\Repositories\MissionRequirementRepositoryInterface;

class MissionRequirementServiceTest extends TestCase
{
    /**
     * The mission requirement repository.
     *
     * @var \Mockery\MockInterface
     */
    private MockInterface $missionRequirementRepository;

    /**
     * The mission requirement service.
     *
     * @var \App\Contracts\Services\MissionRequirementServiceInterface
     */
    private MissionRequirementServiceInterface $missionService;

    /**
     * Setup new test environments.
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->missionRequirementRepository = Mockery::mock(MissionRequirementRepositoryInterface::class);

        $this->app->instance(MissionRequirementRepositoryInterface::class, $this->missionRequirementRepository);

        $this->missionService = app(MissionRequirementServiceInterface::class);
    }

    /**
     * Test if can find a mission requirement by key.
     *
     * @return void
     */
    public function test_if_can_find_a_mission_requirement_by_key(): void
    {
        $key = 'mock_key';

        $missionRequirement = Mockery::mock(MissionRequirement::class);
        $missionRequirement->shouldReceive('getAttribute')->with('key')->andReturn($key);

        $collection = Collection::make([$missionRequirement]);

        $this->missionRequirementRepository
            ->shouldReceive('findByKey')
            ->once()
            ->with($key)
            ->andReturn($collection);

        $result = $this->missionService->findByKey($key);

        $this->assertSame($result, $collection);
    }

    /**
     * Cleanup Mockery after each test.
     *
     * @return void
     */
    public function tearDown(): void
    {
        Mockery::close();

        parent::tearDown();
    }
}
