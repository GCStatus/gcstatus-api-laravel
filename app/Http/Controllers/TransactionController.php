<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use App\Http\Resources\TransactionResource;
use App\Contracts\Responses\ApiResponseInterface;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use App\Contracts\Services\{
    AuthServiceInterface,
    TransactionServiceInterface,
};

class TransactionController extends Controller
{
    /**
     * The auth service.
     *
     * @var \App\Contracts\Services\AuthServiceInterface
     */
    private $authService;

    /**
     * The transaction service.
     *
     * @var \App\Contracts\Services\TransactionServiceInterface
     */
    private TransactionServiceInterface $transactionService;

    /**
     * Create a new class instance.
     *
     * @param \App\Contracts\Services\AuthServiceInterface $authService
     * @param \App\Contracts\Services\TransactionServiceInterface $transactionService
     * @return void
     */
    public function __construct(
        AuthServiceInterface $authService,
        TransactionServiceInterface $transactionService,
    ) {
        $this->authService = $authService;
        $this->transactionService = $transactionService;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function index(): AnonymousResourceCollection
    {
        /** @var \App\Models\User $user */
        $user = $this->authService->getAuthUser();

        return TransactionResource::collection(
            $this->transactionService->allForAuth($user),
        );
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param mixed $id
     * @param \App\Contracts\Responses\ApiResponseInterface $response
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(mixed $id, ApiResponseInterface $response): JsonResponse
    {
        /** @var \App\Models\User $user */
        $user = $this->authService->getAuthUser();

        $this->transactionService->deleteForUser($user, $id);

        return response()->json(
            $response->setMessage('The transaction was successfully removed!')->toMessage(),
        );
    }
}
