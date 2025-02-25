<?php

namespace App\Http\Controllers\Admin;

use Exception;
use App\Http\Controllers\Controller;
use App\Http\Resources\Admin\RequirementTypeResource;
use App\Contracts\Services\RequirementTypeServiceInterface;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Routing\Controllers\{Middleware, HasMiddleware};
use App\Http\Requests\Admin\RequirementType\{RequirementTypeStoreRequest, RequirementTypeUpdateRequest};

class RequirementTypeController extends Controller implements HasMiddleware
{
    /**
     * Get the middleware that should be assigned to the controller.
     *
     * @return array<int, mixed>
     */
    public static function middleware(): array
    {
        return [
            new Middleware('scopes:view:requirement-types'),
            new Middleware('scopes:create:requirement-types', only: ['store']),
            new Middleware('scopes:update:requirement-types', only: ['update']),
            new Middleware('scopes:delete:requirement-types', only: ['destroy']),
        ];
    }

    /**
     * The requirementType service.
     *
     * @var \App\Contracts\Services\RequirementTypeServiceInterface
     */
    private RequirementTypeServiceInterface $requirementTypeService;

    /**
     * Create a new controller instance.
     *
     * @param \App\Contracts\Services\RequirementTypeServiceInterface $requirementTypeService
     * @return void
     */
    public function __construct(RequirementTypeServiceInterface $requirementTypeService)
    {
        $this->requirementTypeService = $requirementTypeService;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function index(): AnonymousResourceCollection
    {
        return RequirementTypeResource::collection(
            $this->requirementTypeService->all(),
        );
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \App\Http\Requests\Admin\RequirementType\RequirementTypeStoreRequest $request
     * @return \App\Http\Resources\Admin\RequirementTypeResource
     */
    public function store(RequirementTypeStoreRequest $request): RequirementTypeResource
    {
        try {
            /** @var array<string, mixed> $data */
            $data = $request->validated();

            $requirementType = $this->requirementTypeService->create($data);

            return RequirementTypeResource::make($requirementType);
        } catch (Exception $e) {
            $this->handleDefaultExceptionLog('Failed to create a new requirement type.', $e);

            throw $e;
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \App\Http\Requests\Admin\RequirementType\RequirementTypeUpdateRequest $request
     * @param mixed $id
     * @return \App\Http\Resources\Admin\RequirementTypeResource
     */
    public function update(RequirementTypeUpdateRequest $request, mixed $id): RequirementTypeResource
    {
        try {
            /** @var array<string, mixed> $data */
            $data = $request->validated();

            $requirementType = $this->requirementTypeService->update($data, $id);

            return RequirementTypeResource::make($requirementType);
        } catch (Exception $e) {
            $this->handleDefaultExceptionLog('Failed to update a requirement type.', $e);

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
        $this->requirementTypeService->delete($id);
    }
}
