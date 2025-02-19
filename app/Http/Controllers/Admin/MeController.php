<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\Admin\MeResource;
use App\Contracts\Services\AuthServiceInterface;

class MeController extends Controller
{
    /**
     * The auth service.
     *
     * @var \App\Contracts\Services\AuthServiceInterface
     */
    private AuthServiceInterface $authService;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->authService = app(AuthServiceInterface::class);
    }

    /**
     * Handle the incoming request.
     *
     * @return \App\Http\Resources\Admin\MeResource
     */
    public function __invoke(): MeResource
    {
        $user = $this->authService->getAuthUser()->load(
            'roles.permissions',
            'permissions',
        );

        return MeResource::make($user);
    }
}
