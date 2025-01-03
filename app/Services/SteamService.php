<?php

namespace App\Services;

use App\DTO\SteamAppDTO;
use App\Contracts\Clients\SteamClientInterface;
use App\Contracts\Services\GalleryServiceInterface;
use App\Contracts\Services\GameServiceInterface;
use App\Contracts\Services\SteamServiceInterface;
use App\Models\Game;

class SteamService implements SteamServiceInterface
{
    /**
     * The steam client.
     *
     * @var \App\Contracts\Clients\SteamClientInterface
     */
    private SteamClientInterface $client;

    /**
     * The game service.
     *
     * @var \App\Contracts\Services\GameServiceInterface
     */
    private GameServiceInterface $gameService;

    /**
     * The gallery service.
     *
     * @var \App\Contracts\Services\GalleryServiceInterface
     */
    private GalleryServiceInterface $galleryService;

    /**
     * Create a new class instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->client = app(SteamClientInterface::class);
        $this->gameService = app(GameServiceInterface::class);
        $this->galleryService = app(GalleryServiceInterface::class);
    }

    /**
     * @inheritDoc
     */
    public function saveSteamApp(string $appId): void
    {
        $details = $this->client->fetchAppDetails($appId);

        $formattedApp = SteamAppDTO::fromArray($details);

        $game = $this->gameService->create((array)$formattedApp);

        $this->createGalleriables($game, $formattedApp);
    }

    /**
     * Create the galleriable for steam app (game).
     *
     * @param \App\Models\Game $game
     * @param \App\DTO\SteamAppDTO $formattedApp
     * @return void
     */
    private function createGalleriables(Game $game, SteamAppDTO $formattedApp): void
    {
        foreach ($formattedApp->galleries as $gallery) {
            $this->galleryService->create([
                'path' => $gallery['path'],
                'galleriable_id' => $game->id,
                'galleriable_type' => $game::class,
                'media_type_id' => $gallery['type'],
            ]);
        }
    }
}
