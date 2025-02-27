<?php

namespace App\Contracts\Services;

use App\Models\Game;
use App\DTO\SteamAppDTO;

interface LanguageableServiceInterface extends AbstractServiceInterface
{
    /**
     * Create the game languageables.
     *
     * @param \App\Models\Game $game
     * @param \App\DTO\SteamAppDTO $formattedApp
     * @return void
     */
    public function createGameLanguageables(Game $game, SteamAppDTO $formattedApp): void;

    /**
     * Check if exists for payload.
     *
     * @param array<string, mixed> $data
     * @return bool
     */
    public function existsForPayload(array $data): bool;
}
