<?php

namespace Tests\Unit\Services;

use Mockery;
use Tests\TestCase;
use App\DTO\SteamAppDTO;
use Mockery\MockInterface;
use App\Models\{Genre, Genreable};
use Illuminate\Database\Eloquent\Model;
use App\Repositories\GenreableRepository;
use App\Contracts\Repositories\GenreableRepositoryInterface;
use App\Contracts\Services\{
    GenreServiceInterface,
    GenreableServiceInterface,
};

class GenreableServiceTest extends TestCase
{
    /**
     * The genre service.
     *
     * @var \Mockery\MockInterface
     */
    private MockInterface $genreService;

    /**
     * The genreable service.
     *
     * @var \App\Contracts\Services\GenreableServiceInterface
     */
    private GenreableServiceInterface $genreableService;

    /**
     * Setup new test environments.
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->genreService = Mockery::mock(GenreServiceInterface::class);

        $this->app->instance(GenreServiceInterface::class, $this->genreService);

        $this->genreableService = app(GenreableServiceInterface::class);
    }

    /**
     * Test if GenreableService uses the Genreable repository correctly.
     *
     * @return void
     */
    public function test_genreable_repository_uses_genreable_repository(): void
    {
        $this->app->instance(GenreableRepositoryInterface::class, new GenreableRepository());

        /** @var \App\Services\GenreableService $genreableService */
        $genreableService = app(GenreableServiceInterface::class);

        $this->assertInstanceOf(GenreableRepository::class, $genreableService->repository());
    }

    /**
     * Test if can create genreables for steam app.
     *
     * @return void
     */
    public function test_if_can_create_genreables_for_steam_app(): void
    {
        $model = Mockery::mock(Model::class);
        $model->shouldReceive('getAttribute')->with('id')->andReturn(1);

        $modelGenre = Mockery::mock(Genre::class);
        $modelGenre->shouldReceive('getAttribute')->with('id')->andReturn(1);

        $dto = Mockery::mock(SteamAppDTO::class);
        $dto->genres = [ // @phpstan-ignore-line
            $genre = [
                'id' => 1,
                'description' => 'Action',
            ],
        ];

        $model
            ->shouldReceive('getKey')
            ->twice()
            ->withNoArgs()
            ->andReturn(1);

        $this->genreService
            ->shouldReceive('firstOrCreate')
            ->once()
            ->with(['name' => $genre['description']])
            ->andReturn($modelGenre);

        $repository = Mockery::mock(GenreableRepositoryInterface::class);
        $this->app->instance(GenreableRepositoryInterface::class, $repository);

        /** @var \App\Models\Genre $modelGenre */
        /** @var \Illuminate\Database\Eloquent\Model $model */
        $repository->shouldReceive('create')
            ->once()
            ->with([
                'genre_id' => $modelGenre->id,
                'genreable_type' => $model::class,
                'genreable_id' => $model->getKey(),
            ])->andReturn(Mockery::mock(Genreable::class));

        /** @var \App\DTO\SteamAppDTO $dto */
        /** @var \Illuminate\Database\Eloquent\Model $model */
        $this->genreableService->createGenresForSteamApp($model, $dto);

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
