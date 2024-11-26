<?php

namespace App\Http\Controllers\Profile;

use App\Http\Resources\UserResource;
use App\Http\Controllers\Controller;
use App\Http\Requests\Profile\PictureUpdateRequest;
use App\Contracts\Services\{
    AuthServiceInterface,
    CacheServiceInterface,
    ProfileServiceInterface,
};

class PictureController extends Controller
{
    /**
     * The auth service.
     *
     * @var \App\Contracts\Services\AuthServiceInterface
     */
    private AuthServiceInterface $authService;

    /**
     * The cache service.
     *
     * @var \App\Contracts\Services\CacheServiceInterface
     */
    private CacheServiceInterface $cacheService;

    /**
     * The profile service.
     *
     * @var \App\Contracts\Services\ProfileServiceInterface
     */
    private ProfileServiceInterface $profileService;

    /**
     * Create a new class instance.
     *
     * @param \App\Contracts\Services\AuthServiceInterface $authService
     * @param \App\Contracts\Services\CacheServiceInterface $cacheService
     * @param \App\Contracts\Services\ProfileServiceInterface $profileService
     * @return void
     */
    public function __construct(
        AuthServiceInterface $authService,
        CacheServiceInterface $cacheService,
        ProfileServiceInterface $profileService,
    ) {
        $this->authService = $authService;
        $this->cacheService = $cacheService;
        $this->profileService = $profileService;
    }

    /**
     * Handle the incoming request.
     *
     * @param \App\Http\Requests\Profile\PictureUpdateRequest $request
     * @return \App\Http\Resources\UserResource
     */
    public function __invoke(PictureUpdateRequest $request): UserResource
    {
        /** @var array<string, \Illuminate\Http\UploadedFile> $data */
        $data = $request->validated();

        /** @var \App\Models\User $user */
        $user = $this->authService->getAuthUser();

        $this->profileService->updatePicture($user, $data['file']);

        $key = "auth.user.{$user->id}";

        $this->cacheService->forget($key);

        return UserResource::make($user->fresh('profile'));
    }
}
