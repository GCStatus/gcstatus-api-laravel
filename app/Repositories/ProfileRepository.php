<?php

namespace App\Repositories;

use App\Models\{User, Profile};
use App\Contracts\Repositories\ProfileRepositoryInterface;

class ProfileRepository implements ProfileRepositoryInterface
{
    /**
     * Update given user profile.
     *
     * @param \App\Models\User $user
     * @param array<string, mixed> $data
     * @return \App\Models\Profile
     */
    public function updateForUser(User $user, array $data): Profile
    {
        /** @var \App\Models\Profile $profile */
        $profile = $user->profile;

        $profile->update($data);

        /** @phpstan-ignore-next-line */
        return $profile->fresh();
    }
}
