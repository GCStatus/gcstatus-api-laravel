<?php

namespace Tests\Unit\Services;

use Mockery;
use Tests\TestCase;
use App\DTO\SteamAppDTO;
use Mockery\MockInterface;
use App\Models\{Game, Language, Languageable};
use App\Repositories\LanguageableRepository;
use App\Contracts\Repositories\LanguageableRepositoryInterface;
use App\Contracts\Services\{
    LanguageServiceInterface,
    LanguageableServiceInterface,
};

class LanguageableServiceTest extends TestCase
{
    /**
     * The language service.
     *
     * @var \Mockery\MockInterface
     */
    private MockInterface $languageService;

    /**
     * The languageable service.
     *
     * @var \App\Contracts\Services\LanguageableServiceInterface
     */
    private LanguageableServiceInterface $languageableService;

    /**
     * Setup new test environments.
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->languageService = Mockery::mock(LanguageServiceInterface::class);

        $this->app->instance(LanguageServiceInterface::class, $this->languageService);

        $this->languageableService = app(LanguageableServiceInterface::class);
    }

    /**
     * Test if LanguageableService uses the Languageable repository correctly.
     *
     * @return void
     */
    public function test_Languageable_repository_uses_Languageable_repository(): void
    {
        $this->app->instance(LanguageableRepositoryInterface::class, new LanguageableRepository());

        /** @var \App\Services\LanguageableService $languageableService */
        $languageableService = app(LanguageableServiceInterface::class);

        $this->assertInstanceOf(LanguageableRepository::class, $languageableService->repository());
    }

    /**
     * Test if can create languageables for steam app.
     *
     * @return void
     */
    public function test_if_can_create_languageables_for_steam_app(): void
    {
        $model = Mockery::mock(Game::class);
        $model->shouldReceive('getAttribute')->with('id')->andReturn(1);

        $modelLanguage = Mockery::mock(Language::class);
        $modelLanguage->shouldReceive('getAttribute')->with('id')->andReturn(1);

        $dto = Mockery::mock(SteamAppDTO::class);
        $dto->languages = [ // @phpstan-ignore-line
            $language = [
                'audio' => true,
                'language' => 'English',
            ],
        ];

        $this->languageService
            ->shouldReceive('firstOrCreate')
            ->once()
            ->with(['name' => $language['language']])
            ->andReturn($modelLanguage);

        $repository = Mockery::mock(LanguageableRepositoryInterface::class);
        $this->app->instance(LanguageableRepositoryInterface::class, $repository);

        /** @var \App\Models\Language $modelLanguage */
        /** @var \App\Models\Game $model */
        $repository->shouldReceive('create')
            ->once()
            ->with([
                'dubs' => $language['audio'],
                'languageable_id' => $model->id,
                'language_id' => $modelLanguage->id,
                'languageable_type' => $model::class,
            ])->andReturn(Mockery::mock(Languageable::class));

        /** @var \App\DTO\SteamAppDTO $dto */
        $this->languageableService->createGameLanguageables($model, $dto);

        $this->assertEquals(2, Mockery::getContainer()->mockery_getExpectationCount(), 'Mock expectations met.');
    }


    /**
     * Tear down application tests.
     *
     * @return void
     */
    public function tearDown(): void
    {
        Mockery::close();

        parent::tearDown();
    }
}
