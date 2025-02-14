<?php

namespace App\Contracts\Clients;

interface SteamClientInterface
{
    /**
     * Fetch a steam app details.
     *
     * @param string $appId
     * @return array<string, mixed>
     */
    public function fetchAppDetails(string $appId): array;
}
