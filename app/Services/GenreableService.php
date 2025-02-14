<?php

namespace App\Services;

use App\DTO\SteamAppDTO;
use Illuminate\Database\Eloquent\Model;
use App\Contracts\Repositories\GenreableRepositoryInterface;
use App\Contracts\Services\{
    GenreServiceInterface,
    GenreableServiceInterface,
};

class GenreableService extends AbstractService implements GenreableServiceInterface
{
    /**
     * The genre service.
     *
     * @var \App\Contracts\Services\GenreServiceInterface
     */
    private GenreServiceInterface $genreService;

    /**
     * Create a new class instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->genreService = app(GenreServiceInterface::class);
    }

    /**
     * The genreable repository.
     *
     * @return \App\Contracts\Repositories\GenreableRepositoryInterface
     */
    public function repository(): GenreableRepositoryInterface
    {
        return app(GenreableRepositoryInterface::class);
    }

    /**
     * Create the steam app genres.
     *
     * @param \Illuminate\Database\Eloquent\Model $app
     * @param \App\DTO\SteamAppDTO $formattedApp
     * @return void
     */
    public function createGenresForSteamApp(Model $app, SteamAppDTO $formattedApp): void
    {
        foreach ($formattedApp->genres as $genre) {
            /** @var array<string, mixed> $genre */
            /** @var \App\Models\Genre $modelGenre */
            $modelGenre = $this->genreService->firstOrCreate([
                'name' => $genre['description'],
            ]);

            $this->create([
                'genre_id' => $modelGenre->id,
                'genreable_type' => $app::class,
                'genreable_id' => $app->getKey(),
            ]);
        }
    }
}
