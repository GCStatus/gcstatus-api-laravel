<?php

namespace App\Contracts\Services;

use App\DTO\SteamAppDTO;
use Illuminate\Database\Eloquent\Model;

interface StoreableServiceInterface extends AbstractServiceInterface
{
    /**
     * Create the storeable with the price for the steam app.
     *
     * @param \Illuminate\Database\Eloquent\Model $app
     * @param \App\DTO\SteamAppDTO $formattedApp
     * @return void
     */
    public function createStoreableForSteamApp(Model $app, SteamAppDTO $formattedApp): void;
}
