<?php

namespace App\Http\Controllers\EmailVerify;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use App\Contracts\Services\{
    AuthServiceInterface,
    EmailVerifyServiceInterface,
};

class VerifyController extends Controller
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
     * @param \Illuminate\Foundation\Auth\EmailVerificationRequest $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function __invoke(EmailVerificationRequest $request): RedirectResponse
    {
        $request->validated();

        /** @var \App\Models\User $user */
        $user = $this->authService->getAuthUser();

        $this->emailVerifyService->verify($user);

        /** @var string $baseUrl */
        $baseUrl = config('gcstatus.front_base_url');

        return redirect()->away($baseUrl);
    }
}
