<?php

namespace App\Clients;

use App\Contracts\Clients\{HttpClientInterface, SteamClientInterface};
use App\Contracts\Services\Validation\SteamResponseValidatorInterface;

class SteamClient implements SteamClientInterface
{
    /**
     * The http client.
     *
     * @var \App\Contracts\Clients\HttpClientInterface
     */
    private HttpClientInterface $client;

    /**
     * The steam response validator.
     *
     * @var \App\Contracts\Services\Validation\SteamResponseValidatorInterface
     */
    private SteamResponseValidatorInterface $validator;

    /**
     * Create a new class instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->client = app(HttpClientInterface::class);
        $this->validator = app(SteamResponseValidatorInterface::class);
    }

    /**
     * @inheritDoc
     */

    public function fetchAppDetails(string $appId): array
    {
        $response = $this->client->get(
            sprintf('https://store.steampowered.com/api/appdetails?appids=%s&cc=us', $appId)
        )->json();

        $this->validator->validate($appId, $response);

        return $response[$appId]['data'];
    }
}
