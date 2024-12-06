<?php

namespace Tests\Unit\Services;

use Mockery;
use PHPUnit\Framework\TestCase;
use App\Models\{Mission, User, MissionRequirement};
use App\Services\ProgressCalculatorService;
use App\Contracts\Strategies\MissionStrategyInterface;
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
        $mockFactory = $this->createMock(MissionStrategyFactoryInterface::class);
        $mockStrategy = $this->createMock(MissionStrategyInterface::class);

        $user = $this->createMock(User::class);
        $requirement = $this->createMock(MissionRequirement::class);

        $mockFactory->expects($this->once())
            ->method('resolve')
            ->with($requirement)
            ->willReturn($mockStrategy);

        $mockStrategy->expects($this->once())
            ->method('calculateProgress')
            ->with($user, $requirement)
            ->willReturn(10);

        $service = new ProgressCalculatorService($mockFactory);

        $progress = $service->determineProgress($user, $requirement);

        $this->assertEquals(10, $progress);
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
