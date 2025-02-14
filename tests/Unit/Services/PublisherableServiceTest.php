<?php

namespace Tests\Unit\Services;

use Mockery;
use Tests\TestCase;
use App\DTO\SteamAppDTO;
use Mockery\MockInterface;
use Illuminate\Database\Eloquent\Model;
use App\Models\{Publisher, Publisherable};
use App\Repositories\PublisherableRepository;
use App\Contracts\Repositories\PublisherableRepositoryInterface;
use App\Contracts\Services\{
    PublisherServiceInterface,
    PublisherableServiceInterface,
};

class PublisherableServiceTest extends TestCase
{
    /**
     * The publisher service.
     *
     * @var \Mockery\MockInterface
     */
    private MockInterface $publisherService;

    /**
     * The publisherable service.
     *
     * @var \App\Contracts\Services\PublisherableServiceInterface
     */
    private PublisherableServiceInterface $publisherableService;

    /**
     * Setup new test environments.
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->publisherService = Mockery::mock(PublisherServiceInterface::class);

        $this->app->instance(PublisherServiceInterface::class, $this->publisherService);

        $this->publisherableService = app(PublisherableServiceInterface::class);
    }

    /**
     * Test if PublisherableService uses the publisherable repository correctly.
     *
     * @return void
     */
    public function test_publisherable_repository_uses_publisherable_repository(): void
    {
        $this->app->instance(PublisherableRepositoryInterface::class, new PublisherableRepository());

        /** @var \App\Services\PublisherableService $publisherableService */
        $publisherableService = app(PublisherableServiceInterface::class);

        $this->assertInstanceOf(PublisherableRepository::class, $publisherableService->repository());
    }

    /**
     * Test if can create publisherables for steam app.
     *
     * @return void
     */
    public function test_if_can_create_publisherables_for_steam_app(): void
    {
        $model = Mockery::mock(Model::class);
        $model->shouldReceive('getAttribute')->with('id')->andReturn(1);

        $modelPublisher = Mockery::mock(Publisher::class);
        $modelPublisher->shouldReceive('getAttribute')->with('id')->andReturn(1);

        $dto = Mockery::mock(SteamAppDTO::class);
        $dto->publishers = $publisher = [ // @phpstan-ignore-line
            'Action',
        ];

        $model
            ->shouldReceive('getKey')
            ->twice()
            ->withNoArgs()
            ->andReturn(1);

        $this->publisherService
            ->shouldReceive('firstOrCreate')
            ->once()
            ->with(['name' => $publisher[0]])
            ->andReturn($modelPublisher);

        $repository = Mockery::mock(PublisherableRepositoryInterface::class);
        $this->app->instance(PublisherableRepositoryInterface::class, $repository);

        /** @var \App\Models\Publisher $modelPublisher */
        /** @var \Illuminate\Database\Eloquent\Model $model */
        $repository->shouldReceive('create')
            ->once()
            ->with([
                'publisherable_type' => $model::class,
                'publisher_id' => $modelPublisher->id,
                'publisherable_id' => $model->getKey(),
            ])->andReturn(Mockery::mock(Publisherable::class));

        /** @var \App\DTO\SteamAppDTO $dto */
        /** @var \Illuminate\Database\Eloquent\Model $model */
        $this->publisherableService->createPublishersForSteamApp($model, $dto);

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
