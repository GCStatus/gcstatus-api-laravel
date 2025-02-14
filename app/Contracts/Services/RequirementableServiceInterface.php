<?php

namespace App\Contracts\Services;

use App\DTO\SteamAppDTO;
use App\Models\Game;

interface RequirementableServiceInterface extends AbstractServiceInterface
{
    /**
     * Create the requirements for the game.
     *
     * @param \App\Models\Game $game
     * @param \App\DTO\SteamAppDTO $formattedApp
     * @return void
     */
    public function createGameRequirements(Game $game, SteamAppDTO $formattedApp): void;
}
