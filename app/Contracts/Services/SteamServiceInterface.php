<?php

namespace App\Contracts\Services;

interface SteamServiceInterface
{
    /**
     * Sync and create steam app data on database.
     *
     * @param string $appId
     * @return void
     */
    public function saveSteamApp(string $appId): void;
}
