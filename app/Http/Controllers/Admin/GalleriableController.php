<?php

namespace App\Http\Controllers\Admin;

use Exception;
use App\Http\Controllers\Controller;
use App\Http\Resources\Admin\GalleriableResource;
use App\Contracts\Services\GalleriableServiceInterface;
use Illuminate\Routing\Controllers\{Middleware, HasMiddleware};
use App\Http\Requests\Admin\Galleriable\GalleriableStoreRequest;

class GalleriableController extends Controller implements HasMiddleware
{
    /**
     * Get the middleware that should be assigned to the controller.
     *
     * @return array<int, mixed>
     */
    public static function middleware(): array
    {
        return [
            new Middleware('scopes:create:galleriables', only: ['store']),
            new Middleware('scopes:delete:galleriables', only: ['destroy']),
        ];
    }

    /**
     * The galleriable service.
     *
     * @var \App\Contracts\Services\GalleriableServiceInterface
     */
    private GalleriableServiceInterface $galleriableService;

    /**
     * Create a new controller instance.
     *
     * @param \App\Contracts\Services\GalleriableServiceInterface $galleriableService
     * @return void
     */
    public function __construct(GalleriableServiceInterface $galleriableService)
    {
        $this->galleriableService = $galleriableService;
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \App\Http\Requests\Admin\Galleriable\GalleriableStoreRequest $request
     * @return \App\Http\Resources\Admin\GalleriableResource
     */
    public function store(GalleriableStoreRequest $request): GalleriableResource
    {
        try {
            /** @var array<string, mixed> $data */
            $data = $request->validated();

            $galleriable = $this->galleriableService->create($data);

            return GalleriableResource::make($galleriable);
        } catch (Exception $e) {
            $this->handleDefaultExceptionLog('Failed to create a new galleriable.', $e);

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
        $this->galleriableService->delete($id);
    }
}
