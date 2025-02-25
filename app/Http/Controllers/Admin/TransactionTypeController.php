<?php

namespace App\Http\Controllers\Admin;

use Exception;
use App\Http\Controllers\Controller;
use App\Http\Resources\Admin\TransactionTypeResource;
use App\Contracts\Services\TransactionTypeServiceInterface;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Routing\Controllers\{Middleware, HasMiddleware};
use App\Http\Requests\Admin\TransactionType\{TransactionTypeStoreRequest, TransactionTypeUpdateRequest};

class TransactionTypeController extends Controller implements HasMiddleware
{
    /**
     * Get the middleware that should be assigned to the controller.
     *
     * @return array<int, mixed>
     */
    public static function middleware(): array
    {
        return [
            new Middleware('scopes:view:transaction-types'),
            new Middleware('scopes:create:transaction-types', only: ['store']),
            new Middleware('scopes:update:transaction-types', only: ['update']),
            new Middleware('scopes:delete:transaction-types', only: ['destroy']),
        ];
    }

    /**
     * The torrentProvider service.
     *
     * @var \App\Contracts\Services\TransactionTypeServiceInterface
     */
    private TransactionTypeServiceInterface $transactionTypeService;

    /**
     * Create a new controller instance.
     *
     * @param \App\Contracts\Services\TransactionTypeServiceInterface $transactionTypeService
     * @return void
     */
    public function __construct(TransactionTypeServiceInterface $transactionTypeService)
    {
        $this->transactionTypeService = $transactionTypeService;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function index(): AnonymousResourceCollection
    {
        return TransactionTypeResource::collection(
            $this->transactionTypeService->all(),
        );
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \App\Http\Requests\Admin\TransactionType\TransactionTypeStoreRequest $request
     * @return \App\Http\Resources\Admin\TransactionTypeResource
     */
    public function store(TransactionTypeStoreRequest $request): TransactionTypeResource
    {
        try {
            /** @var array<string, mixed> $data */
            $data = $request->validated();

            $torrentProvider = $this->transactionTypeService->create($data);

            return TransactionTypeResource::make($torrentProvider);
        } catch (Exception $e) {
            $this->handleDefaultExceptionLog('Failed to create a new transaction type.', $e);

            throw $e;
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \App\Http\Requests\Admin\TransactionType\TransactionTypeUpdateRequest $request
     * @param mixed $id
     * @return \App\Http\Resources\Admin\TransactionTypeResource
     */
    public function update(TransactionTypeUpdateRequest $request, mixed $id): TransactionTypeResource
    {
        try {
            /** @var array<string, mixed> $data */
            $data = $request->validated();

            $torrentProvider = $this->transactionTypeService->update($data, $id);

            return TransactionTypeResource::make($torrentProvider);
        } catch (Exception $e) {
            $this->handleDefaultExceptionLog('Failed to update a transaction type.', $e);

            throw $e;
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param mixed $id
     * @return void
     */
    public function destroy(mixed $id): void
    {
        $this->transactionTypeService->delete($id);
    }
}
