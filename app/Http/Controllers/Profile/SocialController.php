<?php

namespace App\Http\Controllers\Profile;

use App\Http\Resources\UserResource;
use App\Http\Controllers\Controller;
use App\Http\Requests\Profile\SocialUpdateRequest;
use App\Contracts\Services\{
    AuthServiceInterface,
    CacheServiceInterface,
    ProfileServiceInterface,
};

class SocialController extends Controller
{
    /**
     * The auth service.
     *
     * @var \App\Contracts\Services\AuthServiceInterface
     */
    private $authService;

    /**
     * The cache service.
     *
     * @var \App\Contracts\Services\CacheServiceInterface
     */
    private $cacheService;

    /**
     * The profile service.
     *
     * @var \App\Contracts\Services\ProfileServiceInterface
     */
    private $profileService;

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
     * @param \App\Http\Requests\Profile\SocialUpdateRequest $request
     * @return \App\Http\Resources\UserResource
     */
    public function __invoke(SocialUpdateRequest $request): UserResource
    {
        /** @var array<string, mixed> $data */
        $data = $request->validated();

        /** @var \App\Models\User $user */
        $user = $this->authService->getAuthUser();

        $this->profileService->updateForUser($user, $data);

        $key = "auth.user.{$user->id}";

        $this->cacheService->forget($key);

        return UserResource::make($user);
    }
}
