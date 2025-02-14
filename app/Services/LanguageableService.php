<?php

namespace App\Services;

use App\Models\Game;
use App\DTO\SteamAppDTO;
use App\Contracts\Repositories\LanguageableRepositoryInterface;
use App\Contracts\Services\{
    LanguageServiceInterface,
    LanguageableServiceInterface,
};

class LanguageableService extends AbstractService implements LanguageableServiceInterface
{
    /**
     * The language service.
     *
     * @var \App\Contracts\Services\LanguageServiceInterface
     */
    private LanguageServiceInterface $languageService;

    /**
     * Create a new service instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->languageService = app(LanguageServiceInterface::class);
    }

    /**
     * The game repository.
     *
     * @return \App\Contracts\Repositories\LanguageableRepositoryInterface
     */
    public function repository(): LanguageableRepositoryInterface
    {
        return app(LanguageableRepositoryInterface::class);
    }

    /**
     * Create the game languageables.
     *
     * @param \App\Models\Game $game
     * @param \App\DTO\SteamAppDTO $formattedApp
     * @return void
     */
    public function createGameLanguageables(Game $game, SteamAppDTO $formattedApp): void
    {
        foreach ($formattedApp->languages as $language) {
            /** @var array<string, mixed> $language */
            /** @var \App\Models\Language $modelLanguage */
            $modelLanguage = $this->languageService->firstOrCreate([
                'name' => $language['language'],
            ]);

            $this->create([
                'dubs' => $language['audio'],
                'languageable_id' => $game->id,
                'languageable_type' => $game::class,
                'language_id' => $modelLanguage->id,
            ]);
        }
    }
}
