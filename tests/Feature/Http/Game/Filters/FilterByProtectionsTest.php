<?php

namespace Tests\Feature\Http\Game\Filters;

use App\Models\Game;
use Illuminate\Database\Eloquent\Collection;
use Tests\Feature\Http\BaseIntegrationTesting;
use Tests\Traits\{
    HasDummyGame,
    HasDummyCrack,
    HasDummyProtection,
};

class FilterByProtectionsTest extends BaseIntegrationTesting
{
    use HasDummyGame;
    use HasDummyCrack;
    use HasDummyProtection;

    /**
     * Test if can find correctly games quantity by protections.
     *
     * @return void
     */
    public function test_if_can_find_correctly_games_quantity_by_protections(): void
    {
        $protection = $this->createDummyProtection();

        $data = [
            'attribute' => 'protections',
            'value' => $protection->slug,
        ];

        $this->getJson(route('games.filters.find', $data))->assertOk()->assertJsonCount(0, 'data');

        $games = $this->createDummyGames(4)->each(function (Game $game) use ($protection) {
            $this->createDummyCrackTo($game, [
                'protection_id' => $protection->id,
            ]);
        });

        $this->removeWrongGames($games);

        $this->getJson(route('games.filters.find', $data))->assertOk()->assertJsonCount(4, 'data');
    }

    /**
     * Remove the games created on crack creation.
     *
     * @param \Illuminate\Database\Eloquent\Collection<int, \App\Models\Game> $games
     * @return void
     */
    private function removeWrongGames(Collection $games): void
    {
        Game::whereNotIn('id', $games->pluck('id')->toArray())->delete();
    }
}
