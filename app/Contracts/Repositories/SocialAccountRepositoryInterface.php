<?php

namespace App\Contracts\Repositories;

use App\Models\SocialAccount;

interface SocialAccountRepositoryInterface
{
    /**
     * Get the first social account or create if not exists.
     *
     * @param array<string, mixed> $searchable
     * @param array<string, mixed> $creatable
     * @return \App\Models\SocialAccount
     */
    public function firstOrCreate(array $searchable, array $creatable): SocialAccount;
}
