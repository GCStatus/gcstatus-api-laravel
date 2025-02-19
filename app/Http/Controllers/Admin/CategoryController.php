<?php

namespace App\Http\Controllers\Admin;

use Exception;
use App\Http\Controllers\Controller;
use App\Http\Resources\Admin\CategoryResource;
use App\Contracts\Services\CategoryServiceInterface;
use Illuminate\Routing\Controllers\{Middleware, HasMiddleware};
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use App\Http\Requests\Admin\Category\{CategoryStoreRequest, CategoryUpdateRequest};

class CategoryController extends Controller implements HasMiddleware
{
    /**
     * Get the middleware that should be assigned to the controller.
     *
     * @return array<int, mixed>
     */
    public static function middleware(): array
    {
        return [
            new Middleware('scopes:view:categories'),
            new Middleware('scopes:create:categories', only: ['store']),
            new Middleware('scopes:update:categories', only: ['update']),
            new Middleware('scopes:delete:categories', only: ['destroy']),
        ];
    }

    /**
     * The category service.
     *
     * @var \App\Contracts\Services\CategoryServiceInterface
     */
    private CategoryServiceInterface $categoryService;

    /**
     * Create a new controller instance.
     *
     * @param \App\Contracts\Services\CategoryServiceInterface $categoryService
     * @return void
     */
    public function __construct(CategoryServiceInterface $categoryService)
    {
        $this->categoryService = $categoryService;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function index(): AnonymousResourceCollection
    {
        return CategoryResource::collection(
            $this->categoryService->all(),
        );
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \App\Http\Requests\Admin\Category\CategoryStoreRequest $request
     * @return \App\Http\Resources\Admin\CategoryResource
     */
    public function store(CategoryStoreRequest $request): CategoryResource
    {
        try {
            /** @var array<string, mixed> $data */
            $data = $request->validated();

            $category = $this->categoryService->create($data);

            return CategoryResource::make($category);
        } catch (Exception $e) {
            $this->handleDefaultExceptionLog('Failed to create a new category.', $e);

            throw $e;
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \App\Http\Requests\Admin\Category\CategoryUpdateRequest $request
     * @param mixed $id
     * @return \App\Http\Resources\Admin\CategoryResource
     */
    public function update(CategoryUpdateRequest $request, mixed $id): CategoryResource
    {
        try {
            /** @var array<string, mixed> $data */
            $data = $request->validated();

            $category = $this->categoryService->update($data, $id);

            return CategoryResource::make($category);
        } catch (Exception $e) {
            $this->handleDefaultExceptionLog('Failed to update a category.', $e);

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
        $this->categoryService->delete($id);
    }
}
