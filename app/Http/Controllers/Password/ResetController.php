<?php

namespace App\Http\Controllers\Password;

use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use App\Contracts\Responses\ApiResponseInterface;
use App\Contracts\Services\ResetPasswordServiceInterface;
use App\Http\Requests\ResetPassword\ResetPasswordRequest;

class ResetController extends Controller
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
     * @param \App\Http\Requests\ResetPassword\ResetPasswordRequest $request
     * @param \App\Contracts\Responses\ApiResponseInterface $response
     * @return \Illuminate\Http\JsonResponse
     */
    public function __invoke(
        ResetPasswordRequest $request,
        ApiResponseInterface $response,
    ): JsonResponse {
        /** @var array<string, string> $data */
        $data = $request->validated();

        $this->resetPasswordService->resetPassword($data);

        return response()->json(
            $response->setMessage('Your password was successfully changed!')->toMessage(),
        );
    }
}
