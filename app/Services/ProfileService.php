<?php

namespace App\Services;

use App\Models\{Profile, User};
use Illuminate\Http\UploadedFile;
use App\Contracts\Repositories\ProfileRepositoryInterface;
use App\Contracts\Services\{
    AuthServiceInterface,
    ProfileServiceInterface,
    StorageServiceInterface,
};

class ProfileService implements ProfileServiceInterface
{
    /**
     * The profile repository.
     *
     * @var \App\Contracts\Repositories\ProfileRepositoryInterface
     */
    private ProfileRepositoryInterface $profileRepository;

    /**
     * The storage service.
     *
     * @var \App\Contracts\Services\StorageServiceInterface
     */
    private StorageServiceInterface $storageService;

    /**
     * The auth service.
     *
     * @var \App\Contracts\Services\AuthServiceInterface
     */
    private AuthServiceInterface $authService;

    /**
     * Create a new class instance.
     *
     * @param \App\Contracts\Repositories\ProfileRepositoryInterface $profileRepository
     * @param \App\Contracts\Services\StorageServiceInterface $storageService
     * @param \App\Contracts\Services\AuthServiceInterface $authService
     * @return void
     */
    public function __construct(
        ProfileRepositoryInterface $profileRepository,
        StorageServiceInterface $storageService,
        AuthServiceInterface $authService,
    ) {
        $this->profileRepository = $profileRepository;
        $this->storageService = $storageService;
        $this->authService = $authService;
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
        $profile = $this->profileRepository->updateForUser($user, $data);

        $this->authService->forgetAuthUserCache($user);

        return $profile;
    }

    /**
     * Update profile picture.
     *
     * @param \App\Models\User $user
     * @param \Illuminate\Http\UploadedFile $file
     * @return void
     */
    public function updatePicture(User $user, UploadedFile $file): void
    {
        /** @var \App\Models\Profile $profile */
        $profile = $user->profile;

        if ($profile->photo) {
            $this->storageService->delete($profile->photo);
        }

        /** @var string $path */
        $path = $this->storageService->createAs($file, 'profiles', "{$user->nickname}_profile_picture.{$file->getClientOriginalExtension()}");

        $this->updateForUser($user, [
            'photo' => $path,
        ]);
    }
}
