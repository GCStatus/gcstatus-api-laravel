<?php

namespace App\Http\Controllers\Admin;

use Exception;
use App\Http\Controllers\Controller;
use App\Http\Resources\Admin\CriticResource;
use App\Contracts\Services\CriticServiceInterface;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Routing\Controllers\{Middleware, HasMiddleware};
use App\Http\Requests\Admin\Critic\{CriticStoreRequest, CriticUpdateRequest};

class CriticController extends Controller implements HasMiddleware
{
    /**
     * Get the middleware that should be assigned to the controller.
     *
     * @return array<int, mixed>
     */
    public static function middleware(): array
    {
        return [
            new Middleware('scopes:view:critics'),
            new Middleware('scopes:create:critics', only: ['store']),
            new Middleware('scopes:update:critics', only: ['update']),
            new Middleware('scopes:delete:critics', only: ['destroy']),
        ];
    }

    /**
     * The critic service.
     *
     * @var \App\Contracts\Services\CriticServiceInterface
     */
    private CriticServiceInterface $criticService;

    /**
     * Create a new controller instance.
     *
     * @param \App\Contracts\Services\CriticServiceInterface $criticService
     * @return void
     */
    public function __construct(CriticServiceInterface $criticService)
    {
        $this->criticService = $criticService;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function index(): AnonymousResourceCollection
    {
        return CriticResource::collection(
            $this->criticService->all(),
        );
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \App\Http\Requests\Admin\Critic\CriticStoreRequest $request
     * @return \App\Http\Resources\Admin\CriticResource
     */
    public function store(CriticStoreRequest $request): CriticResource
    {
        try {
            /** @var array<string, mixed> $data */
            $data = $request->validated();

            $critic = $this->criticService->create($data);

            return CriticResource::make($critic);
        } catch (Exception $e) {
            $this->handleDefaultExceptionLog('Failed to create a new critic.', $e);

            throw $e;
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \App\Http\Requests\Admin\Critic\CriticUpdateRequest $request
     * @param mixed $id
     * @return \App\Http\Resources\Admin\CriticResource
     */
    public function update(CriticUpdateRequest $request, mixed $id): CriticResource
    {
        try {
            /** @var array<string, mixed> $data */
            $data = $request->validated();

            $critic = $this->criticService->update($data, $id);

            return CriticResource::make($critic);
        } catch (Exception $e) {
            $this->handleDefaultExceptionLog('Failed to update a critic.', $e);

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
        $this->criticService->delete($id);
    }
}
