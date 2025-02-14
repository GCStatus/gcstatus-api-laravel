<?php

namespace Tests\Unit\Services;

use Mockery;
use Tests\TestCase;
use App\DTO\SteamAppDTO;
use Mockery\MockInterface;
use Illuminate\Database\Eloquent\Model;
use App\Models\{Category, Categoriable};
use App\Repositories\CategoriableRepository;
use App\Contracts\Repositories\CategoriableRepositoryInterface;
use App\Contracts\Services\{
    CategoryServiceInterface,
    CategoriableServiceInterface,
};

class CategoriableServiceTest extends TestCase
{
    /**
     * The category service.
     *
     * @var \Mockery\MockInterface
     */
    private MockInterface $categoryService;

    /**
     * The categoriable service.
     *
     * @var \App\Contracts\Services\CategoriableServiceInterface
     */
    private CategoriableServiceInterface $categoriableService;

    /**
     * Setup new test environments.
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->categoryService = Mockery::mock(CategoryServiceInterface::class);

        $this->app->instance(CategoryServiceInterface::class, $this->categoryService);

        $this->categoriableService = app(CategoriableServiceInterface::class);
    }

    /**
     * Test if CategoriableService uses the Categoriable repository correctly.
     *
     * @return void
     */
    public function test_categoriable_repository_uses_categoriable_repository(): void
    {
        $this->app->instance(CategoriableRepositoryInterface::class, new CategoriableRepository());

        /** @var \App\Services\CategoriableService $categoriableService */
        $categoriableService = app(CategoriableServiceInterface::class);

        $this->assertInstanceOf(CategoriableRepository::class, $categoriableService->repository());
    }

    /**
     * Test if can create categoriables for steam app.
     *
     * @return void
     */
    public function test_if_can_create_categoriables_for_steam_app(): void
    {
        $model = Mockery::mock(Model::class);
        $model->shouldReceive('getAttribute')->with('id')->andReturn(1);

        $modelCategory = Mockery::mock(Category::class);
        $modelCategory->shouldReceive('getAttribute')->with('id')->andReturn(1);

        $dto = Mockery::mock(SteamAppDTO::class);
        $dto->categories = [ // @phpstan-ignore-line
            $category = [
                'id' => 1,
                'description' => 'Action',
            ],
        ];

        $model
            ->shouldReceive('getKey')
            ->twice()
            ->withNoArgs()
            ->andReturn(1);

        $this->categoryService
            ->shouldReceive('firstOrCreate')
            ->once()
            ->with(['name' => $category['description']])
            ->andReturn($modelCategory);

        $repository = Mockery::mock(CategoriableRepositoryInterface::class);
        $this->app->instance(CategoriableRepositoryInterface::class, $repository);

        /** @var \App\Models\Category $modelCategory */
        /** @var \Illuminate\Database\Eloquent\Model $model */
        $repository->shouldReceive('create')
            ->once()
            ->with([
                'categoriable_type' => $model::class,
                'categoriable_id' => $model->getKey(),
                'category_id'     => $modelCategory->id,
            ])->andReturn(Mockery::mock(Categoriable::class));

        /** @var \App\DTO\SteamAppDTO $dto */
        /** @var \Illuminate\Database\Eloquent\Model $model */
        $this->categoriableService->createCategoriesForSteamApp($model, $dto);

        $this->assertEquals(3, Mockery::getContainer()->mockery_getExpectationCount(), 'Mock expectations met.');
    }


    /**
     * Tear down application tests.
     *
     * @return void
     */
    public function tearDown(): void
    {
        Mockery::close();

        parent::tearDown();
    }
}
