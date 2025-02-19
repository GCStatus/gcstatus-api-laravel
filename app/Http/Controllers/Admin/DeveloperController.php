<?php

namespace App\Http\Controllers\Admin;

use Exception;
use App\Http\Controllers\Controller;
use App\Http\Resources\Admin\DeveloperResource;
use App\Contracts\Services\DeveloperServiceInterface;
use Illuminate\Routing\Controllers\{Middleware, HasMiddleware};
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use App\Http\Requests\Admin\Developer\{DeveloperStoreRequest, DeveloperUpdateRequest};

class DeveloperController extends Controller implements HasMiddleware
{
    /**
     * Get the middleware that should be assigned to the controller.
     *
     * @return array<int, mixed>
     */
    public static function middleware(): array
    {
        return [
            new Middleware('scopes:view:developers'),
            new Middleware('scopes:create:developers', only: ['store']),
            new Middleware('scopes:update:developers', only: ['update']),
            new Middleware('scopes:delete:developers', only: ['destroy']),
        ];
    }

    /**
     * The developer service.
     *
     * @var \App\Contracts\Services\DeveloperServiceInterface
     */
    private DeveloperServiceInterface $developerService;

    /**
     * Create a new controller instance.
     *
     * @param \App\Contracts\Services\DeveloperServiceInterface $developerService
     * @return void
     */
    public function __construct(DeveloperServiceInterface $developerService)
    {
        $this->developerService = $developerService;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function index(): AnonymousResourceCollection
    {
        return DeveloperResource::collection(
            $this->developerService->all(),
        );
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \App\Http\Requests\Admin\Developer\DeveloperStoreRequest $request
     * @return \App\Http\Resources\Admin\DeveloperResource
     */
    public function store(DeveloperStoreRequest $request): DeveloperResource
    {
        try {
            /** @var array<string, mixed> $data */
            $data = $request->validated();

            $developer = $this->developerService->create($data);

            return DeveloperResource::make($developer);
        } catch (Exception $e) {
            $this->handleDefaultExceptionLog('Failed to create a new developer.', $e);

            throw $e;
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \App\Http\Requests\Admin\Developer\DeveloperUpdateRequest $request
     * @param mixed $id
     * @return \App\Http\Resources\Admin\DeveloperResource
     */
    public function update(DeveloperUpdateRequest $request, mixed $id): DeveloperResource
    {
        try {
            /** @var array<string, mixed> $data */
            $data = $request->validated();

            $developer = $this->developerService->update($data, $id);

            return DeveloperResource::make($developer);
        } catch (Exception $e) {
            $this->handleDefaultExceptionLog('Failed to update a developer.', $e);

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
        $this->developerService->delete($id);
    }
}
