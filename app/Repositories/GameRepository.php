<?php

namespace App\Repositories;

use App\Models\Game;
use App\Contracts\Repositories\GameRepositoryInterface;

class GameRepository extends AbstractRepository implements GameRepositoryInterface
{
    /**
     * The game model.
     *
     * @return \App\Models\Game
     */
    public function model(): Game
    {
        return new Game();
    }
}
