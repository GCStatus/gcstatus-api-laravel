<?php

namespace Tests\Unit\Services;

use Tests\TestCase;
use App\Repositories\CategoryRepository;
use App\Contracts\Services\CategoryServiceInterface;
use App\Contracts\Repositories\CategoryRepositoryInterface;

class CategoryServiceTest extends TestCase
{
    /**
     * Test if CategoryService uses the Category repository correctly.
     *
     * @return void
     */
    public function test_Category_repository_uses_Category_repository(): void
    {
        $this->app->instance(CategoryRepositoryInterface::class, new CategoryRepository());

        /** @var \App\Services\CategoryService $CategoryService */
        $CategoryService = app(CategoryServiceInterface::class);

        $this->assertInstanceOf(CategoryRepository::class, $CategoryService->repository());
    }
}
