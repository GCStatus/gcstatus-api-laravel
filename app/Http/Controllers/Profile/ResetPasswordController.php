<?php

namespace App\Http\Controllers\Profile;

use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use App\Contracts\Responses\ApiResponseInterface;
use App\Http\Requests\Profile\ResetPasswordRequest;
use App\Contracts\Services\{
    AuthServiceInterface,
    CacheServiceInterface,
    UserServiceInterface,
};

class ResetPasswordController extends Controller
{
    /**
     * The user service.
     *
     * @var \App\Contracts\Services\UserServiceInterface
     */
    private UserServiceInterface $userService;

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
     * Create a new class instance.
     *
     * @param \App\Contracts\Services\UserServiceInterface $userService
     * @param \App\Contracts\Services\AuthServiceInterface $authService
     * @param \App\Contracts\Services\CacheServiceInterface $cacheService
     * @return void
     */
    public function __construct(
        UserServiceInterface $userService,
        AuthServiceInterface $authService,
        CacheServiceInterface $cacheService,
    ) {
        $this->userService = $userService;
        $this->authService = $authService;
        $this->cacheService = $cacheService;
    }

    /**
     * Handle the incoming request.
     *
     * @param \App\Http\Requests\Profile\ResetPasswordRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function __invoke(ResetPasswordRequest $request, ApiResponseInterface $response): JsonResponse
    {
        /** @var array<string, string> $data */
        $data = $request->validated();

        /** @var \App\Models\User $user */
        $user = $this->authService->getAuthUser();

        $this->userService->updatePassword($user, $data['old_password'], $data['password']);

        $key = "auth.user.{$user->id}";

        $this->cacheService->forget($key);

        return response()->json(
            $response->setMessage('Your password was successfully updated!')->toMessage(),
        );
    }
}
