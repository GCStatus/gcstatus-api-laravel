<?php

namespace Tests\Feature\Http\Admin\Steam;

use Illuminate\Support\Str;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Bus;
use App\Jobs\Steam\CreateSteamAppByIdJob;
use Tests\Feature\Http\BaseIntegrationTesting;
use Database\Seeders\{RoleSeeder, StoreSeeder};
use App\Contracts\Clients\SteamClientInterface;
use App\Models\{Game, MediaType, Role, Store, User};

class CreateSteamAppByIdTest extends BaseIntegrationTesting
{
    /**
     * The dummy user.
     *
     * @var \App\Models\User
     */
    private User $user;

    /**
     * The steam fake response.
     *
     * @var array<string, mixed>
     */
    private array $fakeResponse;

    /**
     * Setup new test environments.
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->seed([RoleSeeder::class, StoreSeeder::class]);

        $this->user = $this->actingAsDummyUser();

        $this->user->roles()->attach(Role::TECHNOLOGY_ROLE_ID);

        $this->fakeResponse = [
            'steam_appid' => 123,
            'type' => 'game',
            'name' => 'Test Game',
            'required_age' => '0',
            'is_free' => false,
            'controller_support' => 'full',
            'dlc' => [],
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

        $fakeSteamClient = new class ($this->fakeResponse) implements SteamClientInterface {
            /**
             * The fake response data.
             *
             * @var array<string, mixed>
             */
            protected array $fakeResponse;

            /**
             * Create a new client instance.
             *
             * @param array<string, mixed> $fakeResponse
             * @return void
             */
            public function __construct(array $fakeResponse)
            {
                $this->fakeResponse = $fakeResponse;
            }

            /**
             * Mock the steam client fetch method.
             *
             * @param string $appId
             * @return array<string, mixed>
             */
            public function fetchAppDetails(string $appId): array
            {
                return $this->fakeResponse;
            }
        };

        $this->app->instance(SteamClientInterface::class, $fakeSteamClient);

        config(['queue.default' => 'sync']);
    }

    /**
     * Test if can't act if is unauthenticated.
     *
     * @return void
     */
    public function test_if_cant_act_if_user_is_unauthenticated(): void
    {
        $this->authService->clearAuthenticationCookies();

        $postData = ['app_id' => '123'];

        $this->postJson(route('steam.apps.create'), $postData)
            ->assertUnauthorized()
            ->assertSee('We could not authenticate your user. Please, try to login again.');
    }

    /**
     * Test if create steam app is queued and returns correct response.
     *
     * @return void
     */
    public function test_if_create_steam_app_is_queued_and_returns_correct_response(): void
    {
        Bus::fake();

        $postData = ['app_id' => '123'];

        $this->postJson(route('steam.apps.create'), $postData)->assertOk()->assertJson([
            'data' => [
                'message' => 'Steam App successfully added to queue and is running on background.',
            ],
        ]);

        Bus::assertDispatched(CreateSteamAppByIdJob::class, function (CreateSteamAppByIdJob $job) use ($postData) {
            return $job->appId === $postData['app_id'];
        });
    }

    /**
     * Test if can't duplicate app id on database.
     *
     * @return void
     */
    public function test_if_cant_duplicate_app_id_on_database(): void
    {
        $postData = ['app_id' => '123'];

        $this->postJson(route('steam.apps.create'), $postData)->assertOk()->assertJson([
            'data' => [
                'message' => 'Steam App successfully added to queue and is running on background.',
            ],
        ]);

        $this->postJson(route('steam.apps.create'), $postData)
            ->assertUnprocessable()
            ->assertSee('The given app id is already stored on database.');
    }

    /**
     * Test if can save correctly the steam data on database.
     *
     * @return void
     */
    public function test_if_can_save_correctly_the_steam_data_on_database(): void
    {
        $this->assertDatabaseEmpty('games');

        $postData = ['app_id' => '123'];

        $this->postJson(route('steam.apps.create'), $postData)->assertOk()->assertJson([
            'data' => [
                'message' => 'Steam App successfully added to queue and is running on background.',
            ],
        ]);

        /** @var string $name */
        $name = $this->fakeResponse['name'];

        /** @var array<string, string> $release_date */
        $release_date = $this->fakeResponse['release_date'];

        $this->assertDatabaseHas('games', [
            'condition' => 'common',
            'slug' => Str::slug($name),
            'title' => $this->fakeResponse['name'],
            'free' => $this->fakeResponse['is_free'],
            'website' => $this->fakeResponse['website'],
            'age' => $this->fakeResponse['required_age'],
            'legal' => $this->fakeResponse['legal_notice'],
            'cover' => $this->fakeResponse['background_raw'],
            'about' => $this->fakeResponse['about_the_game'],
            'description' => $this->fakeResponse['detailed_description'],
            'short_description' => $this->fakeResponse['short_description'],
            'release_date' => Carbon::createFromFormat('d M, Y', $release_date['date'])?->startOfDay()?->toDateTimeString(),
        ]);

        /** @var array<int, array<string, mixed>> $genres */
        $genres = $this->fakeResponse['genres'];

        $this->assertDatabaseHas('genres', [
            'name' => $genres[0]['description'],
        ]);

        $this->assertDatabaseHas('genreables', [
            'genre_id' => 1,
            'genreable_id' => 1,
            'genreable_type' => Game::class,
        ]);

        /** @var array<int, array<string, mixed>> $categories */
        $categories = $this->fakeResponse['categories'];

        $this->assertDatabaseHas('categories', [
            'name' => $categories[0]['description'],
        ]);

        $this->assertDatabaseHas('categoriables', [
            'category_id' => 1,
            'categoriable_id' => 1,
            'categoriable_type' => Game::class,
        ]);

        /** @var array<int, string> $developers */
        $developers = $this->fakeResponse['developers'];

        $this->assertDatabaseHas('developers', [
            'name' => $developers[0],
        ]);

        $this->assertDatabaseHas('developerables', [
            'developer_id' => 1,
            'developerable_id' => 1,
            'developerable_type' => Game::class,
        ]);

        /** @var array<int, string> $publishers */
        $publishers = $this->fakeResponse['publishers'];

        $this->assertDatabaseHas('publishers', [
            'name' => $publishers[0],
        ]);

        $this->assertDatabaseHas('publisherables', [
            'publisher_id' => 1,
            'publisherable_id' => 1,
            'publisherable_type' => Game::class,
        ]);

        /** @var array<string, mixed> $overview */
        $overview = $this->fakeResponse['price_overview'];

        $this->assertDatabaseHas('storeables', [
            'storeable_id' => 1,
            'price' => $overview['final'],
            'storeable_type' => Game::class,
            'store_id' => Store::STEAM_STORE_ID,
            'store_item_id' => $postData['app_id'],
            'url' => sprintf('https://store.steampowered.com/app/%s', $postData['app_id']),
        ]);

        /** @var array<int, array<string, mixed>> $screenshots */
        $screenshots = $this->fakeResponse['screenshots'];

        /** @var array<int, array<string, array<string, string>>> $movies */
        $movies = $this->fakeResponse['movies'];

        $this->assertDatabaseHas('galleriables', [
            's3' => false,
            'galleriable_id' => 1,
            'galleriable_type' => Game::class,
            'media_type_id' => MediaType::PHOTO_CONST_ID,
            'path' => $screenshots[0]['path_full'],
        ])->assertDatabaseHas('galleriables', [
            's3' => false,
            'galleriable_id' => 1,
            'galleriable_type' => Game::class,
            'media_type_id' => MediaType::VIDEO_CONST_ID,
            'path' => $movies[0]['webm']['max'],
        ]);

        $this->assertDatabaseHas('requirementables', [
            'os' => 'Windows 10 64-bit',
            'dx' => 'Version 12',
            'cpu' => 'Intel i5-4670k or AMD Ryzen 3 1200',
            'gpu' => 'NVIDIA GTX 1060 (6GB) or AMD RX 5500 XT (8GB) or Intel Arc A750',
            'ram' => '8 GB RAM',
            'rom' => '190 GB available space',
            'obs' => 'Windows version 2004 2020-05-27 19041. 6GB GPU is required',
            'network' => 'N/A',
            'requirementable_id' => 1,
            'requirement_type_id' => 1,
            'requirementable_type' => Game::class,
        ])->assertDatabaseHas('requirementables', [
            'os' => 'Windows 10 64-bit',
            'dx' => 'Version 12',
            'cpu' => 'Intel i5-8600 or AMD Ryzen 5 3600',
            'gpu' => 'NVIDIA RTX 2060 Super or AMD RX 5700 or Intel Arc A770',
            'ram' => '16 GB RAM',
            'rom' => '190 GB available space',
            'obs' => 'Windows version 2004 2020-05-27 19041. 6GB GPU is required',
            'network' => 'N/A',
            'requirementable_id' => 1,
            'requirement_type_id' => 2,
            'requirementable_type' => Game::class,
        ]);

        $this->assertDatabaseHas('requirement_types', [
            'os' => 'windows',
            'potential' => 'minimum',
        ])->assertDatabaseHas('requirement_types', [
            'os' => 'windows',
            'potential' => 'recommended',
        ]);

        $this->assertDatabaseHas('languages', [
            'name' => 'English',
            'slug' => 'english',
        ])->assertDatabaseHas('languages', [
            'name' => 'French',
            'slug' => 'french',
        ]);

        $this->assertDatabaseHas('languageables', [
            'menu' => true,
            'dubs' => true,
            'language_id' => 1,
            'subtitles' => true,
            'languageable_id' => 1,
            'languageable_type' => Game::class,
        ])->assertDatabaseHas('languageables', [
            'menu' => true,
            'dubs' => false,
            'language_id' => 2,
            'subtitles' => true,
            'languageable_id' => 1,
            'languageable_type' => Game::class,
        ]);

        /** @var array<string, array<int, string>> $support */
        $support = $this->fakeResponse['support_info'];

        $this->assertDatabaseHas('game_supports', [
            'game_id' => 1,
            'url' => $support['url'],
            'email' => $support['email'],
        ]);
    }
}
