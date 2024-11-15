<?php

namespace App\Contracts\Services;

use App\Models\{User, Profile};

interface ProfileServiceInterface
{
    /**
     * Update given user profile.
     *
     * @param \App\Models\User $user
     * @param array<string, mixed> $data
     * @return \App\Models\Profile
     */
    public function updateForUser(User $user, array $data): Profile;
}
