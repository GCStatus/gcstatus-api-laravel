<?php

namespace App\Services;

use App\DTO\SteamAppDTO;
use App\Models\{Game, Languageable};
use App\Contracts\Repositories\LanguageableRepositoryInterface;
use App\Exceptions\Admin\Languageable\LanguageableAlreadyExistsException;
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
     * @inheritDoc
     */
    public function repository(): LanguageableRepositoryInterface
    {
        return app(LanguageableRepositoryInterface::class);
    }

    /**
     * @inheritDoc
     */
    public function create(array $data): Languageable
    {
        $this->assertCanCreate($data);

        /** @var \App\Models\Languageable */
        return $this->repository()->create($data);
    }

    /**
     * @inheritDoc
     */
    public function existsForPayload(array $data): bool
    {
        return $this->repository()->existsForPayload($data);
    }

    /**
     * Assert can create languageable.
     *
     * @param array<string, mixed> $data
     * @throws \App\Exceptions\Admin\Languageable\LanguageableAlreadyExistsException
     * @return void
     */
    private function assertCanCreate(array $data): void
    {
        if ($this->existsForPayload($data)) {
            throw new LanguageableAlreadyExistsException();
        }
    }

    /**
     * @inheritDoc
     */
    public function createGameLanguageables(Game $game, SteamAppDTO $formattedApp): void
    {
        foreach ($formattedApp->languages as $language) {
            /** @var array<string, mixed> $language */
            /** @var \App\Models\Language $modelLanguage */
            $modelLanguage = $this->languageService->firstOrCreate([
                'name' => $language['language'],
            ]);

            $this->repository()->create([
                'dubs' => $language['audio'],
                'languageable_id' => $game->id,
                'languageable_type' => $game::class,
                'language_id' => $modelLanguage->id,
            ]);
        }
    }
}
