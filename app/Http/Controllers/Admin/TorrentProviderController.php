<?php

namespace App\Http\Controllers\Admin;

use Exception;
use App\Http\Controllers\Controller;
use App\Http\Resources\Admin\TorrentProviderResource;
use App\Contracts\Services\TorrentProviderServiceInterface;
use Illuminate\Routing\Controllers\{Middleware, HasMiddleware};
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use App\Http\Requests\Admin\TorrentProvider\{TorrentProviderStoreRequest, TorrentProviderUpdateRequest};

class TorrentProviderController extends Controller implements HasMiddleware
{
    /**
     * Get the middleware that should be assigned to the controller.
     *
     * @return array<int, mixed>
     */
    public static function middleware(): array
    {
        return [
            new Middleware('scopes:view:torrent-providers'),
            new Middleware('scopes:create:torrent-providers', only: ['store']),
            new Middleware('scopes:update:torrent-providers', only: ['update']),
            new Middleware('scopes:delete:torrent-providers', only: ['destroy']),
        ];
    }

    /**
     * The torrentProvider service.
     *
     * @var \App\Contracts\Services\TorrentProviderServiceInterface
     */
    private TorrentProviderServiceInterface $torrentProviderService;

    /**
     * Create a new controller instance.
     *
     * @param \App\Contracts\Services\TorrentProviderServiceInterface $torrentProviderService
     * @return void
     */
    public function __construct(TorrentProviderServiceInterface $torrentProviderService)
    {
        $this->torrentProviderService = $torrentProviderService;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function index(): AnonymousResourceCollection
    {
        return TorrentProviderResource::collection(
            $this->torrentProviderService->all(),
        );
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \App\Http\Requests\Admin\TorrentProvider\TorrentProviderStoreRequest $request
     * @return \App\Http\Resources\Admin\TorrentProviderResource
     */
    public function store(TorrentProviderStoreRequest $request): TorrentProviderResource
    {
        try {
            /** @var array<string, mixed> $data */
            $data = $request->validated();

            $torrentProvider = $this->torrentProviderService->create($data);

            return TorrentProviderResource::make($torrentProvider);
        } catch (Exception $e) {
            $this->handleDefaultExceptionLog('Failed to create a new torrent provider.', $e);

            throw $e;
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \App\Http\Requests\Admin\TorrentProvider\TorrentProviderUpdateRequest $request
     * @param mixed $id
     * @return \App\Http\Resources\Admin\TorrentProviderResource
     */
    public function update(TorrentProviderUpdateRequest $request, mixed $id): TorrentProviderResource
    {
        try {
            /** @var array<string, mixed> $data */
            $data = $request->validated();

            $torrentProvider = $this->torrentProviderService->update($data, $id);

            return TorrentProviderResource::make($torrentProvider);
        } catch (Exception $e) {
            $this->handleDefaultExceptionLog('Failed to update a torrent provider.', $e);

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
        $this->torrentProviderService->delete($id);
    }
}
