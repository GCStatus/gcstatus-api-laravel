<?php

namespace Tests\Feature\Http\Game;

use App\Models\Game;
use Tests\Feature\Http\BaseIntegrationTesting;
use Tests\Traits\{
    HasDummyGame,
    HasDummyPublisher,
};

class FilterByPublishersTest extends BaseIntegrationTesting
{
    use HasDummyGame;
    use HasDummyPublisher;

    /**
     * Test if can find correctly games quantity by publishers.
     *
     * @return void
     */
    public function test_if_can_find_correctly_games_quantity_by_publishers(): void
    {
        $publisher = $this->createDummyPublisher([
            'slug' => 'Publisher-slug',
        ]);

        $data = [
            'attribute' => 'publishers',
            'value' => $publisher->slug,
        ];

        $this->getJson(route('games.filters.find', $data))->assertOk()->assertJsonCount(0, 'data');

        $this->createDummyGames(4)->each(function (Game $game) use ($publisher) {
            $game->publishers()->save($publisher);
        });

        $this->getJson(route('games.filters.find', $data))->assertOk()->assertJsonCount(4, 'data');
    }
}
