<?php

namespace App\Services;

use App\Models\Game;
use App\DTO\SteamAppDTO;
use Illuminate\Support\Facades\DB;
use App\Contracts\Clients\SteamClientInterface;
use App\Contracts\Services\{
    DlcServiceInterface,
    GameServiceInterface,
    SteamServiceInterface,
    StoreableServiceInterface,
    GenreableServiceInterface,
    GalleriableServiceInterface,
    CategoriableServiceInterface,
    LanguageableServiceInterface,
    DeveloperableServiceInterface,
    PublisherableServiceInterface,
    RequirementableServiceInterface,
};

class SteamService implements SteamServiceInterface
{
    /**
     * The steam client.
     *
     * @var \App\Contracts\Clients\SteamClientInterface
     */
    private SteamClientInterface $client;

    /**
     * The dlc service.
     *
     * @var \App\Contracts\Services\DlcServiceInterface
     */
    private DlcServiceInterface $dlcService;

    /**
     * The game service.
     *
     * @var \App\Contracts\Services\GameServiceInterface
     */
    private GameServiceInterface $gameService;

    /**
     * The genreable service.
     *
     * @var \App\Contracts\Services\GenreableServiceInterface
     */
    private GenreableServiceInterface $genreableService;

    /**
     * The categoriable service.
     *
     * @var \App\Contracts\Services\CategoriableServiceInterface
     */
    private CategoriableServiceInterface $categoriableService;

    /**
     * The storeable service.
     *
     * @var \App\Contracts\Services\StoreableServiceInterface
     */
    private StoreableServiceInterface $storeableService;

    /**
     * The developerable service.
     *
     * @var \App\Contracts\Services\DeveloperableServiceInterface
     */
    private DeveloperableServiceInterface $developerableService;

    /**
     * The publisherable service.
     *
     * @var \App\Contracts\Services\PublisherableServiceInterface
     */
    private PublisherableServiceInterface $publisherableService;

    /**
     * The galleriable service.
     *
     * @var \App\Contracts\Services\GalleriableServiceInterface
     */
    private GalleriableServiceInterface $galleriableService;

    /**
     * The languageable service.
     *
     * @var \App\Contracts\Services\LanguageableServiceInterface
     */
    private LanguageableServiceInterface $languageableService;

    /**
     * The requirementable service.
     *
     * @var \App\Contracts\Services\RequirementableServiceInterface
     */
    private RequirementableServiceInterface $requirementableService;

    /**
     * Create a new class instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->client = app(SteamClientInterface::class);
        $this->dlcService = app(DlcServiceInterface::class);
        $this->gameService = app(GameServiceInterface::class);
        $this->genreableService = app(GenreableServiceInterface::class);
        $this->storeableService = app(StoreableServiceInterface::class);
        $this->galleriableService = app(GalleriableServiceInterface::class);
        $this->categoriableService = app(CategoriableServiceInterface::class);
        $this->languageableService = app(LanguageableServiceInterface::class);
        $this->developerableService = app(DeveloperableServiceInterface::class);
        $this->publisherableService = app(PublisherableServiceInterface::class);
        $this->requirementableService = app(RequirementableServiceInterface::class);
    }

    /**
     * @inheritDoc
     */
    public function saveSteamApp(string $appId): void
    {
        $details = $this->client->fetchAppDetails($appId);

        $formattedApp = SteamAppDTO::validateAndGet($details);

        DB::transaction(function () use ($formattedApp) {
            $game = $this->gameService->create((array)$formattedApp);

            $this->genreableService->createGenresForSteamApp($game, $formattedApp);
            $this->storeableService->createStoreableForSteamApp($game, $formattedApp);
            $this->developerableService->createDevelopersForSteamApp($game, $formattedApp);
            $this->publisherableService->createPublishersForSteamApp($game, $formattedApp);
            $this->categoriableService->createCategoriesForSteamApp($game, $formattedApp);
            $this->galleriableService->createGalleriablesForSteamApp($game, $formattedApp);

            /** @var \App\Models\Game $game */
            $this->requirementableService->createGameRequirements($game, $formattedApp);
            $this->languageableService->createGameLanguageables($game, $formattedApp);
            $this->createSupportInfo($game, $formattedApp);

            $this->saveDLCs($game, $formattedApp);
        });
    }

    /**
     * Save the steam app downloadable content.
     *
     * @param \App\Models\Game $game
     * @param \App\DTO\SteamAppDTO $formattedApp
     * @return void
     */
    private function saveDLCs(Game $game, SteamAppDTO $formattedApp): void
    {
        foreach ($formattedApp->dlc as $dlc) {
            /** @var string $appId */
            $appId = (string)$dlc;

            $dlcDetails = $this->client->fetchAppDetails($appId);

            $formattedDLC = SteamAppDTO::validateAndGet($dlcDetails);

            DB::transaction(function () use ($game, $formattedDLC) {
                $dlc = $this->dlcService->create((array)$formattedDLC + [
                    'game_id' => $game->id,
                ]);

                $this->genreableService->createGenresForSteamApp($dlc, $formattedDLC);
                $this->storeableService->createStoreableForSteamApp($dlc, $formattedDLC);
                $this->developerableService->createDevelopersForSteamApp($dlc, $formattedDLC);
                $this->publisherableService->createPublishersForSteamApp($dlc, $formattedDLC);
                $this->categoriableService->createCategoriesForSteamApp($dlc, $formattedDLC);
                $this->galleriableService->createGalleriablesForSteamApp($dlc, $formattedDLC);
            });
        }
    }

    /**
     * Create the game support info.
     *
     * @param \App\Models\Game $game
     * @param \App\DTO\SteamAppDTO $formattedApp
     * @return void
     */
    private function createSupportInfo(Game $game, SteamAppDTO $formattedApp): void
    {
        $url = isset($formattedApp->support['url']) ? $formattedApp->support['url'] : null;
        $email = isset($formattedApp->support['email']) ? $formattedApp->support['email'] : null;

        $game->support()->create([
            'url' => $url,
            'email' => $email,
        ]);
    }
}
