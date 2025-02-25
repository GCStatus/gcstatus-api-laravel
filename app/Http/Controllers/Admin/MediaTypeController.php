<?php

namespace App\Http\Controllers\Admin;

use Exception;
use App\Http\Controllers\Controller;
use App\Http\Resources\Admin\MediaTypeResource;
use App\Contracts\Services\MediaTypeServiceInterface;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Routing\Controllers\{Middleware, HasMiddleware};
use App\Http\Requests\Admin\MediaType\{MediaTypeStoreRequest, MediaTypeUpdateRequest};

class MediaTypeController extends Controller implements HasMiddleware
{
    /**
     * Get the middleware that should be assigned to the controller.
     *
     * @return array<int, mixed>
     */
    public static function middleware(): array
    {
        return [
            new Middleware('scopes:view:media-types'),
            new Middleware('scopes:create:media-types', only: ['store']),
            new Middleware('scopes:update:media-types', only: ['update']),
            new Middleware('scopes:delete:media-types', only: ['destroy']),
        ];
    }

    /**
     * The mediaType service.
     *
     * @var \App\Contracts\Services\MediaTypeServiceInterface
     */
    private MediaTypeServiceInterface $mediaTypeService;

    /**
     * Create a new controller instance.
     *
     * @param \App\Contracts\Services\MediaTypeServiceInterface $mediaTypeService
     * @return void
     */
    public function __construct(MediaTypeServiceInterface $mediaTypeService)
    {
        $this->mediaTypeService = $mediaTypeService;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function index(): AnonymousResourceCollection
    {
        return MediaTypeResource::collection(
            $this->mediaTypeService->all(),
        );
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \App\Http\Requests\Admin\MediaType\MediaTypeStoreRequest $request
     * @return \App\Http\Resources\Admin\MediaTypeResource
     */
    public function store(MediaTypeStoreRequest $request): MediaTypeResource
    {
        try {
            /** @var array<string, mixed> $data */
            $data = $request->validated();

            $mediaType = $this->mediaTypeService->create($data);

            return MediaTypeResource::make($mediaType);
        } catch (Exception $e) {
            $this->handleDefaultExceptionLog('Failed to create a new media type.', $e);

            throw $e;
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \App\Http\Requests\Admin\MediaType\MediaTypeUpdateRequest $request
     * @param mixed $id
     * @return \App\Http\Resources\Admin\MediaTypeResource
     */
    public function update(MediaTypeUpdateRequest $request, mixed $id): MediaTypeResource
    {
        try {
            /** @var array<string, mixed> $data */
            $data = $request->validated();

            $mediaType = $this->mediaTypeService->update($data, $id);

            return MediaTypeResource::make($mediaType);
        } catch (Exception $e) {
            $this->handleDefaultExceptionLog('Failed to update a media type.', $e);

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
        $this->mediaTypeService->delete($id);
    }
}
