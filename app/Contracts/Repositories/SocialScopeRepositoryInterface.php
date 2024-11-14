<?php

namespace App\Contracts\Repositories;

use App\Models\SocialScope;

interface SocialScopeRepositoryInterface
{
    /**
     * Get the first social scope or create if not exists.
     *
     * @param array<string, mixed> $searchable
     * @param array<string, mixed> $creatable
     * @return \App\Models\SocialScope
     */
    public function firstOrCreate(array $searchable, array $creatable): SocialScope;
}
