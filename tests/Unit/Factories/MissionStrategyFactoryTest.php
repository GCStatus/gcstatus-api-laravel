<?php

namespace Tests\Unit\Factories;

use Mockery;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use App\Models\MissionRequirement;
use App\Factories\MissionStrategyFactory;
use App\Contracts\Strategies\MissionStrategyInterface;

class MissionStrategyFactoryTest extends TestCase
{
    /**
     * Test the register and resolve strategy.
     *
     * @return void
     */
    public function test_register_and_resolve_strategy(): void
    {
        $factory = new MissionStrategyFactory();
        $mockStrategy = $this->createMock(MissionStrategyInterface::class);

        $key = 'make_transactions';

        $factory->register($key, $mockStrategy);

        $requirement = new MissionRequirement(['key' => $key]);

        $resolvedStrategy = $factory->resolve($requirement);

        $this->assertSame($mockStrategy, $resolvedStrategy);
    }

    /**
     * Test if can't resolve non registered strategy and throws exception.
     *
     * @return void
     */
    public function test_if_cant_resolve_non_registered_strategy_and_throws_exception(): void
    {
        $factory = new MissionStrategyFactory();
        $requirement = new MissionRequirement(['key' => 'non_existing_key']);

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('No strategy found for key: non_existing_key');

        $factory->resolve($requirement);
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
