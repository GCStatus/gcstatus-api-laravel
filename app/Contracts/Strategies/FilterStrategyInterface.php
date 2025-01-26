<?php

namespace App\Contracts\Strategies;

use Illuminate\Database\Eloquent\Builder;

interface FilterStrategyInterface
{
    /**
     * Apply a new filter.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $value
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function apply(Builder $query, string $value): Builder;
}
