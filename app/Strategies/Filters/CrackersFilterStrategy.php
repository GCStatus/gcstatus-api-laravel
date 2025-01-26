<?php

namespace App\Strategies\Filters;

use Illuminate\Database\Eloquent\Builder;
use App\Contracts\Strategies\FilterStrategyInterface;

class CrackersFilterStrategy implements FilterStrategyInterface
{
    /**
     * @inheritDoc
     */
    public function apply(Builder $query, string $value): Builder
    {
        return $query->whereHas('crack', function (Builder $q) use ($value) {
            $q->whereHas('cracker', function (Builder $q) use ($value) {
                $q->where('slug', $value);
            });
        });
    }
}
