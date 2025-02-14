<?php

namespace Tests\Unit\Repositories;

use Tests\TestCase;
use App\Models\Category;
use App\Contracts\Repositories\CategoryRepositoryInterface;

class CategoryRepositoryTest extends TestCase
{
    /**
     * The category repository.
     *
     * @var \App\Contracts\Repositories\CategoryRepositoryInterface
     */
    private CategoryRepositoryInterface $categoryRepository;

    /**
     * Setup new test environments.
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->categoryRepository = app(CategoryRepositoryInterface::class);
    }

    /**
     * Test if CategoryRepository uses the Category model correctly.
     *
     * @return void
     */
    public function test_category_repository_uses_category_model(): void
    {
        /** @var \App\Repositories\CategoryRepository $categoryRepository */
        $categoryRepository = $this->categoryRepository;

        $this->assertInstanceOf(Category::class, $categoryRepository->model());
    }
}
