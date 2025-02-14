<?php

namespace App\Contracts\Services;

use App\DTO\SteamAppDTO;
use Illuminate\Database\Eloquent\Model;

interface CategoriableServiceInterface extends AbstractServiceInterface
{
    /**
     * Create the steam app categories.
     *
     * @param \Illuminate\Database\Eloquent\Model $app
     * @param \App\DTO\SteamAppDTO $formattedApp
     * @return void
     */
    public function createCategoriesForSteamApp(Model $app, SteamAppDTO $formattedApp): void;
}
