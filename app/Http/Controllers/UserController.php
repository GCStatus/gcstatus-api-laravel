<?php

namespace App\Http\Controllers;

use App\Http\Resources\UserResource;
use App\Http\Requests\User\{
    BasicUpdateRequest,
    SensitiveUpdateRequest,
};
use App\Contracts\Services\{
    AuthServiceInterface,
    UserServiceInterface,
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
     * Create a new class instance.
     *
     * @param \App\Contracts\Services\AuthServiceInterface $authService
     * @param \App\Contracts\Services\UserServiceInterface $userService
     * @return void
     */
    public function __construct(
        AuthServiceInterface $authService,
        UserServiceInterface $userService,
    ) {
        $this->authService = $authService;
        $this->userService = $userService;
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

        return UserResource::make(
            $user->fresh(),
        );
    }

    /**
     * Update user sensitive informations.
     *
     * @param \App\Http\Requests\User\SensitiveUpdateRequest $request
     * @return \App\Http\Resources\UserResource
     */
    public function updateSensitives(SensitiveUpdateRequest $request): UserResource
    {
        /** @var array<string, string> $data */
        $data = $request->validated();

        /** @var \App\Models\User $user */
        $user = $this->authService->getAuthUser();

        $this->userService->updateSensitives($user, $data);

        return UserResource::make(
            $user->fresh(),
        );
    }
}
