<?php

namespace App\Strategies\Filters;

use Illuminate\Database\Eloquent\Builder;
use App\Contracts\Strategies\FilterStrategyInterface;

class CracksFilterStrategy implements FilterStrategyInterface
{
    /**
     * @inheritDoc
     */
    public function apply(Builder $query, string $value): Builder
    {
        return $query->whereHas('crack', function (Builder $q) use ($value) {
            $q->whereHas('status', function (Builder $q) use ($value) {
                if ($value === 'cracked') {
                    $q->whereIn('name', ['cracked', 'cracked-oneday']);
                } else {
                    $q->where('name', $value);
                }
            });
        });
    }
}
