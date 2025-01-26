<?php

namespace App\Contracts\Factories;

use App\Contracts\Strategies\FilterStrategyInterface;

interface FilterStrategyFactoryInterface
{
    /**
     * Resolve a filter strategy for the given game attribute.
     *
     * @param string $attribute
     * @return \App\Contracts\Strategies\FilterStrategyInterface
     */
    public function resolve(string $attribute): FilterStrategyInterface;

    /**
     * Register a strategy for a specific attribute.
     *
     * @param string $attribute
     * @param \App\Contracts\Strategies\FilterStrategyInterface $strategy
     */
    public function register(string $attribute, FilterStrategyInterface $strategy): void;
}
