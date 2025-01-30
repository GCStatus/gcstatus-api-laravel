<?php

namespace Tests\Feature\Http\Game\Filters;

use App\Models\Game;
use Tests\Feature\Http\BaseIntegrationTesting;
use Tests\Traits\{
    HasDummyGame,
    HasDummyCrack,
    HasDummyStatus,
};

class FilterByCracksTest extends BaseIntegrationTesting
{
    use HasDummyGame;
    use HasDummyCrack;
    use HasDummyStatus;

    /**
     * Test if can't get uncracked games for cracked input filter.
     *
     * @return void
     */
    public function test_if_cant_get_uncracked_games_for_cracked_input_filter(): void
    {
        $uncrackedStatus = $this->createDummyStatus([
            'name' => 'uncracked',
        ]);

        $crackedStatus = $this->createDummyStatus([
            'name' => 'cracked',
        ]);

        $data = [
            'attribute' => 'cracks',
            'value' => $crackedStatus->name,
        ];

        $this->createDummyGames(4)->each(function (Game $game) use ($uncrackedStatus) {
            $game->crack()->save(
                $this->createDummyCrack([
                    'status_id' => $uncrackedStatus->id,
                ]),
            );
        });

        $this->getJson(route('games.filters.find', $data))->assertOk()->assertJsonCount(0, 'data');
    }

    /**
     * Test if can find cracked and cracked-oneday games for cracked input filter.
     *
     * @return void
     */
    public function test_if_can_find_cracked_and_cracked_oneday_games_for_cracked_input_filter(): void
    {
        $crackedStatus = $this->createDummyStatus([
            'name' => 'cracked',
        ]);

        $crackedOnedayStatus = $this->createDummyStatus([
            'name' => 'cracked-oneday',
        ]);

        $data = [
            'attribute' => 'cracks',
            'value' => $crackedStatus->name,
        ];

        $this->getJson(route('games.filters.find', $data))->assertOk()->assertJsonCount(0, 'data');

        $this->createDummyGames(4)->each(function (Game $game) use ($crackedStatus) {
            $game->crack()->save(
                $this->createDummyCrack([
                    'status_id' => $crackedStatus->id,
                ]),
            );
        });

        $this->getJson(route('games.filters.find', $data))->assertOk()->assertJsonCount(4, 'data');

        $this->createDummyGames(4)->each(function (Game $game) use ($crackedOnedayStatus) {
            $game->crack()->save(
                $this->createDummyCrack([
                    'status_id' => $crackedOnedayStatus->id,
                ]),
            );
        });

        $this->getJson(route('games.filters.find', $data))->assertOk()->assertJsonCount(8, 'data');
    }

    /**
     * Test if can find only cracked-oneday for cracked-oneday input filter.
     *
     * @return void
     */
    public function test_if_can_find_only_cracked_oneday_for_cracked_oneday_input_filter(): void
    {
        $crackedStatus = $this->createDummyStatus([
            'name' => 'cracked',
        ]);

        $crackedOnedayStatus = $this->createDummyStatus([
            'name' => 'cracked-oneday',
        ]);

        $data = [
            'attribute' => 'cracks',
            'value' => $crackedOnedayStatus->name,
        ];

        $this->getJson(route('games.filters.find', $data))->assertOk()->assertJsonCount(0, 'data');

        $this->createDummyGames(4)->each(function (Game $game) use ($crackedStatus) {
            $game->crack()->save(
                $this->createDummyCrack([
                    'status_id' => $crackedStatus->id,
                ]),
            );
        });

        $this->getJson(route('games.filters.find', $data))->assertOk()->assertJsonCount(0, 'data');

        $this->createDummyGames(4)->each(function (Game $game) use ($crackedOnedayStatus) {
            $game->crack()->save(
                $this->createDummyCrack([
                    'status_id' => $crackedOnedayStatus->id,
                ]),
            );
        });

        $this->getJson(route('games.filters.find', $data))->assertOk()->assertJsonCount(4, 'data');
    }
}
