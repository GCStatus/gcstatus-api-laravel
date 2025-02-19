<?php

namespace App\Http\Controllers\Admin;

use Exception;
use App\Http\Controllers\Controller;
use App\Http\Resources\Admin\PlatformResource;
use App\Contracts\Services\PlatformServiceInterface;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Routing\Controllers\{Middleware, HasMiddleware};
use App\Http\Requests\Admin\Platform\{PlatformStoreRequest, PlatformUpdateRequest};

class PlatformController extends Controller implements HasMiddleware
{
    /**
     * Get the middleware that should be assigned to the controller.
     *
     * @return array<int, mixed>
     */
    public static function middleware(): array
    {
        return [
            new Middleware('scopes:view:platforms'),
            new Middleware('scopes:create:platforms', only: ['store']),
            new Middleware('scopes:update:platforms', only: ['update']),
            new Middleware('scopes:delete:platforms', only: ['destroy']),
        ];
    }

    /**
     * The platform service.
     *
     * @var \App\Contracts\Services\PlatformServiceInterface
     */
    private PlatformServiceInterface $platformService;

    /**
     * Create a new controller instance.
     *
     * @param \App\Contracts\Services\PlatformServiceInterface $platformService
     * @return void
     */
    public function __construct(PlatformServiceInterface $platformService)
    {
        $this->platformService = $platformService;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function index(): AnonymousResourceCollection
    {
        return PlatformResource::collection(
            $this->platformService->all(),
        );
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \App\Http\Requests\Admin\Platform\PlatformStoreRequest $request
     * @return \App\Http\Resources\Admin\PlatformResource
     */
    public function store(PlatformStoreRequest $request): PlatformResource
    {
        try {
            /** @var array<string, mixed> $data */
            $data = $request->validated();

            $platform = $this->platformService->create($data);

            return PlatformResource::make($platform);
        } catch (Exception $e) {
            $this->handleDefaultExceptionLog('Failed to create a new platform.', $e);

            throw $e;
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \App\Http\Requests\Admin\Platform\PlatformUpdateRequest $request
     * @param mixed $id
     * @return \App\Http\Resources\Admin\PlatformResource
     */
    public function update(PlatformUpdateRequest $request, mixed $id): PlatformResource
    {
        try {
            /** @var array<string, mixed> $data */
            $data = $request->validated();

            $platform = $this->platformService->update($data, $id);

            return PlatformResource::make($platform);
        } catch (Exception $e) {
            $this->handleDefaultExceptionLog('Failed to update a platform.', $e);

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
        $this->platformService->delete($id);
    }
}
