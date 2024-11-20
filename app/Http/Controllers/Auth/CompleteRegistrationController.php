<?php

namespace App\Http\Controllers\Auth;

use App\Http\Resources\UserResource;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\CompleteRegistrationRequest;
use App\Contracts\Services\{AuthServiceInterface, UserServiceInterface};

class CompleteRegistrationController extends Controller
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
     * Handle the incoming request.
     *
     * @param \App\Http\Requests\Auth\CompleteRegistrationRequest $request
     * @return \App\Http\Resources\UserResource
     */
    public function __invoke(CompleteRegistrationRequest $request): UserResource
    {
        /** @var array<string, string> $data */
        $data = $request->validated();

        $userId = $this->authService->getAuthId();

        $user = $this->userService->update($data, $userId);

        return UserResource::make($user);
    }
}
