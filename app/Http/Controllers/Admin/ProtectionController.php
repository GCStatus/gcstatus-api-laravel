<?php

namespace App\Http\Controllers\Admin;

use Exception;
use App\Http\Controllers\Controller;
use App\Http\Resources\Admin\ProtectionResource;
use App\Contracts\Services\ProtectionServiceInterface;
use Illuminate\Routing\Controllers\{Middleware, HasMiddleware};
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use App\Http\Requests\Admin\Protection\{ProtectionStoreRequest, ProtectionUpdateRequest};

class ProtectionController extends Controller implements HasMiddleware
{
    /**
     * Get the middleware that should be assigned to the controller.
     *
     * @return array<int, mixed>
     */
    public static function middleware(): array
    {
        return [
            new Middleware('scopes:view:protections'),
            new Middleware('scopes:create:protections', only: ['store']),
            new Middleware('scopes:update:protections', only: ['update']),
            new Middleware('scopes:delete:protections', only: ['destroy']),
        ];
    }

    /**
     * The protection service.
     *
     * @var \App\Contracts\Services\ProtectionServiceInterface
     */
    private ProtectionServiceInterface $protectionService;

    /**
     * Create a new controller instance.
     *
     * @param \App\Contracts\Services\ProtectionServiceInterface $protectionService
     * @return void
     */
    public function __construct(ProtectionServiceInterface $protectionService)
    {
        $this->protectionService = $protectionService;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function index(): AnonymousResourceCollection
    {
        return ProtectionResource::collection(
            $this->protectionService->all(),
        );
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \App\Http\Requests\Admin\Protection\ProtectionStoreRequest $request
     * @return \App\Http\Resources\Admin\ProtectionResource
     */
    public function store(ProtectionStoreRequest $request): ProtectionResource
    {
        try {
            /** @var array<string, mixed> $data */
            $data = $request->validated();

            $protection = $this->protectionService->create($data);

            return ProtectionResource::make($protection);
        } catch (Exception $e) {
            $this->handleDefaultExceptionLog('Failed to create a new protection.', $e);

            throw $e;
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \App\Http\Requests\Admin\Protection\ProtectionUpdateRequest $request
     * @param mixed $id
     * @return \App\Http\Resources\Admin\ProtectionResource
     */
    public function update(ProtectionUpdateRequest $request, mixed $id): ProtectionResource
    {
        try {
            /** @var array<string, mixed> $data */
            $data = $request->validated();

            $protection = $this->protectionService->update($data, $id);

            return ProtectionResource::make($protection);
        } catch (Exception $e) {
            $this->handleDefaultExceptionLog('Failed to update a protection.', $e);

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
        $this->protectionService->delete($id);
    }
}
