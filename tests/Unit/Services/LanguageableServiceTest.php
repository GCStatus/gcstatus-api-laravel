<?php

namespace Tests\Unit\Services;

use Mockery;
use Tests\TestCase;
use App\DTO\SteamAppDTO;
use Mockery\MockInterface;
use App\Repositories\LanguageableRepository;
use App\Models\{Game, Language, Languageable};
use App\Contracts\Repositories\LanguageableRepositoryInterface;
use App\Exceptions\Admin\Languageable\LanguageableAlreadyExistsException;
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
     * The languageable repository.
     *
     * @var \Mockery\MockInterface
     */
    private MockInterface $languageableRepository;

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
        $this->languageableRepository = Mockery::mock(LanguageableRepositoryInterface::class);

        $this->app->instance(LanguageServiceInterface::class, $this->languageService);
        $this->app->instance(LanguageableRepositoryInterface::class, $this->languageableRepository);

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

        /** @var \App\Models\Language $modelLanguage */
        /** @var \App\Models\Game $model */
        $this->languageableRepository
            ->shouldReceive('create')
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
     * Test if can check if languageable exists for payload.
     *
     * @return void
     */
    public function test_if_can_check_if_languageable_exists_for_payload(): void
    {
        $data = [
            'language_id' => 1,
            'languageable_id' => 1,
            'languageable_type' => Game::class,
        ];

        $this->languageableRepository
            ->shouldReceive('existsForPayload')
            ->once()
            ->with($data)
            ->andReturnFalse();

        $result = $this->languageableService->existsForPayload($data);

        $this->assertFalse($result);

        $this->assertEquals(1, Mockery::getContainer()->mockery_getExpectationCount(), 'Mock expectations met.');
    }

    /**
     * Test if can throw an exception if languageable already exists.
     *
     * @return void
     */
    public function test_if_can_throw_an_exception_if_languageable_already_exists(): void
    {
        $data = [
            'language_id' => 1,
            'languageable_id' => 1,
            'languageable_type' => Game::class,
        ];

        $this->languageableRepository
            ->shouldReceive('existsForPayload')
            ->once()
            ->with($data)
            ->andReturnTrue();

        $this->expectException(LanguageableAlreadyExistsException::class);
        $this->expectExceptionMessage('The given language already exists for this languageable!');

        $this->languageableService->create($data);
    }

    /**
     * Test if can create a new languageable if don't exist yet.
     *
     * @return void
     */
    public function test_if_can_create_a_new_languageable_if_dont_exist_yet(): void
    {
        $languageable = Mockery::mock(Languageable::class);

        $data = [
            'language_id' => 1,
            'languageable_id' => 1,
            'menu' => fake()->boolean(),
            'dubs' => fake()->boolean(),
            'subtitles' => fake()->boolean(),
            'languageable_type' => Game::class,
        ];

        $this->languageableRepository
            ->shouldReceive('existsForPayload')
            ->once()
            ->with($data)
            ->andReturnFalse();

        $this->languageableRepository
            ->shouldReceive('create')
            ->once()
            ->with($data)
            ->andReturn($languageable);

        $result = $this->languageableService->create($data);

        $this->assertEquals($result, $languageable);
        $this->assertInstanceOf(Languageable::class, $result);

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
