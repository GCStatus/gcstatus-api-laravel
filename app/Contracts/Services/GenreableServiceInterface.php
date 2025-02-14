<?php

namespace App\Contracts\Services;

use App\DTO\SteamAppDTO;
use Illuminate\Database\Eloquent\Model;

interface GenreableServiceInterface extends AbstractServiceInterface
{
    /**
     * Create the steam app genres.
     *
     * @param \Illuminate\Database\Eloquent\Model $app
     * @param \App\DTO\SteamAppDTO $formattedApp
     * @return void
     */
    public function createGenresForSteamApp(Model $app, SteamAppDTO $formattedApp): void;
}
