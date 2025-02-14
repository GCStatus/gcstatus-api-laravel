<?php

namespace App\Contracts\Services;

use App\DTO\SteamAppDTO;
use Illuminate\Database\Eloquent\Model;

interface DeveloperableServiceInterface extends AbstractServiceInterface
{
    /**
     * Create the steam app developers.
     *
     * @param \Illuminate\Database\Eloquent\Model $app
     * @param \App\DTO\SteamAppDTO $formattedApp
     * @return void
     */
    public function createDevelopersForSteamApp(Model $app, SteamAppDTO $formattedApp): void;
}
