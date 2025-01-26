<?php

namespace Tests\Unit\Factories;

use Mockery;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use App\Factories\FilterStrategyFactory;
use App\Contracts\Strategies\FilterStrategyInterface;

class FilterStrategyFactoryTest extends TestCase
{
    /**
     * Test the register and resolve strategy.
     *
     * @return void
     */
    public function test_register_and_resolve_strategy(): void
    {
        $factory = new FilterStrategyFactory();
        $mockStrategy = $this->createMock(FilterStrategyInterface::class);

        $key = 'tags';

        $factory->register($key, $mockStrategy);

        $resolvedStrategy = $factory->resolve($key);

        $this->assertSame($mockStrategy, $resolvedStrategy);
    }

    /**
     * Test if can't resolve non registered strategy and throws exception.
     *
     * @return void
     */
    public function test_if_cant_resolve_non_registered_strategy_and_throws_exception(): void
    {
        $factory = new FilterStrategyFactory();

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('No strategy found for attribute: non_existing_attribute');

        $key = 'non_existing_attribute';

        $factory->resolve($key);
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
