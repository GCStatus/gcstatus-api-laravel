<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use App\Contracts\Services\AuthServiceInterface;
use App\Contracts\Responses\ApiResponseInterface;

class LogoutController extends Controller
{
    /**
     * The auth service.
     *
     * @var \App\Contracts\Services\AuthServiceInterface
     */
    private AuthServiceInterface $authService;

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
     * Handle the incoming request.
     *
     * @param \App\Contracts\Responses\ApiResponseInterface $response
     * @return \Illuminate\Http\JsonResponse
     */
    public function __invoke(ApiResponseInterface $response): JsonResponse
    {
        $this->authService->clearAuthenticationCookies();

        return response()->json(
            $response->setMessage('You have successfully logged out from platform!')->toMessage(),
        );
    }
}
