<?php

namespace Tests\Unit\Services;

use Mockery;
use Tests\TestCase;
use App\DTO\SteamAppDTO;
use Mockery\MockInterface;
use App\Models\{Store, Storeable};
use Illuminate\Database\Eloquent\Model;
use App\Repositories\StoreableRepository;
use App\Contracts\Repositories\StoreableRepositoryInterface;
use App\Contracts\Services\{
    StoreServiceInterface,
    StoreableServiceInterface,
};

class StoreableServiceTest extends TestCase
{
    /**
     * The store service.
     *
     * @var \Mockery\MockInterface
     */
    private MockInterface $storeService;

    /**
     * The Storeable service.
     *
     * @var \App\Contracts\Services\StoreableServiceInterface
     */
    private StoreableServiceInterface $storeableService;

    /**
     * Setup new test environments.
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->storeService = Mockery::mock(StoreServiceInterface::class);

        $this->app->instance(StoreServiceInterface::class, $this->storeService);

        $this->storeableService = app(StoreableServiceInterface::class);
    }

    /**
     * Test if StoreableService uses the Storeable repository correctly.
     *
     * @return void
     */
    public function test_Storeable_repository_uses_Storeable_repository(): void
    {
        $this->app->instance(StoreableRepositoryInterface::class, new StoreableRepository());

        /** @var \App\Services\StoreableService $storeableService */
        $storeableService = app(StoreableServiceInterface::class);

        $this->assertInstanceOf(StoreableRepository::class, $storeableService->repository());
    }

    /**
     * Test if can create Storeables for steam app.
     *
     * @return void
     */
    public function test_if_can_create_Storeables_for_steam_app(): void
    {
        $model = Mockery::mock(Model::class);
        $model->shouldReceive('getAttribute')->with('id')->andReturn(1);

        $modelStore = Mockery::mock(Store::class);
        $modelStore->shouldReceive('getAttribute')->with('id')->andReturn(1);

        $dto = Mockery::mock(SteamAppDTO::class);
        $dto->price = 9999; // @phpstan-ignore-line
        $dto->appId = 123; // @phpstan-ignore-line

        $model
            ->shouldReceive('getKey')
            ->twice()
            ->withNoArgs()
            ->andReturn(1);

        $this->storeService
            ->shouldReceive('findOrFail')
            ->once()
            ->with(Store::STEAM_STORE_ID)
            ->andReturn($modelStore);

        $repository = Mockery::mock(StoreableRepositoryInterface::class);
        $this->app->instance(StoreableRepositoryInterface::class, $repository);

        /** @var \App\DTO\SteamAppDTO $dto */
        /** @var \App\Models\Store $modelStore */
        /** @var \Illuminate\Database\Eloquent\Model $model */
        $repository->shouldReceive('create')
            ->once()
            ->with([
                'price' => $dto->price,
                'store_id' => $modelStore->id,
                'store_item_id' => $dto->appId,
                'storeable_type' => $model::class,
                'storeable_id' => $model->getKey(),
                'url' => sprintf('https://store.steampowered.com/app/%s', $dto->appId),
            ])->andReturn(Mockery::mock(Storeable::class));

        $this->storeableService->createStoreableForSteamApp($model, $dto);

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
