<?php

namespace App\Repositories;

use App\Models\SocialScope;
use App\Contracts\Repositories\SocialScopeRepositoryInterface;

class SocialScopeRepository implements SocialScopeRepositoryInterface
{
    /**
     * Get the first social account or create if not exists.
     *
     * @param array<string, mixed> $searchable
     * @param array<string, mixed> $creatable
     * @return \App\Models\SocialScope
     */
    public function firstOrCreate(array $searchable, array $creatable): SocialScope
    {
        return SocialScope::firstOrCreate($searchable, $creatable);
    }
}
