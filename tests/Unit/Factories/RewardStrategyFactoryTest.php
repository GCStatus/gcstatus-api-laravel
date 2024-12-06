<?php

namespace Tests\Unit\Factories;

use Mockery;
use Tests\TestCase;
use InvalidArgumentException;
use App\Models\{Title, Rewardable};
use App\Factories\RewardStrategyFactory;
use App\Contracts\Strategies\RewardStrategyInterface;

class RewardStrategyFactoryTest extends TestCase
{
    /**
     * Test the register and resolve strategy.
     *
     * @return void
     */
    public function test_register_and_resolve_strategy(): void
    {
        $factory = new RewardStrategyFactory();
        $mockStrategy = $this->createMock(RewardStrategyInterface::class);

        $type = Title::class;

        $rewardable = new Rewardable(['rewardable_type' => $type]);

        $factory->register($type, $mockStrategy);

        $resolvedStrategy = $factory->resolve($rewardable);

        $this->assertSame($mockStrategy, $resolvedStrategy);
    }

    /**
     * Test if can't resolve non registered strategy and throws exception.
     *
     * @return void
     */
    public function test_if_cant_resolve_non_registered_strategy_and_throws_exception(): void
    {
        $factory = new RewardStrategyFactory();
        $requirement = new Rewardable(['rewardable_type' => 'non_existing_type']);

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('No strategy found for reward: non_existing_type');

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
