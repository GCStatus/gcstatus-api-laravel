<?php

namespace Tests\Unit\Resources\Admin;

use Mockery;
use App\Models\Category;
use App\Http\Resources\Admin\CategoryResource;
use Tests\Contracts\Resources\BaseResourceTesting;

class CategoryResourceTest extends BaseResourceTesting
{
    /**
     * Define the expected structure for CategoryResource.
     *
     * @var array<string, string>
     */
    protected array $expectedStructure = [
        'id' => 'int',
        'slug' => 'string',
        'name' => 'string',
        'created_at' => 'string',
        'updated_at' => 'string',
    ];

    /**
     * Get the resource class being tested.
     *
     * @return class-string<CategoryResource>
     */
    public function resource(): string
    {
        return CategoryResource::class;
    }

    /**
     * Provide a mock instance of Category for testing.
     *
     * @return \App\Models\Category
     */
    public function modelInstance(): Category
    {
        $categoryMock = Mockery::mock(Category::class)->makePartial();
        $categoryMock->shouldAllowMockingMethod('getAttribute');

        $categoryMock->shouldReceive('getAttribute')->with('id')->andReturn(1);
        $categoryMock->shouldReceive('getAttribute')->with('name')->andReturn(fake()->name());
        $categoryMock->shouldReceive('getAttribute')->with('slug')->andReturn(fake()->name());
        $categoryMock->shouldReceive('getAttribute')->with('created_at')->andReturn(fake()->date());
        $categoryMock->shouldReceive('getAttribute')->with('updated_at')->andReturn(fake()->date());

        /** @var \App\Models\Category $categoryMock */
        return $categoryMock;
    }

    /**
     * Cleanup Mockery after each test.
     *
     * @return void
     */
    protected function tearDown(): void
    {
        Mockery::close();

        parent::tearDown();
    }
}
