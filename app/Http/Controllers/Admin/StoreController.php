<?php

namespace App\Http\Controllers\Admin;

use Exception;
use App\Http\Controllers\Controller;
use App\Http\Resources\Admin\StoreResource;
use App\Contracts\Services\StoreServiceInterface;
use Illuminate\Routing\Controllers\{Middleware, HasMiddleware};
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use App\Http\Requests\Admin\Store\{StoreStoreRequest, StoreUpdateRequest};

class StoreController extends Controller implements HasMiddleware
{
    /**
     * Get the middleware that should be assigned to the controller.
     *
     * @return array<int, mixed>
     */
    public static function middleware(): array
    {
        return [
            new Middleware('scopes:view:stores'),
            new Middleware('scopes:create:stores', only: ['store']),
            new Middleware('scopes:update:stores', only: ['update']),
            new Middleware('scopes:delete:stores', only: ['destroy']),
        ];
    }

    /**
     * The store service.
     *
     * @var \App\Contracts\Services\StoreServiceInterface
     */
    private StoreServiceInterface $storeService;

    /**
     * Create a new controller instance.
     *
     * @param \App\Contracts\Services\StoreServiceInterface $storeService
     * @return void
     */
    public function __construct(StoreServiceInterface $storeService)
    {
        $this->storeService = $storeService;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function index(): AnonymousResourceCollection
    {
        return StoreResource::collection(
            $this->storeService->all(),
        );
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \App\Http\Requests\Admin\Store\StoreStoreRequest $request
     * @return \App\Http\Resources\Admin\StoreResource
     */
    public function store(StoreStoreRequest $request): StoreResource
    {
        try {
            /** @var array<string, mixed> $data */
            $data = $request->validated();

            $store = $this->storeService->create($data);

            return StoreResource::make($store);
        } catch (Exception $e) {
            $this->handleDefaultExceptionLog('Failed to create a new store.', $e);

            throw $e;
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \App\Http\Requests\Admin\Store\StoreUpdateRequest $request
     * @param mixed $id
     * @return \App\Http\Resources\Admin\StoreResource
     */
    public function update(StoreUpdateRequest $request, mixed $id): StoreResource
    {
        try {
            /** @var array<string, mixed> $data */
            $data = $request->validated();

            $store = $this->storeService->update($data, $id);

            return StoreResource::make($store);
        } catch (Exception $e) {
            $this->handleDefaultExceptionLog('Failed to update a store.', $e);

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
        $this->storeService->delete($id);
    }
}
