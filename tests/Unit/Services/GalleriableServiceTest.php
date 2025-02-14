<?php

namespace Tests\Unit\Services;

use Mockery;
use Tests\TestCase;
use App\DTO\SteamAppDTO;
use Illuminate\Database\Eloquent\Model;
use App\Models\{Galleriable, MediaType};
use App\Repositories\GalleriableRepository;
use App\Contracts\Services\GalleriableServiceInterface;
use App\Contracts\Repositories\GalleriableRepositoryInterface;

class GalleriableServiceTest extends TestCase
{
    /**
     * The galleriable service.
     *
     * @var \App\Contracts\Services\GalleriableServiceInterface
     */
    private GalleriableServiceInterface $galleriableService;

    /**
     * Setup new test environments.
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->galleriableService = app(GalleriableServiceInterface::class);
    }

    /**
     * Test if GalleriableService uses the Galleriable repository correctly.
     *
     * @return void
     */
    public function test_galleriable_repository_uses_galleriable_repository(): void
    {
        $this->app->instance(GalleriableRepositoryInterface::class, new GalleriableRepository());

        /** @var \App\Services\GalleriableService $galleriableService */
        $galleriableService = app(GalleriableServiceInterface::class);

        $this->assertInstanceOf(GalleriableRepository::class, $galleriableService->repository());
    }

    /**
     * Test if can create galleriables for steam app.
     *
     * @return void
     */
    public function test_if_can_create_Galleriables_for_steam_app(): void
    {
        $model = Mockery::mock(Model::class);
        $model->shouldReceive('getAttribute')->with('id')->andReturn(1);

        $modelGallery = Mockery::mock(Galleriable::class);
        $modelGallery->shouldReceive('getAttribute')->with('id')->andReturn(1);

        $dto = Mockery::mock(SteamAppDTO::class);
        $dto->galleries = [ // @phpstan-ignore-line
            $photo = [
                'type' => MediaType::PHOTO_CONST_ID,
                'path' => 'https://shared.akamai.steamstatic.com/store_item_assets/steam/apps/2322010/ss_7c59382e67eadf779e0e15c3837ee91158237f11.1920x1080.jpg?t=1738256985',
            ],
            $video = [
                'type' => MediaType::VIDEO_CONST_ID,
                'path' => 'http://video.akamai.steamstatic.com/store_trailers/257054534/movie_max_vp9.webm?t=1726759092',
            ],
        ];

        $model
            ->shouldReceive('getKey')
            ->times(4)
            ->withNoArgs()
            ->andReturn(1);

        $repository = Mockery::mock(GalleriableRepositoryInterface::class);
        $this->app->instance(GalleriableRepositoryInterface::class, $repository);

        /** @var \Illuminate\Database\Eloquent\Model $model */
        $repository->shouldReceive('create')
            ->once()
            ->with([
                'path' => $photo['path'],
                'media_type_id' => $photo['type'],
                'galleriable_type' => $model::class,
                'galleriable_id' => $model->getKey(),
            ])->andReturn(Mockery::mock(Galleriable::class));

        $repository->shouldReceive('create')
            ->once()
            ->with([
                'path' => $video['path'],
                'media_type_id' => $video['type'],
                'galleriable_type' => $model::class,
                'galleriable_id' => $model->getKey(),
            ])->andReturn(Mockery::mock(Galleriable::class));

        /** @var \App\DTO\SteamAppDTO $dto */
        /** @var \Illuminate\Database\Eloquent\Model $model */
        $this->galleriableService->createGalleriablesForSteamApp($model, $dto);

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
