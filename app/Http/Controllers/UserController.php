<?php

namespace App\Http\Controllers;

use App\Http\Resources\UserResource;
use App\Http\Requests\User\BasicUpdateRequest;
use App\Contracts\Services\{
    AuthServiceInterface,
    UserServiceInterface,
    CacheServiceInterface,
};

class UserController extends Controller
{
    /**
     * The auth service.
     *
     * @var \App\Contracts\Services\AuthServiceInterface
     */
    private $authService;

    /**
     * The user service.
     *
     * @var \App\Contracts\Services\UserServiceInterface
     */
    private $userService;

    /**
     * The cache service.
     *
     * @var \App\Contracts\Services\CacheServiceInterface
     */
    private $cacheService;

    /**
     * Create a new class instance.
     *
     * @param \App\Contracts\Services\AuthServiceInterface $authService
     * @param \App\Contracts\Services\UserServiceInterface $userService
     * @param \App\Contracts\Services\CacheServiceInterface $cacheService
     * @return void
     */
    public function __construct(
        AuthServiceInterface $authService,
        UserServiceInterface $userService,
        CacheServiceInterface $cacheService,
    ) {
        $this->authService = $authService;
        $this->userService = $userService;
        $this->cacheService = $cacheService;
    }

    /**
     * Get the authenticated user data.
     *
     * @return \App\Http\Resources\UserResource
     */
    public function me(): UserResource
    {
        return UserResource::make(
            $this->authService->getAuthUser(),
        );
    }

    /**
     * Update user basic informations.
     *
     * @param \App\Http\Requests\User\BasicUpdateRequest $request
     * @return \App\Http\Resources\UserResource
     */
    public function updateBasics(BasicUpdateRequest $request): UserResource
    {
        /** @var array<string, mixed> $data */
        $data = $request->validated();

        /** @var \App\Models\User $user */
        $user = $this->authService->getAuthUser();

        $this->userService->update($data, $user->id);

        $key = "auth.user.{$user->id}";

        $this->cacheService->forget($key);

        return UserResource::make(
            $user->fresh(),
        );
    }
}
