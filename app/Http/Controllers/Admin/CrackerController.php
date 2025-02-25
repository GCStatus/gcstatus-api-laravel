<?php

namespace App\Http\Controllers\Admin;

use Exception;
use App\Http\Controllers\Controller;
use App\Http\Resources\Admin\CrackerResource;
use App\Contracts\Services\CrackerServiceInterface;
use Illuminate\Routing\Controllers\{Middleware, HasMiddleware};
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use App\Http\Requests\Admin\Cracker\{CrackerStoreRequest, CrackerUpdateRequest};

class CrackerController extends Controller implements HasMiddleware
{
    /**
     * Get the middleware that should be assigned to the controller.
     *
     * @return array<int, mixed>
     */
    public static function middleware(): array
    {
        return [
            new Middleware('scopes:view:crackers'),
            new Middleware('scopes:create:crackers', only: ['store']),
            new Middleware('scopes:update:crackers', only: ['update']),
            new Middleware('scopes:delete:crackers', only: ['destroy']),
        ];
    }

    /**
     * The cracker service.
     *
     * @var \App\Contracts\Services\CrackerServiceInterface
     */
    private CrackerServiceInterface $crackerService;

    /**
     * Create a new controller instance.
     *
     * @param \App\Contracts\Services\CrackerServiceInterface $crackerService
     * @return void
     */
    public function __construct(CrackerServiceInterface $crackerService)
    {
        $this->crackerService = $crackerService;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function index(): AnonymousResourceCollection
    {
        return CrackerResource::collection(
            $this->crackerService->all(),
        );
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \App\Http\Requests\Admin\Cracker\CrackerStoreRequest $request
     * @return \App\Http\Resources\Admin\CrackerResource
     */
    public function store(CrackerStoreRequest $request): CrackerResource
    {
        try {
            /** @var array<string, mixed> $data */
            $data = $request->validated();

            $cracker = $this->crackerService->create($data);

            return CrackerResource::make($cracker);
        } catch (Exception $e) {
            $this->handleDefaultExceptionLog('Failed to create a new cracker.', $e);

            throw $e;
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \App\Http\Requests\Admin\Cracker\CrackerUpdateRequest $request
     * @param mixed $id
     * @return \App\Http\Resources\Admin\CrackerResource
     */
    public function update(CrackerUpdateRequest $request, mixed $id): CrackerResource
    {
        try {
            /** @var array<string, mixed> $data */
            $data = $request->validated();

            $cracker = $this->crackerService->update($data, $id);

            return CrackerResource::make($cracker);
        } catch (Exception $e) {
            $this->handleDefaultExceptionLog('Failed to update a cracker.', $e);

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
        $this->crackerService->delete($id);
    }
}
