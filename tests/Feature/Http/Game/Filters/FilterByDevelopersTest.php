<?php

namespace Tests\Feature\Http\Game\Filters;

use App\Models\Game;
use Tests\Feature\Http\BaseIntegrationTesting;
use Tests\Traits\{
    HasDummyGame,
    HasDummyDeveloper,
};

class FilterByDevelopersTest extends BaseIntegrationTesting
{
    use HasDummyGame;
    use HasDummyDeveloper;

    /**
     * Test if can find correctly games quantity by developers.
     *
     * @return void
     */
    public function test_if_can_find_correctly_games_quantity_by_developers(): void
    {
        $developer = $this->createDummyDeveloper([
            'slug' => 'Developer-slug',
        ]);

        $data = [
            'attribute' => 'developers',
            'value' => $developer->slug,
        ];

        $this->getJson(route('games.filters.find', $data))->assertOk()->assertJsonCount(0, 'data');

        $this->createDummyGames(4)->each(function (Game $game) use ($developer) {
            $game->developers()->save($developer);
        });

        $this->getJson(route('games.filters.find', $data))->assertOk()->assertJsonCount(4, 'data');
    }
}
