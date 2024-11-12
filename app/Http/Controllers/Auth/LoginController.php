<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Exceptions\Auth\InvalidUserException;
use App\Contracts\{
    Services\AuthServiceInterface,
    Responses\ApiResponseInterface,
};

class LoginController extends Controller
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
     * Handle the incoming request.
     *
     * @param \App\Http\Requests\Auth\LoginRequest $request
     * @param \App\Contracts\Responses\ApiResponseInterface $response
     * @return \Illuminate\Http\JsonResponse
     */
    public function __invoke(
        LoginRequest $request,
        ApiResponseInterface $response,
    ): JsonResponse {
        /** @var array<string, string> $data */
        $data = $request->validated();

        if (!$token = $this->authService->auth($data)) {
            throw new InvalidUserException();
        }

        $this->authService->setAuthenticationCookies($token);

        return response()->json(
            $response->setMessage('User successfully authenticated.')->toMessage(),
        );
    }
}
