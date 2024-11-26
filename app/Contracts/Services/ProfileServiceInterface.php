<?php

namespace App\Contracts\Services;

use App\Models\{User, Profile};
use Illuminate\Http\UploadedFile;

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

    /**
     * Update profile picture.
     *
     * @param \App\Models\User $user
     * @param \Illuminate\Http\UploadedFile $file
     * @return void
     */
    public function updatePicture(User $user, UploadedFile $file): void;
}
