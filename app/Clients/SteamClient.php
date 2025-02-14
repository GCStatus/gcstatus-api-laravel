<?php

namespace App\Clients;

use App\Contracts\Clients\{HttpClientInterface, SteamClientInterface};
use App\Contracts\Services\Validation\SteamResponseValidatorInterface;

class SteamClient implements SteamClientInterface
{
    /**
     * The base url.
     *
     * @var string
     */
    private string $baseUrl;

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
        /** @var string $baseUrl */
        $baseUrl = config('services.steam.base_url');

        $this->baseUrl = $baseUrl;

        $this->client = app(HttpClientInterface::class);
        $this->validator = app(SteamResponseValidatorInterface::class);
    }

    /**
     * @inheritDoc
     */

    public function fetchAppDetails(string $appId): array
    {
        /** @var array<int, array<string, false>> $response */
        $response = $this->client->get(
            $this->baseUrl,
            [
                'l' => 'en',
                'cc' => 'us',
                'appids' => $appId,
            ],
        )->json();

        $this->validator->validate($appId, $response);

        return $response[$appId]['data'] ?? []; // @phpstan-ignore-line
    }
}
