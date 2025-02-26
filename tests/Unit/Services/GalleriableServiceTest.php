<?php

namespace Tests\Unit\Services;

use Mockery;
use Tests\TestCase;
use App\DTO\SteamAppDTO;
use Mockery\MockInterface;
use Illuminate\Http\UploadedFile;
use Illuminate\Database\Eloquent\Model;
use App\Models\{Galleriable, MediaType};
use App\Repositories\GalleriableRepository;
use App\Contracts\Repositories\GalleriableRepositoryInterface;
use App\Contracts\Services\{GalleriableServiceInterface, StorageServiceInterface};

class GalleriableServiceTest extends TestCase
{
    /**
     * The galleriable service.
     *
     * @var \App\Contracts\Services\GalleriableServiceInterface
     */
    private GalleriableServiceInterface $galleriableService;

    /**
     * The storage service.
     *
     * @var \Mockery\MockInterface
     */
    private MockInterface $storageService;

    /**
     * The galleriable repository.
     *
     * @var \Mockery\MockInterface
     */
    private MockInterface $galleriableRepository;

    /**
     * Setup new test environments.
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->storageService = Mockery::mock(StorageServiceInterface::class);
        $this->galleriableRepository = Mockery::mock(GalleriableRepositoryInterface::class);

        $this->app->instance(StorageServiceInterface::class, $this->storageService);
        $this->app->instance(GalleriableRepositoryInterface::class, $this->galleriableRepository);

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
     * Test if can create a new galleriable as url.
     *
     * @return void
     */
    public function test_if_can_create_a_new_galleriable_as_url(): void
    {
        $mediaType = Mockery::mock(MediaType::class);
        $mediaType->shouldReceive('getAttribute')->with('id')->andReturn(MediaType::PHOTO_CONST_ID);

        $galleriable = Mockery::mock(Galleriable::class);

        /** @var \App\Models\MediaType $mediaType */
        $data = [
            's3' => false,
            'galleriable_id' => 123,
            'url' => 'https://google.com',
            'media_type_id' => $mediaType->id,
            'galleriable_type' => fake()->randomElement(Galleriable::ALLOWED_GALLERIABLES_TYPE),
        ];

        $this->galleriableRepository
            ->shouldReceive('create')
            ->once()
            ->with($data + [
                'path' => $data['url'],
            ])->andReturn($galleriable);

        $result = $this->galleriableService->create($data);

        $this->assertEquals($result, $galleriable);
        $this->assertInstanceOf(Galleriable::class, $result);

        $this->assertEquals(1, Mockery::getContainer()->mockery_getExpectationCount(), 'Mock expectations met.');
    }

    /**
     * Test if can create a new galleriable as file.
     *
     * @return void
     */
    public function test_if_can_create_a_new_galleriable_as_file(): void
    {
        $path = fake()->imageUrl();

        $mediaType = Mockery::mock(MediaType::class);
        $mediaType->shouldReceive('getAttribute')->with('id')->andReturn(MediaType::PHOTO_CONST_ID);

        $galleriable = Mockery::mock(Galleriable::class);

        /** @var \App\Models\MediaType $mediaType */
        $data = [
            's3' => true,
            'galleriable_id' => 123,
            'media_type_id' => $mediaType->id,
            'file' => UploadedFile::fake()->create('fake.png'),
            'galleriable_type' => fake()->randomElement(Galleriable::ALLOWED_GALLERIABLES_TYPE),
        ];

        $this->storageService
            ->shouldReceive('create')
            ->once()
            ->with($data['file'], 'games')
            ->andReturn($path);

        $this->galleriableRepository
            ->shouldReceive('create')
            ->once()
            ->with($data + [
                'path' => $path,
            ])->andReturn($galleriable);

        $result = $this->galleriableService->create($data);

        $this->assertEquals($result, $galleriable);
        $this->assertInstanceOf(Galleriable::class, $result);

        $this->assertEquals(2, Mockery::getContainer()->mockery_getExpectationCount(), 'Mock expectations met.');
    }

    /**
     * Test if can delete an url galleriable.
     *
     * @return void
     */
    public function test_if_can_delete_an_url_galleriable(): void
    {
        $id = 1;

        $galleriable = Mockery::mock(Galleriable::class);
        $galleriable->shouldReceive('getAttribute')->with('id')->andReturn($id);
        $galleriable->shouldReceive('getAttribute')->with('s3')->andReturnFalse();

        $this->galleriableRepository
            ->shouldReceive('findOrFail')
            ->once()
            ->with($id)
            ->andReturn($galleriable);

        $this->storageService->shouldNotReceive('delete');

        $galleriable
            ->shouldReceive('delete')
            ->once()
            ->withNoArgs();

        $this->galleriableService->delete($id);

        $this->assertEquals(3, Mockery::getContainer()->mockery_getExpectationCount(), 'Mock expectations met.');
    }

    /**
     * Test if can delete a file galleriable.
     *
     * @return void
     */
    public function test_if_can_delete_a_file_galleriable(): void
    {
        $id = 1;

        $galleriable = Mockery::mock(Galleriable::class);
        $galleriable->shouldReceive('getAttribute')->with('id')->andReturn($id);
        $galleriable->shouldReceive('getAttribute')->with('s3')->andReturnTrue();
        $galleriable->shouldReceive('getAttribute')->with('path')->andReturn(fake()->imageUrl());

        $this->galleriableRepository
            ->shouldReceive('findOrFail')
            ->once()
            ->with($id)
            ->andReturn($galleriable);

        $galleriable
            ->shouldReceive('delete')
            ->once()
            ->withNoArgs();

        /** @var \App\Models\Galleriable $galleriable */
        $this->storageService
            ->shouldReceive('delete')
            ->once()
            ->with($galleriable->path);

        $this->galleriableService->delete($id);

        $this->assertEquals(3, Mockery::getContainer()->mockery_getExpectationCount(), 'Mock expectations met.');
    }

    /**
     * Test if can create galleriables for steam app.
     *
     * @return void
     */
    public function test_if_can_create_galleriables_for_steam_app(): void
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
