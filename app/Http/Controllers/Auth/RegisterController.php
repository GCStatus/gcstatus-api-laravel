<?php

namespace App\Http\Controllers\Auth;

use App\Http\Resources\UserResource;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\RegisterRequest;
use App\Contracts\Services\{
    JWTServiceInterface,
    AuthServiceInterface,
    UserServiceInterface,
};

class RegisterController extends Controller
{
    /**
     * The user service.
     *
     * @var \App\Contracts\Services\UserServiceInterface
     */
    private $userService;

    /**
     * The auth service.
     *
     * @var \App\Contracts\Services\AuthServiceInterface
     */
    private $authService;

    /**
     * The jwt service.
     *
     * @var \App\Contracts\Services\JWTServiceInterface
     */
    private $jwtService;

    /**
     * Create a new class instance.
     *
     * @param \App\Contracts\Services\JWTServiceInterface $jwtService
     * @param \App\Contracts\Services\UserServiceInterface $userService
     * @param \App\Contracts\Services\AuthServiceInterface $authService
     * @return void
     */
    public function __construct(
        JWTServiceInterface $jwtService,
        UserServiceInterface $userService,
        AuthServiceInterface $authService,
    ) {
        $this->jwtService = $jwtService;
        $this->userService = $userService;
        $this->authService = $authService;
    }

    /**
     * Handle the incoming request.
     *
     * @param \App\Http\Requests\Auth\RegisterRequest $request
     * @return \App\Http\Resources\UserResource
     */
    public function __invoke(RegisterRequest $request): UserResource
    {
        /** @var array<string, mixed> $data */
        $data = $request->validated();

        /** @var \App\Models\User $user */
        $user = $this->userService->create($data);

        $token = $this->jwtService->tokenize($user);

        $this->authService->setAuthenticationCookies($token);

        return UserResource::make($user);
    }
}
