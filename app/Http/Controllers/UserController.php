<?php

namespace App\Http\Controllers;

use App\Http\Resources\UserResource;
use App\Contracts\Services\AuthServiceInterface;

class UserController extends Controller
{
    /**
     * The auth service.
     *
     * @var \App\Contracts\Services\AuthServiceInterface
     */
    private $authService;

    /**
     * Create a new class instance.
     *
     * @param \App\Contracts\Services\AuthServiceInterface $authService
     * @return void
     */
    public function __construct(AuthServiceInterface $authService)
    {
        $this->authService = $authService;
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
}
