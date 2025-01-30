<?php

namespace Tests\Feature\Http\Game\Filters;

use App\Models\Game;
use Tests\Feature\Http\BaseIntegrationTesting;
use Tests\Traits\{
    HasDummyTag,
    HasDummyGame,
};

class FilterByTagsTest extends BaseIntegrationTesting
{
    use HasDummyTag;
    use HasDummyGame;

    /**
     * Test if can find correctly games quantity by tags.
     *
     * @return void
     */
    public function test_if_can_find_correctly_games_quantity_by_tags(): void
    {
        $tag = $this->createDummyTag([
            'slug' => 'tag-slug',
        ]);

        $data = [
            'attribute' => 'tags',
            'value' => $tag->slug,
        ];

        $this->getJson(route('games.filters.find', $data))->assertOk()->assertJsonCount(0, 'data');

        $this->createDummyGames(4)->each(function (Game $game) use ($tag) {
            $game->tags()->save($tag);
        });

        $this->getJson(route('games.filters.find', $data))->assertOk()->assertJsonCount(4, 'data');
    }
}
