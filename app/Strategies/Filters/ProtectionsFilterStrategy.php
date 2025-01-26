<?php

namespace App\Strategies\Filters;

use Illuminate\Database\Eloquent\Builder;
use App\Contracts\Strategies\FilterStrategyInterface;

class ProtectionsFilterStrategy implements FilterStrategyInterface
{
    /**
     * @inheritDoc
     */
    public function apply(Builder $query, string $value): Builder
    {
        return $query->whereHas('crack', function (Builder $q) use ($value) {
            $q->whereHas('protection', function (Builder $q) use ($value) {
                $q->where('slug', $value);
            });
        });
    }
}
