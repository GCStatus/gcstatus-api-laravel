<?php

namespace App\Http\Controllers\Password;

use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use App\Contracts\Responses\ApiResponseInterface;
use App\Contracts\Services\ResetPasswordServiceInterface;
use App\Http\Requests\ResetPassword\ForgotPasswordRequest;

class ForgotController extends Controller
{
    /**
     * The reset password service.
     *
     * @var \App\Contracts\Services\ResetPasswordServiceInterface
     */
    private ResetPasswordServiceInterface $resetPasswordService;

    /**
     * Create a new class instance.
     *
     * @param \App\Contracts\Services\ResetPasswordServiceInterface $resetPasswordService
     * @return void
     */
    public function __construct(
        ResetPasswordServiceInterface $resetPasswordService,
    ) {
        $this->resetPasswordService = $resetPasswordService;
    }

    /**
     * Handle the incoming request.
     *
     * @param \App\Http\Requests\ResetPassword\ForgotPasswordRequest $request
     * @param \App\Contracts\Responses\ApiResponseInterface $response
     * @return \Illuminate\Http\JsonResponse
     */
    public function __invoke(ForgotPasswordRequest $request, ApiResponseInterface $response): JsonResponse
    {
        /** @var array<string, string> $data */
        $data = $request->validated();

        $this->resetPasswordService->sendResetNotification($data);

        return response()->json(
            $response->setMessage('The password reset link was successfully sent to your email!')->toMessage(),
        );
    }
}
