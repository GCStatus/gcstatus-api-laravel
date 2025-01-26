<?php

namespace Tests\Feature\Http\Game;

use App\Models\Game;
use Illuminate\Database\Eloquent\Collection;
use Tests\Feature\Http\BaseIntegrationTesting;
use Tests\Traits\{
    HasDummyGame,
    HasDummyCrack,
    HasDummyCracker,
};

class FilterByCrackersTest extends BaseIntegrationTesting
{
    use HasDummyGame;
    use HasDummyCrack;
    use HasDummyCracker;

    /**
     * Test if can find correctly games quantity by Crackers.
     *
     * @return void
     */
    public function test_if_can_find_correctly_games_quantity_by_Crackers(): void
    {
        $cracker = $this->createDummyCracker();

        $data = [
            'attribute' => 'crackers',
            'value' => $cracker->slug,
        ];

        $this->getJson(route('games.filters.find', $data))->assertOk()->assertJsonCount(0, 'data');

        $games = $this->createDummyGames(4)->each(function (Game $game) use ($cracker) {
            $this->createDummyCrackTo($game, [
                'cracker_id' => $cracker->id,
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
