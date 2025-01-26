<?php

namespace Tests\Feature\Http\Game;

use App\Models\Game;
use Tests\Feature\Http\BaseIntegrationTesting;
use Tests\Traits\{
    HasDummyGame,
    HasDummyCategory,
};

class FilterByCategoriesTest extends BaseIntegrationTesting
{
    use HasDummyGame;
    use HasDummyCategory;

    /**
     * Test if can find correctly games quantity by categories.
     *
     * @return void
     */
    public function test_if_can_find_correctly_games_quantity_by_categories(): void
    {
        $category = $this->createDummyCategory([
            'slug' => 'Category-slug',
        ]);

        $data = [
            'attribute' => 'categories',
            'value' => $category->slug,
        ];

        $this->getJson(route('games.filters.find', $data))->assertOk()->assertJsonCount(0, 'data');

        $this->createDummyGames(4)->each(function (Game $game) use ($category) {
            $game->categories()->save($category);
        });

        $this->getJson(route('games.filters.find', $data))->assertOk()->assertJsonCount(4, 'data');
    }
}
