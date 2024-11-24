<?php

namespace App\Http\Controllers\EmailVerify;

use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use App\Contracts\{
    Services\AuthServiceInterface,
    Responses\ApiResponseInterface,
    Services\EmailVerifyServiceInterface,
};

class NotifyController extends Controller
{
    /**
     * The auth service.
     *
     * @var \App\Contracts\Services\AuthServiceInterface
     */
    private AuthServiceInterface $authService;

    /**
     * The email verify service.
     *
     * @var \App\Contracts\Services\EmailVerifyServiceInterface
     */
    private EmailVerifyServiceInterface $emailVerifyService;

    /**
     * Create a new class instance.
     *
     * @param \App\Contracts\Services\AuthServiceInterface $authService
     * @param \App\Contracts\Services\EmailVerifyServiceInterface $emailVerifyService
     * @return void
     */
    public function __construct(
        AuthServiceInterface $authService,
        EmailVerifyServiceInterface $emailVerifyService,
    ) {
        $this->authService = $authService;
        $this->emailVerifyService = $emailVerifyService;
    }

    /**
     * Handle the incoming request.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function __invoke(ApiResponseInterface $response): JsonResponse
    {
        /** @var \App\Models\User $user */
        $user = $this->authService->getAuthUser();

        $this->emailVerifyService->notify($user);

        return response()->json(
            $response->setMessage('The verification link will be sent to your email!')->toMessage(),
        );
    }
}
