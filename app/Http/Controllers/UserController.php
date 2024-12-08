<?php

namespace App\Http\Controllers;

use App\Http\Resources\{
    UserResource,
    TitleResource,
};
use App\Http\Requests\User\{
    BasicUpdateRequest,
    SensitiveUpdateRequest,
};
use App\Contracts\Services\{
    AuthServiceInterface,
    TitleOwnershipServiceInterface,
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
     * The title ownership service.
     *
     * @var \App\Contracts\Services\TitleOwnershipServiceInterface
     */
    private TitleOwnershipServiceInterface $titleOwnershipService;

    /**
     * Create a new class instance.
     *
     * @param \App\Contracts\Services\AuthServiceInterface $authService
     * @param \App\Contracts\Services\UserServiceInterface $userService
     * @param \App\Contracts\Services\TitleOwnershipServiceInterface $titleOwnershipService
     * @return void
     */
    public function __construct(
        AuthServiceInterface $authService,
        UserServiceInterface $userService,
        TitleOwnershipServiceInterface $titleOwnershipService,
    ) {
        $this->authService = $authService;
        $this->userService = $userService;
        $this->titleOwnershipService = $titleOwnershipService;
    }

    /**
     * Get the authenticated user data.
     *
     * @return \App\Http\Resources\UserResource
     */
    public function me(): UserResource
    {
        /** @var \App\Models\User $user */
        $user = $this->authService->getAuthUser();

        /** @var ?\App\Models\Title $title */
        $title = $user->title;

        TitleResource::setTitleOwnershipService($this->titleOwnershipService);
        TitleResource::preloadOwnership([$title?->title]);

        return UserResource::make($user);
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
