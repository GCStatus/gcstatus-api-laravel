<?php

namespace App\Factories;

use InvalidArgumentException;
use App\Contracts\Strategies\FilterStrategyInterface;
use App\Contracts\Factories\FilterStrategyFactoryInterface;

class FilterStrategyFactory implements FilterStrategyFactoryInterface
{
    /**
     * The registry array.
     *
     * @var array<string, \App\Contracts\Strategies\FilterStrategyInterface>
     */
    private array $registry = [];

    /**
     * Register a strategy for a specific key.
     *
     * @param string $key
     * @param \App\Contracts\Strategies\FilterStrategyInterface $strategy
     */
    public function register(string $key, FilterStrategyInterface $strategy): void
    {
        $this->registry[$key] = $strategy;
    }

    /**
     * Resolve a strategy for the given mission requirement.
     *
     * @param string $attribute
     * @return \App\Contracts\Strategies\FilterStrategyInterface
     * @throws InvalidArgumentException
     */
    public function resolve(string $attribute): FilterStrategyInterface
    {
        if (!isset($this->registry[$attribute])) {
            throw new InvalidArgumentException("No strategy found for attribute: {$attribute}");
        }

        return $this->registry[$attribute];
    }
}
