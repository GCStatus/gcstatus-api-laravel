<?php

namespace Tests\Unit\Services;

use Mockery;
use PHPUnit\Framework\TestCase;
use App\Models\{User, MissionRequirement};
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
