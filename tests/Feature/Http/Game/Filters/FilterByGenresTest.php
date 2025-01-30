<?php

namespace Tests\Feature\Http\Game\Filters;

use App\Models\Game;
use Tests\Feature\Http\BaseIntegrationTesting;
use Tests\Traits\{
    HasDummyGame,
    HasDummyGenre,
};

class FilterByGenresTest extends BaseIntegrationTesting
{
    use HasDummyGame;
    use HasDummyGenre;

    /**
     * Test if can find correctly games quantity by genres.
     *
     * @return void
     */
    public function test_if_can_find_correctly_games_quantity_by_genres(): void
    {
        $genre = $this->createDummyGenre([
            'slug' => 'genre-slug',
        ]);

        $data = [
            'attribute' => 'genres',
            'value' => $genre->slug,
        ];

        $this->getJson(route('games.filters.find', $data))->assertOk()->assertJsonCount(0, 'data');

        $this->createDummyGames(4)->each(function (Game $game) use ($genre) {
            $game->genres()->save($genre);
        });

        $this->getJson(route('games.filters.find', $data))->assertOk()->assertJsonCount(4, 'data');
    }
}
