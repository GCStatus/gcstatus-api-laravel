<?php

namespace Tests\Unit\Services;

use Mockery;
use Tests\TestCase;
use App\DTO\SteamAppDTO;
use Mockery\MockInterface;
use App\Models\{Dlc, Game};
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
use App\Contracts\Clients\SteamClientInterface;
use Illuminate\Database\Eloquent\Relations\HasOne;
use App\Contracts\Services\{
    DlcServiceInterface,
    GameServiceInterface,
    SteamServiceInterface,
    GenreableServiceInterface,
    StoreableServiceInterface,
    GalleriableServiceInterface,
    LanguageableServiceInterface,
    CategoriableServiceInterface,
    DeveloperableServiceInterface,
    PublisherableServiceInterface,
    RequirementableServiceInterface,
};

class SteamServiceTest extends TestCase
{
    /**
     * The steam service.
     *
     * @var \App\Contracts\Services\SteamServiceInterface
     */
    private SteamServiceInterface $steamService;

    /**
     * The steam client.
     *
     * @var \Mockery\MockInterface
     */
    private MockInterface $client;

    /**
     * The dlc service.
     *
     * @var \Mockery\MockInterface
     */
    private MockInterface $dlcService;

    /**
     * The game service.
     *
     * @var \Mockery\MockInterface
     */
    private MockInterface $gameService;

    /**
     * The genreable service.
     *
     * @var \Mockery\MockInterface
     */
    private MockInterface $genreableService;

    /**
     * The storeable service.
     *
     * @var \Mockery\MockInterface
     */
    private MockInterface $storeableService;

    /**
     * The developerable service.
     *
     * @var \Mockery\MockInterface
     */
    private MockInterface $developerableService;

    /**
     * The publisherable service.
     *
     * @var \Mockery\MockInterface
     */
    private MockInterface $publisherableService;

    /**
     * The categoriable service.
     *
     * @var \Mockery\MockInterface
     */
    private MockInterface $categoriableService;

    /**
     * The galleriable service.
     *
     * @var \Mockery\MockInterface
     */
    private MockInterface $galleriableService;

    /**
     * The languageable service.
     *
     * @var \Mockery\MockInterface
     */
    private MockInterface $languageableService;

    /**
     * The requirementable service.
     *
     * @var \Mockery\MockInterface
     */
    private MockInterface $requirementableService;

    /**
     * Setup a new test environments.
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->client = Mockery::mock(SteamClientInterface::class);
        $this->dlcService = Mockery::mock(DlcServiceInterface::class);
        $this->gameService = Mockery::mock(GameServiceInterface::class);
        $this->genreableService = Mockery::mock(GenreableServiceInterface::class);
        $this->storeableService = Mockery::mock(StoreableServiceInterface::class);
        $this->galleriableService = Mockery::mock(GalleriableServiceInterface::class);
        $this->categoriableService = Mockery::mock(CategoriableServiceInterface::class);
        $this->languageableService = Mockery::mock(LanguageableServiceInterface::class);
        $this->developerableService = Mockery::mock(DeveloperableServiceInterface::class);
        $this->publisherableService = Mockery::mock(PublisherableServiceInterface::class);
        $this->requirementableService = Mockery::mock(RequirementableServiceInterface::class);

        $this->app->instance(SteamClientInterface::class, $this->client);
        $this->app->instance(DlcServiceInterface::class, $this->dlcService);
        $this->app->instance(GameServiceInterface::class, $this->gameService);
        $this->app->instance(GenreableServiceInterface::class, $this->genreableService);
        $this->app->instance(StoreableServiceInterface::class, $this->storeableService);
        $this->app->instance(DeveloperableServiceInterface::class, $this->developerableService);
        $this->app->instance(PublisherableServiceInterface::class, $this->publisherableService);
        $this->app->instance(CategoriableServiceInterface::class, $this->categoriableService);
        $this->app->instance(GalleriableServiceInterface::class, $this->galleriableService);
        $this->app->instance(LanguageableServiceInterface::class, $this->languageableService);
        $this->app->instance(RequirementableServiceInterface::class, $this->requirementableService);

        DB::shouldReceive('transaction')->andReturnUsing(function (callable $closure) {
            return $closure();
        });

        $this->steamService = app(SteamServiceInterface::class);
    }

    public function test_saveSteamApp_calls_dependencies_and_creates_game(): void
    {
        $appId = '123';

        $fakeDetails = [
            'steam_appid' => 123,
            'type' => 'game',
            'name' => 'Test Game',
            'required_age' => '0',
            'is_free' => false,
            'controller_support' => 'full',
            'dlc' => [
                111,
                222,
            ],
            'detailed_description' => 'Detailed description of the game.',
            'about_the_game' => 'About the game.',
            'short_description' => 'Short description.',
            'release_date' => ['coming_soon' => false, 'date' => '12 Jan, 2020'],
            'background_raw' => 'http://example.com/image.jpg',
            'categories' => [
                [
                    'id' => 1,
                    'description' => 'Single-player',
                ],
            ],
            'genres' => [
                [
                    'id' => '1',
                    'description' => 'Action'
                ],
            ],
            'developers' => ['Test Developer'],
            'publishers' => ['Test Publisher'],
            'price_overview' => ['final' => 1999, 'discount_percent' => 0],
            'legal_notice' => 'Legal Notice',
            'supported_languages' => 'English<strong>*</strong>, French',
            'website' => 'http://example.com',
            'support_info' => ['url' => 'http://example.com/support', 'email' => 'support@example.com'],
            'recommendations' => ['total' => 50000],
            'pc_requirements' => [
                'minimum' => '<strong>Minimum:</strong><br><ul class=\'bb_ul\'><li>Requires a 64-bit processor and operating system<br></li><li><strong>OS:</strong> Windows 10 64-bit<br></li><li><strong>Processor:</strong> Intel i5-4670k or AMD Ryzen 3 1200<br></li><li><strong>Memory:</strong> 8 GB RAM<br></li><li><strong>Graphics:</strong> NVIDIA GTX 1060 (6GB) or AMD RX 5500 XT (8GB) or Intel Arc A750<br></li><li><strong>DirectX:</strong> Version 12<br></li><li><strong>Storage:</strong> 190 GB available space<br></li><li><strong>Additional Notes:</strong> Windows version 2004 2020-05-27 19041. 6GB GPU is required</li></ul>',
                'recommended' => '<strong>Recommended:</strong><br><ul class=\'bb_ul\'><li>Requires a 64-bit processor and operating system<br></li><li><strong>OS:</strong> Windows 10 64-bit<br></li><li><strong>Processor:</strong> Intel i5-8600 or AMD Ryzen 5 3600<br></li><li><strong>Memory:</strong> 16 GB RAM<br></li><li><strong>Graphics:</strong> NVIDIA RTX 2060 Super or AMD RX 5700 or Intel Arc A770<br></li><li><strong>DirectX:</strong> Version 12<br></li><li><strong>Storage:</strong> 190 GB available space<br></li><li><strong>Additional Notes:</strong> Windows version 2004 2020-05-27 19041. 6GB GPU is required</li></ul>'
            ],
            'mac_requirements' => [
                'minimum' => '<strong>Minimum:</strong><br><ul class=\'bb_ul\'></ul>',
                'recommended' => '<strong>Recommended:</strong><br><ul class=\'bb_ul\'></ul>'
            ],
            'linux_requirements' => [
                'minimum' => '<strong>Minimum:</strong><br><ul class=\'bb_ul\'></ul>',
                'recommended' => '<strong>Recommended:</strong><br><ul class=\'bb_ul\'></ul>',
            ],
            'screenshots' => [
                [
                    'id' => 0,
                    'path_thumbnail' => 'https://shared.akamai.steamstatic.com/store_item_assets/steam/apps/2322010/ss_7c59382e67eadf779e0e15c3837ee91158237f11.600x338.jpg?t=1738256985',
                    'path_full' => 'https://shared.akamai.steamstatic.com/store_item_assets/steam/apps/2322010/ss_7c59382e67eadf779e0e15c3837ee91158237f11.1920x1080.jpg?t=1738256985',
                ],
            ],
            'movies' => [
                [
                    'id' => 257054534,
                    'name' => 'Launch Trailer (US-EN)',
                    'thumbnail' => 'https://shared.akamai.steamstatic.com/store_item_assets/steam/apps/257054534/movie.293x165.jpg?t=1726759092',
                    'webm' => [
                        '480' => 'http://video.akamai.steamstatic.com/store_trailers/257054534/movie480_vp9.webm?t=1726759092',
                        'max' => 'http://video.akamai.steamstatic.com/store_trailers/257054534/movie_max_vp9.webm?t=1726759092',
                    ],
                    'mp4' => [
                        '480' => 'http://video.akamai.steamstatic.com/store_trailers/257054534/movie480.mp4?t=1726759092',
                        'max' => 'http://video.akamai.steamstatic.com/store_trailers/257054534/movie_max.mp4?t=1726759092',
                    ],
                    'highlight' => true,
                ],
            ],
            'metacritic' => [
                'score' => 90,
                'url' => 'https://www.metacritic.com/game/pc/god-of-war-ragnarok?ftag=MCD-06-10aaa1f'
            ],
        ];

        $formattedApp = SteamAppDTO::validateAndGet($fakeDetails);

        $this->client
            ->shouldReceive('fetchAppDetails')
            ->once()
            ->with($appId)
            ->andReturn($fakeDetails);

        $gameMock = Mockery::mock(Game::class);
        $gameMock->shouldReceive('getAttribute')->with('id')->andReturn(1);

        $this->gameService
            ->shouldReceive('create')
            ->once()
            ->with((array)$formattedApp)
            ->andReturn($gameMock);

        $this->genreableService
            ->shouldReceive('createGenresForSteamApp')
            ->once()
            ->with($gameMock, Mockery::type(SteamAppDTO::class));

        $this->storeableService
            ->shouldReceive('createStoreableForSteamApp')
            ->once()
            ->with($gameMock, Mockery::type(SteamAppDTO::class));

        $this->developerableService
            ->shouldReceive('createDevelopersForSteamApp')
            ->once()
            ->with($gameMock, Mockery::type(SteamAppDTO::class));

        $this->publisherableService
            ->shouldReceive('createPublishersForSteamApp')
            ->once()
            ->with($gameMock, Mockery::type(SteamAppDTO::class));

        $this->categoriableService
            ->shouldReceive('createCategoriesForSteamApp')
            ->once()
            ->with($gameMock, Mockery::type(SteamAppDTO::class));

        $this->galleriableService
            ->shouldReceive('createGalleriablesForSteamApp')
            ->once()
            ->with($gameMock, Mockery::type(SteamAppDTO::class));

        $this->requirementableService
            ->shouldReceive('createGameRequirements')
            ->once()
            ->with($gameMock, Mockery::type(SteamAppDTO::class));

        $this->languageableService
            ->shouldReceive('createGameLanguageables')
            ->once()
            ->with($gameMock, Mockery::type(SteamAppDTO::class));

        $supportMock = Mockery::mock(HasOne::class);
        $gameMock->shouldReceive('support')->once()->andReturn($supportMock);
        $supportMock->shouldReceive('create')
            ->once()
            ->with([
                'url' => 'http://example.com/support',
                'email' => 'support@example.com',
            ]);

        foreach ($formattedApp->dlc as $dlcId) {
            $mockDlc = Mockery::mock(Dlc::class);

            $dlcFakeDetails = $fakeDetails;

            $this->client
                ->shouldReceive('fetchAppDetails')
                ->once()
                ->with($dlcId)
                ->andReturn($dlcFakeDetails);

            $formattedApp = SteamAppDTO::validateAndGet($dlcFakeDetails);

            /** @var \App\Models\Game $gameMock */
            $this->dlcService
                ->shouldReceive('create')
                ->once()
                ->with((array)$formattedApp + [
                    'game_id' => $gameMock->id,
                ])->andReturn($mockDlc);

            $this->genreableService->shouldReceive('createGenresForSteamApp')
                ->once()
                ->with(Mockery::type(Model::class), Mockery::type(SteamAppDTO::class));

            $this->storeableService->shouldReceive('createStoreableForSteamApp')
                ->once()
                ->with(Mockery::type(Model::class), Mockery::type(SteamAppDTO::class));

            $this->developerableService
                ->shouldReceive('createDevelopersForSteamApp')
                ->once()
                ->with(Mockery::type(Model::class), Mockery::type(SteamAppDTO::class));

            $this->publisherableService
                ->shouldReceive('createPublishersForSteamApp')
                ->once()
                ->with(Mockery::type(Model::class), Mockery::type(SteamAppDTO::class));

            $this->categoriableService
                ->shouldReceive('createCategoriesForSteamApp')
                ->once()
                ->with(Mockery::type(Model::class), Mockery::type(SteamAppDTO::class));

            $this->galleriableService
                ->shouldReceive('createGalleriablesForSteamApp')
                ->once()
                ->with(Mockery::type(Model::class), Mockery::type(SteamAppDTO::class));
        }

        $this->steamService->saveSteamApp($appId);

        $this->assertEquals(28, Mockery::getContainer()->mockery_getExpectationCount(), 'Mock expectations met.');
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
