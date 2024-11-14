<?php

namespace App\Services;

use App\Models\{Profile, User};
use App\Contracts\Services\ProfileServiceInterface;
use App\Contracts\Repositories\ProfileRepositoryInterface;

class ProfileService implements ProfileServiceInterface
{
    /**
     * The profile repository.
     *
     * @var \App\Contracts\Repositories\ProfileRepositoryInterface
     */
    private $profileRepository;

    /**
     * Create a new class instance.
     *
     * @param \App\Contracts\Repositories\ProfileRepositoryInterface $profileRepository
     * @return void
     */
    public function __construct(ProfileRepositoryInterface $profileRepository)
    {
        $this->profileRepository = $profileRepository;
    }

    /**
     * Update given user profile.
     *
     * @param \App\Models\User $user
     * @param array<string, mixed> $data
     * @return \App\Models\Profile
     */
    public function updateForUser(User $user, array $data): Profile
    {
        return $this->profileRepository->updateForUser($user, $data);
    }
}
