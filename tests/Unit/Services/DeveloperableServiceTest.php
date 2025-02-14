<?php

namespace Tests\Unit\Services;

use Mockery;
use Tests\TestCase;
use App\DTO\SteamAppDTO;
use Mockery\MockInterface;
use Illuminate\Database\Eloquent\Model;
use App\Models\{Developer, Developerable};
use App\Repositories\DeveloperableRepository;
use App\Contracts\Repositories\DeveloperableRepositoryInterface;
use App\Contracts\Services\{
    DeveloperServiceInterface,
    DeveloperableServiceInterface,
};

class DeveloperableServiceTest extends TestCase
{
    /**
     * The developer service.
     *
     * @var \Mockery\MockInterface
     */
    private MockInterface $developerService;

    /**
     * The Developerable service.
     *
     * @var \App\Contracts\Services\DeveloperableServiceInterface
     */
    private DeveloperableServiceInterface $developerableService;

    /**
     * Setup new test environments.
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->developerService = Mockery::mock(DeveloperServiceInterface::class);

        $this->app->instance(DeveloperServiceInterface::class, $this->developerService);

        $this->developerableService = app(DeveloperableServiceInterface::class);
    }

    /**
     * Test if DeveloperableService uses the Developerable repository correctly.
     *
     * @return void
     */
    public function test_Developerable_repository_uses_Developerable_repository(): void
    {
        $this->app->instance(DeveloperableRepositoryInterface::class, new DeveloperableRepository());

        /** @var \App\Services\DeveloperableService $developerableService */
        $developerableService = app(DeveloperableServiceInterface::class);

        $this->assertInstanceOf(DeveloperableRepository::class, $developerableService->repository());
    }

    /**
     * Test if can create developerables for steam app.
     *
     * @return void
     */
    public function test_if_can_create_developerables_for_steam_app(): void
    {
        $model = Mockery::mock(Model::class);
        $model->shouldReceive('getAttribute')->with('id')->andReturn(1);

        $modelDeveloper = Mockery::mock(Developer::class);
        $modelDeveloper->shouldReceive('getAttribute')->with('id')->andReturn(1);

        $dto = Mockery::mock(SteamAppDTO::class);
        $dto->developers = $developer = [ // @phpstan-ignore-line
            'Action',
        ];

        $model
            ->shouldReceive('getKey')
            ->twice()
            ->withNoArgs()
            ->andReturn(1);

        $this->developerService
            ->shouldReceive('firstOrCreate')
            ->once()
            ->with(['name' => $developer[0]])
            ->andReturn($modelDeveloper);

        $repository = Mockery::mock(DeveloperableRepositoryInterface::class);
        $this->app->instance(DeveloperableRepositoryInterface::class, $repository);

        /** @var \App\Models\Developer $modelDeveloper */
        /** @var \Illuminate\Database\Eloquent\Model $model */
        $repository->shouldReceive('create')
            ->once()
            ->with([
                'developerable_type' => $model::class,
                'developer_id' => $modelDeveloper->id,
                'developerable_id' => $model->getKey(),
            ])->andReturn(Mockery::mock(Developerable::class));

        /** @var \App\DTO\SteamAppDTO $dto */
        /** @var \Illuminate\Database\Eloquent\Model $model */
        $this->developerableService->createDevelopersForSteamApp($model, $dto);

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
