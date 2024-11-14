<?php

namespace App\Contracts\Services;

use App\Models\SocialScope;

interface SocialScopeServiceInterface
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
