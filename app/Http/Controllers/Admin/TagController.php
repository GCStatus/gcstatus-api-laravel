<?php

namespace App\Http\Controllers\Admin;

use Exception;
use App\Http\Controllers\Controller;
use App\Http\Resources\Admin\TagResource;
use App\Contracts\Services\TagServiceInterface;
use Illuminate\Routing\Controllers\{Middleware, HasMiddleware};
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use App\Http\Requests\Admin\Tag\{TagStoreRequest, TagUpdateRequest};

class TagController extends Controller implements HasMiddleware
{
    /**
     * Get the middleware that should be assigned to the controller.
     *
     * @return array<int, mixed>
     */
    public static function middleware(): array
    {
        return [
            new Middleware('scopes:view:tags'),
            new Middleware('scopes:create:tags', only: ['store']),
            new Middleware('scopes:update:tags', only: ['update']),
            new Middleware('scopes:delete:tags', only: ['destroy']),
        ];
    }

    /**
     * The tag service.
     *
     * @var \App\Contracts\Services\TagServiceInterface
     */
    private TagServiceInterface $tagService;

    /**
     * Create a new controller instance.
     *
     * @param \App\Contracts\Services\TagServiceInterface $tagService
     * @return void
     */
    public function __construct(TagServiceInterface $tagService)
    {
        $this->tagService = $tagService;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function index(): AnonymousResourceCollection
    {
        return TagResource::collection(
            $this->tagService->all(),
        );
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \App\Http\Requests\Admin\Tag\TagStoreRequest $request
     * @return \App\Http\Resources\Admin\TagResource
     */
    public function store(TagStoreRequest $request): TagResource
    {
        try {
            /** @var array<string, mixed> $data */
            $data = $request->validated();

            $tag = $this->tagService->create($data);

            return TagResource::make($tag);
        } catch (Exception $e) {
            $this->handleDefaultExceptionLog('Failed to create a new tag.', $e);

            throw $e;
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \App\Http\Requests\Admin\Tag\TagUpdateRequest $request
     * @param mixed $id
     * @return \App\Http\Resources\Admin\TagResource
     */
    public function update(TagUpdateRequest $request, mixed $id): TagResource
    {
        try {
            /** @var array<string, mixed> $data */
            $data = $request->validated();

            $tag = $this->tagService->update($data, $id);

            return TagResource::make($tag);
        } catch (Exception $e) {
            $this->handleDefaultExceptionLog('Failed to update a tag.', $e);

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
        $this->tagService->delete($id);
    }
}
