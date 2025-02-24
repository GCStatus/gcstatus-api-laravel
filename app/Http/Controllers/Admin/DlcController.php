<?php

namespace App\Http\Controllers\Admin;

use Exception;
use App\Http\Controllers\Controller;
use App\Http\Resources\Admin\DlcResource;
use App\Contracts\Services\DlcServiceInterface;
use Illuminate\Routing\Controllers\{Middleware, HasMiddleware};
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use App\Http\Requests\Admin\Dlc\{DlcStoreRequest, DlcUpdateRequest};

class DlcController extends Controller implements HasMiddleware
{
    /**
     * Get the middleware that should be assigned to the controller.
     *
     * @return array<int, mixed>
     */
    public static function middleware(): array
    {
        return [
            new Middleware('scopes:view:dlcs'),
            new Middleware('scopes:create:dlcs', only: ['store']),
            new Middleware('scopes:update:dlcs', only: ['update']),
            new Middleware('scopes:delete:dlcs', only: ['destroy']),
        ];
    }

    /**
     * The dlc service.
     *
     * @var \App\Contracts\Services\DlcServiceInterface
     */
    private DlcServiceInterface $dlcService;

    /**
     * Create a new controller instance.
     *
     * @param \App\Contracts\Services\DlcServiceInterface $dlcService
     * @return void
     */
    public function __construct(DlcServiceInterface $dlcService)
    {
        $this->dlcService = $dlcService;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function index(): AnonymousResourceCollection
    {
        return DlcResource::collection(
            $this->dlcService->allForAdmin(),
        );
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \App\Http\Requests\Admin\Dlc\DlcStoreRequest $request
     * @return \App\Http\Resources\Admin\DlcResource
     */
    public function store(DlcStoreRequest $request): DlcResource
    {
        try {
            /** @var array<string, mixed> $data */
            $data = $request->validated();

            $dlc = $this->dlcService->create($data);

            return DlcResource::make($dlc);
        } catch (Exception $e) {
            $this->handleDefaultExceptionLog('Failed to create a new dlc.', $e);

            throw $e;
        }
    }

    /**
     * Display the specified resource.
     *
     * @param mixed $id
     * @return \App\Http\Resources\Admin\DlcResource
     */
    public function show(mixed $id): DlcResource
    {
        return DlcResource::make(
            $this->dlcService->detailsForAdmin($id),
        );
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \App\Http\Requests\Admin\Dlc\DlcUpdateRequest $request
     * @param mixed $id
     * @return \App\Http\Resources\Admin\DlcResource
     */
    public function update(DlcUpdateRequest $request, mixed $id): DlcResource
    {
        try {
            /** @var array<string, mixed> $data */
            $data = $request->validated();

            $dlc = $this->dlcService->update($data, $id);

            return DlcResource::make($dlc);
        } catch (Exception $e) {
            $this->handleDefaultExceptionLog('Failed to update a dlc.', $e);

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
        $this->dlcService->delete($id);
    }
}
