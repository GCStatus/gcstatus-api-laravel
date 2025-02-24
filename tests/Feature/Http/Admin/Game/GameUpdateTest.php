<?php

namespace Tests\Feature\Http\Admin\Game;

use Mockery;
use Exception;
use App\Models\{Game, User};
use Illuminate\Support\Carbon;
use Tests\Feature\Http\BaseIntegrationTesting;
use Tests\Traits\{
    HasDummyGame,
    HasDummyTag,
    HasDummyGenre,
    HasDummyPlatform,
    HasDummyCategory,
    HasDummyPublisher,
    HasDummyDeveloper,
    HasDummyPermission,
};
use App\Contracts\Services\{
    LogServiceInterface,
    GameServiceInterface,
};

class GameUpdateTest extends BaseIntegrationTesting
{
    use HasDummyGame;
    use HasDummyTag;
    use HasDummyGenre;
    use HasDummyCategory;
    use HasDummyPlatform;
    use HasDummyPublisher;
    use HasDummyDeveloper;
    use HasDummyPermission;

    /**
     * The dummy user.
     *
     * @var \App\Models\User
     */
    private User $user;

    /**
     * The updatable game.
     *
     * @var \App\Models\Game
     */
    private Game $game;

    /**
     * The required permissions for this action.
     *
     * @var list< string>
     */
    protected array $permissions = [
        'view:games',
        'update:games',
    ];

    /**
     * Setup new test environments.
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->user = $this->actingAsDummyUser();

        $this->game = $this->createDummyGame();

        $this->bootUserPermissions($this->user);
    }

    /**
     * Get a valid payload.
     *
     * @return array<string, mixed>
     */
    private function getValidPayload(): array
    {
        return [
            'legal' => fake()->text(),
            'title' => fake()->title(),
            'free' => fake()->boolean(),
            'about' => fake()->realText(),
            'release_date' => fake()->date(),
            'website' => 'https://google.com',
            'description' => fake()->realText(),
            'great_release' => fake()->boolean(),
            'short_description' => fake()->text(),
            'age' => fake()->numberBetween(0, 18),
            'cover' => 'https://placehold.co/600x400/EEE/31343C',
            'condition' => fake()->randomElement(['common', 'hot', 'popular', 'unreleased', 'sale']),
        ];
    }

    /**
     * Test if can't see if is unauthenticated.
     *
     * @return void
     */
    public function test_if_cant_see_if_user_is_unauthenticated(): void
    {
        $this->authService->clearAuthenticationCookies();

        $this->putJson(route('admin.games.update', $this->game), $this->getValidPayload())
            ->assertUnauthorized()
            ->assertSee('We could not authenticate your user. Please, try to login again.');
    }

    /**
     * Test if can't see if hasn't permissions.
     *
     * @return void
     */
    public function test_if_cant_see_if_hasnt_permissions(): void
    {
        $this->user->permissions()->detach();

        $this->putJson(route('admin.games.update', $this->game), $this->getValidPayload())->assertNotFound();
    }

    /**
     * Test if can update a game without payload.
     *
     * @return void
     */
    public function test_if_can_update_a_game_without_payload(): void
    {
        $this->putJson(route('admin.games.update', $this->game))->assertOk();
    }

    /**
     * Test if can't update a duplicated game.
     *
     * @return void
     */
    public function test_if_cant_update_a_duplicated_game(): void
    {
        $game = $this->createDummyGame();

        $data = [
            ...$this->getValidPayload(),
            'title' => $game->title,
        ];

        $this->putJson(route('admin.games.update', $this->game), $data)
            ->assertUnprocessable()
            ->assertInvalid(['title'])
            ->assertSee('The title has already been taken.');
    }

    /**
     * Test if can update for same name if is game name owner.
     *
     * @return void
     */
    public function test_if_can_update_for_same_name_if_is_game_name_owner(): void
    {
        $data = [
            ...$this->getValidPayload(),
            'title' => $this->game->title,
        ];

        $this->putJson(route('admin.games.update', $this->game), $data)->assertOk();
    }

    /**
     * Test if can log context on game update failure.
     *
     * @return void
     */
    public function test_if_can_log_context_on_game_update_failure(): void
    {
        $logServiceMock = Mockery::mock(LogServiceInterface::class);
        $logServiceMock->shouldReceive('withContext')
            ->once()
            ->with(
                Mockery::on(function (string $title) {
                    return $title === 'Failed to update a game.';
                }),
                Mockery::on(function (array $context) {
                    return isset($context['code'], $context['message'], $context['trace']);
                }),
            );

        $this->app->instance(LogServiceInterface::class, $logServiceMock);

        $exception = new Exception('Test exception', 500);
        $gameServiceMock = Mockery::mock(GameServiceInterface::class);
        $gameServiceMock->shouldReceive('update')
            ->once()
            ->andThrow($exception);

        $this->app->instance(GameServiceInterface::class, $gameServiceMock);

        $this->putJson(route('admin.games.update', $this->game), $this->getValidPayload())
            ->assertServerError()
            ->assertSee('Test exception');
    }

    /**
     * Test if can update a game with valid payload.
     *
     * @return void
     */
    public function test_if_can_update_a_game_with_valid_payload(): void
    {
        $this->putJson(route('admin.games.update', $this->game), $this->getValidPayload())->assertOk();
    }

    /**
     * Test if can save the game on database correctly.
     *
     * @return void
     */
    public function test_if_can_save_the_game_on_database_correctly(): void
    {
        $this->putJson(route('admin.games.update', $this->game), $data = $this->getValidPayload())->assertOk();

        /** @var string $releaseDate */
        $releaseDate = $data['release_date'];

        $this->assertDatabaseHas('Games', [
            'free' => $data['free'],
            'legal' => $data['legal'],
            'about' => clean($data['about']),
            'cover' => $data['cover'],
            'release_date' => Carbon::parse($releaseDate)->toDateTimeString(),
            'description' => clean($data['description']),
            'title' => $data['title'],
            'short_description' => $data['short_description'],
            'age' => $data['age'],
            'website' => $data['website'],
            'condition' => $data['condition'],
            'great_release' => $data['great_release'],
        ]);
    }

    /**
     * Test if can correctly sync tags on database.
     *
     * @return void
     */
    public function test_if_can_correctly_sync_tags_on_database(): void
    {
        // Case 1: Invalid tags
        $data = [
            'tags' => [1, 2, 3],
        ];

        $this->putJson(route('admin.games.update', $this->game), $data)
            ->assertUnprocessable()
            ->assertSee('The selected tags.0 is invalid. (and 2 more errors)');

        // Case 2: Correctly saved
        $data = [
            'tags' => $tags = [
                $this->createDummyTag()->id,
                $this->createDummyTag()->id,
                $this->createDummyTag()->id,
            ],
        ];

        $this->putJson(route('admin.games.update', $this->game), $data)->assertOk();

        foreach ($tags as $tag) {
            $this->assertDatabaseHas('taggables', [
                'tag_id' => $tag,
                'taggable_id' => $this->game->id,
                'taggable_type' => $this->game::class,
            ]);
        }

        // Case 3: Correctly detached
        $data = [
            'tags' => [],
        ];

        $this->putJson(route('admin.games.update', $this->game), $data)->assertOk();

        $this->assertDatabaseEmpty('taggables');
    }

    /**
     * Test if can correctly sync genres on database.
     *
     * @return void
     */
    public function test_if_can_correctly_sync_genres_on_database(): void
    {
        // Case 1: Invalid genres
        $data = [
            'genres' => [1, 2, 3],
        ];

        $this->putJson(route('admin.games.update', $this->game), $data)
            ->assertUnprocessable()
            ->assertSee('The selected genres.0 is invalid. (and 2 more errors)');

        // Case 2: Correctly saved
        $data = [
            'genres' => $genres = [
                $this->createDummyGenre()->id,
                $this->createDummyGenre()->id,
                $this->createDummyGenre()->id,
            ],
        ];

        $this->putJson(route('admin.games.update', $this->game), $data)->assertOk();

        foreach ($genres as $genre) {
            $this->assertDatabaseHas('genreables', [
                'genre_id' => $genre,
                'genreable_id' => $this->game->id,
                'genreable_type' => $this->game::class,
            ]);
        }

        // Case 3: Correctly detached
        $data = [
            'genres' => [],
        ];

        $this->putJson(route('admin.games.update', $this->game), $data)->assertOk();

        $this->assertDatabaseEmpty('genreables');
    }

    /**
     * Test if can correctly sync categories on database.
     *
     * @return void
     */
    public function test_if_can_correctly_sync_categories_on_database(): void
    {
        // Case 1: Invalid categories
        $data = [
            'categories' => [1, 2, 3],
        ];

        $this->putJson(route('admin.games.update', $this->game), $data)
            ->assertUnprocessable()
            ->assertSee('The selected categories.0 is invalid. (and 2 more errors)');

        // Case 2: Correctly saved
        $data = [
            'categories' => $categories = [
                $this->createDummyCategory()->id,
                $this->createDummyCategory()->id,
                $this->createDummyCategory()->id,
            ],
        ];

        $this->putJson(route('admin.games.update', $this->game), $data)->assertOk();

        foreach ($categories as $category) {
            $this->assertDatabaseHas('categoriables', [
                'category_id' => $category,
                'categoriable_id' => $this->game->id,
                'categoriable_type' => $this->game::class,
            ]);
        }

        // Case 3: Correctly detached
        $data = [
            'categories' => [],
        ];

        $this->putJson(route('admin.games.update', $this->game), $data)->assertOk();

        $this->assertDatabaseEmpty('categoriables');
    }

    /**
     * Test if can correctly sync platforms on database.
     *
     * @return void
     */
    public function test_if_can_correctly_sync_platforms_on_database(): void
    {
        // Case 1: Invalid platforms
        $data = [
            'platforms' => [1, 2, 3],
        ];

        $this->putJson(route('admin.games.update', $this->game), $data)
            ->assertUnprocessable()
            ->assertSee('The selected platforms.0 is invalid. (and 2 more errors)');

        // Case 2: Correctly saved
        $data = [
            'platforms' => $platforms = [
                $this->createDummyPlatform()->id,
                $this->createDummyPlatform()->id,
                $this->createDummyPlatform()->id,
            ],
        ];

        $this->putJson(route('admin.games.update', $this->game), $data)->assertOk();

        foreach ($platforms as $platform) {
            $this->assertDatabaseHas('platformables', [
                'platform_id' => $platform,
                'platformable_id' => $this->game->id,
                'platformable_type' => $this->game::class,
            ]);
        }

        // Case 3: Correctly detached
        $data = [
            'platforms' => [],
        ];

        $this->putJson(route('admin.games.update', $this->game), $data)->assertOk();

        $this->assertDatabaseEmpty('platformables');
    }

    /**
     * Test if can correctly sync publishers on database.
     *
     * @return void
     */
    public function test_if_can_correctly_sync_publishers_on_database(): void
    {
        // Case 1: Invalid publishers
        $data = [
            'publishers' => [1, 2, 3],
        ];

        $this->putJson(route('admin.games.update', $this->game), $data)
            ->assertUnprocessable()
            ->assertSee('The selected publishers.0 is invalid. (and 2 more errors)');

        // Case 2: Correctly saved
        $data = [
            'publishers' => $publishers = [
                $this->createDummyPublisher()->id,
                $this->createDummyPublisher()->id,
                $this->createDummyPublisher()->id,
            ],
        ];

        $this->putJson(route('admin.games.update', $this->game), $data)->assertOk();

        foreach ($publishers as $publisher) {
            $this->assertDatabaseHas('publisherables', [
                'publisher_id' => $publisher,
                'publisherable_id' => $this->game->id,
                'publisherable_type' => $this->game::class,
            ]);
        }

        // Case 3: Correctly detached
        $data = [
            'publishers' => [],
        ];

        $this->putJson(route('admin.games.update', $this->game), $data)->assertOk();

        $this->assertDatabaseEmpty('publisherables');
    }

    /**
     * Test if can correctly sync developers on database.
     *
     * @return void
     */
    public function test_if_can_correctly_sync_developers_on_database(): void
    {
        // Case 1: Invalid developers
        $data = [
            'developers' => [1, 2, 3],
        ];

        $this->putJson(route('admin.games.update', $this->game), $data)
            ->assertUnprocessable()
            ->assertSee('The selected developers.0 is invalid. (and 2 more errors)');

        // Case 2: Correctly saved
        $data = [
            'developers' => $developers = [
                $this->createDummyDeveloper()->id,
                $this->createDummyDeveloper()->id,
                $this->createDummyDeveloper()->id,
            ],
        ];

        $this->putJson(route('admin.games.update', $this->game), $data)->assertOk();

        foreach ($developers as $developer) {
            $this->assertDatabaseHas('developerables', [
                'developer_id' => $developer,
                'developerable_id' => $this->game->id,
                'developerable_type' => $this->game::class,
            ]);
        }

        // Case 3: Correctly detached
        $data = [
            'developers' => [],
        ];

        $this->putJson(route('admin.games.update', $this->game), $data)->assertOk();

        $this->assertDatabaseEmpty('developerables');
    }

    /**
     * Test if can get correct json structure response.
     *
     * @return void
     */
    public function test_if_can_get_correct_json_structure_response(): void
    {
        $this->putJson(route('admin.games.update', $this->game), $this->getValidPayload())->assertOk()->assertJsonStructure([
            'data' => [
                'id',
                'age',
                'slug',
                'free',
                'title',
                'cover',
                'about',
                'legal',
                'website',
                'condition',
                'description',
                'release_date',
                'great_release',
                'short_description',
                'views_count',
                'hearts_count',
                'created_at',
                'updated_at',
            ],
        ]);
    }

    /**
     * Test if can get correct json structure data.
     *
     * @return void
     */
    public function test_if_can_get_correct_json_structure_data(): void
    {
        $data = $this->getValidPayload();

        /** @var string $releaseDate */
        $releaseDate = $data['release_date'];

        $this->putJson(route('admin.games.update', $this->game), $data)->assertOk()->assertJson([
            'data' => [
                'free' => $data['free'],
                'legal' => $data['legal'],
                'about' => clean($data['about']),
                'cover' => $data['cover'],
                'release_date' => Carbon::parse($releaseDate)->toISOString(),
                'description' => clean($data['description']),
                'title' => $data['title'],
                'short_description' => $data['short_description'],
                'age' => $data['age'],
                'website' => $data['website'],
                'condition' => $data['condition'],
                'great_release' => $data['great_release'],
            ],
        ]);
    }

    /**
     * Test if can sanitize about and description fields.
     *
     * @return void
     */
    public function test_if_can_sanitize_about_and_description_fields(): void
    {
        $data = [
            ...$this->getValidPayload(),
            'about' => '<script>alert("hacked!")</script><p style="color:#eb4034;">P field</p>',
            'description' => '<p style="color:#1e1bc2;">P field</p><script>alert("hacked!")</script>',
        ];

        $this->putJson(route('admin.games.update', $this->game), $data)->assertOk();

        /** @var string $releaseDate */
        $releaseDate = $data['release_date'];

        $this->assertDatabaseHas('Games', [
            'free' => $data['free'],
            'legal' => $data['legal'],
            'about' => '<p style="color:#eb4034;">P field</p>',
            'cover' => $data['cover'],
            'release_date' => Carbon::parse($releaseDate)->toDateTimeString(),
            'description' => '<p style="color:#1e1bc2;">P field</p>',
            'title' => $data['title'],
            'short_description' => $data['short_description'],
            'age' => $data['age'],
            'website' => $data['website'],
            'condition' => $data['condition'],
            'great_release' => $data['great_release'],
        ]);
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
