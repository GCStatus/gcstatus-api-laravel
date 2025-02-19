<?php

namespace App\Http\Controllers\Admin;

use Exception;
use App\Http\Controllers\Controller;
use App\Http\Resources\Admin\PublisherResource;
use App\Contracts\Services\PublisherServiceInterface;
use Illuminate\Routing\Controllers\{Middleware, HasMiddleware};
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use App\Http\Requests\Admin\Publisher\{PublisherStoreRequest, PublisherUpdateRequest};

class PublisherController extends Controller implements HasMiddleware
{
    /**
     * Get the middleware that should be assigned to the controller.
     *
     * @return array<int, mixed>
     */
    public static function middleware(): array
    {
        return [
            new Middleware('scopes:view:publishers'),
            new Middleware('scopes:create:publishers', only: ['store']),
            new Middleware('scopes:update:publishers', only: ['update']),
            new Middleware('scopes:delete:publishers', only: ['destroy']),
        ];
    }

    /**
     * The publisher service.
     *
     * @var \App\Contracts\Services\PublisherServiceInterface
     */
    private PublisherServiceInterface $publisherService;

    /**
     * Create a new controller instance.
     *
     * @param \App\Contracts\Services\PublisherServiceInterface $publisherService
     * @return void
     */
    public function __construct(PublisherServiceInterface $publisherService)
    {
        $this->publisherService = $publisherService;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function index(): AnonymousResourceCollection
    {
        return PublisherResource::collection(
            $this->publisherService->all(),
        );
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \App\Http\Requests\Admin\Publisher\PublisherStoreRequest $request
     * @return \App\Http\Resources\Admin\PublisherResource
     */
    public function store(PublisherStoreRequest $request): PublisherResource
    {
        try {
            /** @var array<string, mixed> $data */
            $data = $request->validated();

            $publisher = $this->publisherService->create($data);

            return PublisherResource::make($publisher);
        } catch (Exception $e) {
            $this->handleDefaultExceptionLog('Failed to create a new publisher.', $e);

            throw $e;
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \App\Http\Requests\Admin\Publisher\PublisherUpdateRequest $request
     * @param mixed $id
     * @return \App\Http\Resources\Admin\PublisherResource
     */
    public function update(PublisherUpdateRequest $request, mixed $id): PublisherResource
    {
        try {
            /** @var array<string, mixed> $data */
            $data = $request->validated();

            $publisher = $this->publisherService->update($data, $id);

            return PublisherResource::make($publisher);
        } catch (Exception $e) {
            $this->handleDefaultExceptionLog('Failed to update a publisher.', $e);

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
        $this->publisherService->delete($id);
    }
}
