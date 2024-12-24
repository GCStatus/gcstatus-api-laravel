<?php

namespace Tests\Unit\Services;

use Mockery;
use PHPUnit\Framework\TestCase;
use App\Services\ProgressCalculatorService;
use App\Contracts\Strategies\MissionStrategyInterface;
use App\Models\{Mission, User, MissionRequirement, Status};
use App\Contracts\Factories\MissionStrategyFactoryInterface;

class ProgressCalculatorServiceTest extends TestCase
{
    /**
     * Test if can determine progress.
     *
     * @return void
     */
    public function test_if_can_determine_progress(): void
    {
        $mockFactory = Mockery::mock(MissionStrategyFactoryInterface::class);
        $mockStrategy = Mockery::mock(MissionStrategyInterface::class);
        $userMock = Mockery::mock(User::class);
        $requirementMock = Mockery::mock(MissionRequirement::class);
        $missionMock = Mockery::mock(Mission::class);

        $requirementMock
            ->shouldReceive('getAttribute')
            ->with('mission')
            ->andReturn($missionMock);

        $missionMock
            ->shouldReceive('getAttribute')
            ->with('status_id')
            ->andReturn(Status::AVAILABLE_STATUS_ID);

        $mockFactory
            ->shouldReceive('resolve')
            ->with($requirementMock)
            ->once()
            ->andReturn($mockStrategy);

        $mockStrategy
            ->shouldReceive('calculateProgress')
            ->with($userMock, $requirementMock)
            ->once()
            ->andReturn(10);

        /** @var \App\Contracts\Factories\MissionStrategyFactoryInterface $mockFactory */
        $service = new ProgressCalculatorService($mockFactory);

        /** @var \App\Models\User $userMock */
        /** @var \App\Models\MissionRequirement $requirementMock */
        $progress = $service->determineProgress($userMock, $requirementMock);

        $this->assertEquals(10, $progress);
    }
    /**
     * Test if can't determine progress if mission is not available.
     *
     * @return void
     */
    public function test_if_cant_determine_progress_if_mission_is_not_available(): void
    {
        $mockFactory = Mockery::mock(MissionStrategyFactoryInterface::class);
        $mockStrategy = Mockery::mock(MissionStrategyInterface::class);
        $userMock = Mockery::mock(User::class);
        $requirementMock = Mockery::mock(MissionRequirement::class);
        $missionMock = Mockery::mock(Mission::class);

        $requirementMock
            ->shouldReceive('getAttribute')
            ->with('mission')
            ->andReturn($missionMock);

        $missionMock
            ->shouldReceive('getAttribute')
            ->with('status_id')
            ->andReturn(9999999);

        $mockFactory->shouldNotReceive('resolve');
        $mockStrategy->shouldNotReceive('calculateProgress');

        /** @var \App\Contracts\Factories\MissionStrategyFactoryInterface $mockFactory */
        $service = new ProgressCalculatorService($mockFactory);

        /** @var \App\Models\User $userMock */
        /** @var \App\Models\MissionRequirement $requirementMock */
        $progress = $service->determineProgress($userMock, $requirementMock);

        $this->assertEquals(0, $progress);
    }

    /**
     * Test if requirement is complete when progress meets or exceeds goal.
     *
     * @return void
     */
    public function test_if_requirement_is_complete_when_progress_meets_or_exceeds_goal(): void
    {
        $mockFactory = Mockery::mock(MissionStrategyFactoryInterface::class);
        $mockService = Mockery::mock(ProgressCalculatorService::class, [$mockFactory])->makePartial();

        /** @var \App\Contracts\Services\ProgressCalculatorServiceInterface $progressCalculator */
        $progressCalculator = $mockService;

        $user = Mockery::mock(User::class);
        $requirement = Mockery::mock(MissionRequirement::class);
        $requirement->shouldReceive('getAttribute')->with('goal')->andReturn(10);

        $mockService->shouldReceive('determineProgress')
            ->with($user, $requirement)
            ->once()
            ->andReturn(10);

        /** @var \App\Models\User $user */
        /** @var \App\Models\MissionRequirement $requirement */
        $result = $progressCalculator->isRequirementComplete($user, $requirement);

        $this->assertTrue($result);
    }

    /**
     * Test if requirement is not complete when progress doesn't meet goal.
     *
     * @return void
     */
    public function test_if_requirement_is_not_complete_when_progress_does_not_meet_goal(): void
    {
        $mockFactory = Mockery::mock(MissionStrategyFactoryInterface::class);
        $mockService = Mockery::mock(ProgressCalculatorService::class, [$mockFactory])->makePartial();

        /** @var \App\Contracts\Services\ProgressCalculatorServiceInterface $progressCalculator */
        $progressCalculator = $mockService;

        $user = Mockery::mock(User::class);
        $requirement = Mockery::mock(MissionRequirement::class);
        $requirement->shouldReceive('getAttribute')->with('goal')->andReturn(10);

        $mockService->shouldReceive('determineProgress')
            ->with($user, $requirement)
            ->once()
            ->andReturn(5);

        /** @var \App\Models\User $user */
        /** @var \App\Models\MissionRequirement $requirement */
        $result = $progressCalculator->isRequirementComplete($user, $requirement);

        $this->assertFalse($result);
    }

    /**
     * Test if mission is complete when all requirements are met.
     *
     * @return void
     */
    public function test_if_mission_is_complete_when_all_requirements_are_met(): void
    {
        $mockFactory = Mockery::mock(MissionStrategyFactoryInterface::class);
        $mockService = Mockery::mock(ProgressCalculatorService::class, [$mockFactory])->makePartial();

        /** @var \App\Contracts\Services\ProgressCalculatorServiceInterface $progressCalculator */
        $progressCalculator = $mockService;

        $user = Mockery::mock(User::class);
        $mission = Mockery::mock(Mission::class);
        $requirement1 = Mockery::mock(MissionRequirement::class);
        $requirement2 = Mockery::mock(MissionRequirement::class);

        $mission->shouldReceive('getAttribute')
            ->with('requirements')
            ->andReturn([$requirement1, $requirement2]);

        $mission->shouldReceive('load')
            ->with('requirements')
            ->once();

        $mockService->shouldReceive('isRequirementComplete')
            ->with($user, $requirement1)
            ->once()
            ->andReturnTrue();

        $mockService->shouldReceive('isRequirementComplete')
            ->with($user, $requirement2)
            ->once()
            ->andReturnTrue();

        /** @var \App\Models\User $user */
        /** @var \App\Models\Mission $mission */
        $result = $progressCalculator->isMissionComplete($user, $mission);

        $this->assertTrue($result);
    }

    /**
     * Test if mission is not complete when any requirement is not met.
     *
     * @return void
     */
    public function test_if_mission_is_not_complete_when_any_requirement_is_not_met(): void
    {
        $mockFactory = Mockery::mock(MissionStrategyFactoryInterface::class);
        $mockService = Mockery::mock(ProgressCalculatorService::class, [$mockFactory])->makePartial();

        /** @var \App\Contracts\Services\ProgressCalculatorServiceInterface $progressCalculator */
        $progressCalculator = $mockService;

        $user = Mockery::mock(User::class);
        $mission = Mockery::mock(Mission::class);
        $requirement1 = Mockery::mock(MissionRequirement::class);
        $requirement2 = Mockery::mock(MissionRequirement::class);

        $mission->shouldReceive('getAttribute')
            ->with('requirements')
            ->andReturn([$requirement1, $requirement2]);

        $mission->shouldReceive('load')
            ->with('requirements')
            ->once();

        $mockService->shouldReceive('isRequirementComplete')
            ->with($user, $requirement1)
            ->once()
            ->andReturnTrue();

        $mockService->shouldReceive('isRequirementComplete')
            ->with($user, $requirement2)
            ->once()
            ->andReturnFalse();

        /** @var \App\Models\User $user */
        /** @var \App\Models\Mission $mission */
        $result = $progressCalculator->isMissionComplete($user, $mission);

        $this->assertFalse($result);
    }

    /**
     * Tear down the tests.
     *
     * @return void
     */
    public function tearDown(): void
    {
        Mockery::close();

        parent::tearDown();
    }
}
