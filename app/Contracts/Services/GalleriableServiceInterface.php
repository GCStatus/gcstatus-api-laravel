<?php

namespace App\Contracts\Services;

use App\DTO\SteamAppDTO;
use Illuminate\Database\Eloquent\Model;

interface GalleriableServiceInterface extends AbstractServiceInterface
{
    /**
     * Create the galleriables for steam service.
     *
     * @param \Illuminate\Database\Eloquent\Model $app
     * @param \App\DTO\SteamAppDTO $formattedApp
     * @return void
     */
    public function createGalleriablesForSteamApp(Model $app, SteamAppDTO $formattedApp): void;
}
