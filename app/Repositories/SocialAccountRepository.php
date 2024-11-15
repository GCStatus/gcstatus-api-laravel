<?php

namespace App\Repositories;

use App\Models\SocialAccount;
use App\Contracts\Repositories\SocialAccountRepositoryInterface;

class SocialAccountRepository implements SocialAccountRepositoryInterface
{
    /**
     * Get the first social account or create if not exists.
     *
     * @param array<string, mixed> $searchable
     * @param array<string, mixed> $creatable
     * @return \App\Models\SocialAccount
     */
    public function firstOrCreate(array $searchable, array $creatable): SocialAccount
    {
        return SocialAccount::firstOrCreate($searchable, $creatable);
    }
}
