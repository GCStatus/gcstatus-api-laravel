<?php

namespace App\Contracts\Services;

use App\DTO\SteamAppDTO;
use Illuminate\Database\Eloquent\Model;

interface PublisherableServiceInterface extends AbstractServiceInterface
{
    /**
     * Create the steam app publishers.
     *
     * @param \Illuminate\Database\Eloquent\Model $app
     * @param \App\DTO\SteamAppDTO $formattedApp
     * @return void
     */
    public function createPublishersForSteamApp(Model $app, SteamAppDTO $formattedApp): void;
}
