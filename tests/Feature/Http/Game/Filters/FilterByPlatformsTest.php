<?php

namespace Tests\Feature\Http\Game;

use App\Models\Game;
use Tests\Feature\Http\BaseIntegrationTesting;
use Tests\Traits\{
    HasDummyGame,
    HasDummyPlatform,
};

class FilterByPlatformsTest extends BaseIntegrationTesting
{
    use HasDummyGame;
    use HasDummyPlatform;

    /**
     * Test if can find correctly games quantity by platforms.
     *
     * @return void
     */
    public function test_if_can_find_correctly_games_quantity_by_platforms(): void
    {
        $platform = $this->createDummyPlatform([
            'slug' => 'platform-slug',
        ]);

        $data = [
            'attribute' => 'platforms',
            'value' => $platform->slug,
        ];

        $this->getJson(route('games.filters.find', $data))->assertOk()->assertJsonCount(0, 'data');

        $this->createDummyGames(4)->each(function (Game $game) use ($platform) {
            $game->platforms()->save($platform);
        });

        $this->getJson(route('games.filters.find', $data))->assertOk()->assertJsonCount(4, 'data');
    }
}
