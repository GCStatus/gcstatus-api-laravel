<?php

namespace Tests\Feature\Http\Admin\Dlc;

use Mockery;
use Exception;
use App\Models\{Dlc, User};
use Illuminate\Support\Carbon;
use Tests\Feature\Http\BaseIntegrationTesting;
use Tests\Traits\{
    HasDummyDlc,
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
    DlcServiceInterface,
};

class DlcUpdateTest extends BaseIntegrationTesting
{
    use HasDummyDlc;
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
     * The updatable dlc.
     *
     * @var \App\Models\Dlc
     */
    private Dlc $dlc;

    /**
     * The required permissions for this action.
     *
     * @var list< string>
     */
    protected array $permissions = [
        'view:dlcs',
        'update:dlcs',
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

        $this->dlc = $this->createDummyDlc();

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
            'free' => fake()->boolean(),
            'legal' => fake()->text(),
            'about' => fake()->realText(),
            'cover' => 'https://placehold.co/600x400/EEE/31343C',
            'release_date' => fake()->date(),
            'description' => fake()->realText(),
            'title' => fake()->title(),
            'short_description' => fake()->text(),
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

        $this->putJson(route('dlcs.update', $this->dlc), $this->getValidPayload())
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

        $this->putJson(route('dlcs.update', $this->dlc), $this->getValidPayload())->assertNotFound();
    }

    /**
     * Test if can update a dlc without payload.
     *
     * @return void
     */
    public function test_if_can_update_a_dlc_without_payload(): void
    {
        $this->putJson(route('dlcs.update', $this->dlc))->assertOk();
    }

    /**
     * Test if can't update a duplicated dlc.
     *
     * @return void
     */
    public function test_if_cant_update_a_duplicated_dlc(): void
    {
        $dlc = $this->createDummyDlc();

        $data = [
            ...$this->getValidPayload(),
            'title' => $dlc->title,
        ];

        $this->putJson(route('dlcs.update', $this->dlc), $data)
            ->assertUnprocessable()
            ->assertInvalid(['title'])
            ->assertSee('The title has already been taken.');
    }

    /**
     * Test if can update for same name if is dlc name owner.
     *
     * @return void
     */
    public function test_if_can_update_for_same_name_if_is_dlc_name_owner(): void
    {
        $data = [
            ...$this->getValidPayload(),
            'title' => $this->dlc->title,
        ];

        $this->putJson(route('dlcs.update', $this->dlc), $data)->assertOk();
    }

    /**
     * Test if can log context on dlc update failure.
     *
     * @return void
     */
    public function test_if_can_log_context_on_dlc_update_failure(): void
    {
        $logServiceMock = Mockery::mock(LogServiceInterface::class);
        $logServiceMock->shouldReceive('withContext')
            ->once()
            ->with(
                Mockery::on(function (string $title) {
                    return $title === 'Failed to update a dlc.';
                }),
                Mockery::on(function (array $context) {
                    return isset($context['code'], $context['message'], $context['trace']);
                }),
            );

        $this->app->instance(LogServiceInterface::class, $logServiceMock);

        $exception = new Exception('Test exception', 500);
        $dlcServiceMock = Mockery::mock(DlcServiceInterface::class);
        $dlcServiceMock->shouldReceive('update')
            ->once()
            ->andThrow($exception);

        $this->app->instance(DlcServiceInterface::class, $dlcServiceMock);

        $this->putJson(route('dlcs.update', $this->dlc), $this->getValidPayload())
            ->assertServerError()
            ->assertSee('Test exception');
    }

    /**
     * Test if can update a dlc with valid payload.
     *
     * @return void
     */
    public function test_if_can_update_a_dlc_with_valid_payload(): void
    {
        $this->putJson(route('dlcs.update', $this->dlc), $this->getValidPayload())->assertOk();
    }

    /**
     * Test if can save the dlc on database correctly.
     *
     * @return void
     */
    public function test_if_can_save_the_dlc_on_database_correctly(): void
    {
        $this->putJson(route('dlcs.update', $this->dlc), $data = $this->getValidPayload())->assertOk();

        /** @var string $releaseDate */
        $releaseDate = $data['release_date'];

        $this->assertDatabaseHas('dlcs', [
            'free' => $data['free'],
            'legal' => $data['legal'],
            'about' => clean($data['about']),
            'cover' => $data['cover'],
            'release_date' => Carbon::parse($releaseDate)->toDateTimeString(),
            'description' => clean($data['description']),
            'title' => $data['title'],
            'short_description' => $data['short_description'],
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

        $this->putJson(route('dlcs.update', $this->dlc), $data)
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

        $this->putJson(route('dlcs.update', $this->dlc), $data)->assertOk();

        foreach ($tags as $tag) {
            $this->assertDatabaseHas('taggables', [
                'tag_id' => $tag,
                'taggable_id' => $this->dlc->id,
                'taggable_type' => $this->dlc::class,
            ]);
        }

        // Case 3: Correctly detached
        $data = [
            'tags' => [],
        ];

        $this->putJson(route('dlcs.update', $this->dlc), $data)->assertOk();

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

        $this->putJson(route('dlcs.update', $this->dlc), $data)
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

        $this->putJson(route('dlcs.update', $this->dlc), $data)->assertOk();

        foreach ($genres as $genre) {
            $this->assertDatabaseHas('genreables', [
                'genre_id' => $genre,
                'genreable_id' => $this->dlc->id,
                'genreable_type' => $this->dlc::class,
            ]);
        }

        // Case 3: Correctly detached
        $data = [
            'genres' => [],
        ];

        $this->putJson(route('dlcs.update', $this->dlc), $data)->assertOk();

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

        $this->putJson(route('dlcs.update', $this->dlc), $data)
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

        $this->putJson(route('dlcs.update', $this->dlc), $data)->assertOk();

        foreach ($categories as $category) {
            $this->assertDatabaseHas('categoriables', [
                'category_id' => $category,
                'categoriable_id' => $this->dlc->id,
                'categoriable_type' => $this->dlc::class,
            ]);
        }

        // Case 3: Correctly detached
        $data = [
            'categories' => [],
        ];

        $this->putJson(route('dlcs.update', $this->dlc), $data)->assertOk();

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

        $this->putJson(route('dlcs.update', $this->dlc), $data)
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

        $this->putJson(route('dlcs.update', $this->dlc), $data)->assertOk();

        foreach ($platforms as $platform) {
            $this->assertDatabaseHas('platformables', [
                'platform_id' => $platform,
                'platformable_id' => $this->dlc->id,
                'platformable_type' => $this->dlc::class,
            ]);
        }

        // Case 3: Correctly detached
        $data = [
            'platforms' => [],
        ];

        $this->putJson(route('dlcs.update', $this->dlc), $data)->assertOk();

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

        $this->putJson(route('dlcs.update', $this->dlc), $data)
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

        $this->putJson(route('dlcs.update', $this->dlc), $data)->assertOk();

        foreach ($publishers as $publisher) {
            $this->assertDatabaseHas('publisherables', [
                'publisher_id' => $publisher,
                'publisherable_id' => $this->dlc->id,
                'publisherable_type' => $this->dlc::class,
            ]);
        }

        // Case 3: Correctly detached
        $data = [
            'publishers' => [],
        ];

        $this->putJson(route('dlcs.update', $this->dlc), $data)->assertOk();

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

        $this->putJson(route('dlcs.update', $this->dlc), $data)
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

        $this->putJson(route('dlcs.update', $this->dlc), $data)->assertOk();

        foreach ($developers as $developer) {
            $this->assertDatabaseHas('developerables', [
                'developer_id' => $developer,
                'developerable_id' => $this->dlc->id,
                'developerable_type' => $this->dlc::class,
            ]);
        }

        // Case 3: Correctly detached
        $data = [
            'developers' => [],
        ];

        $this->putJson(route('dlcs.update', $this->dlc), $data)->assertOk();

        $this->assertDatabaseEmpty('developerables');
    }

    /**
     * Test if can get correct json structure response.
     *
     * @return void
     */
    public function test_if_can_get_correct_json_structure_response(): void
    {
        $this->putJson(route('dlcs.update', $this->dlc), $this->getValidPayload())->assertOk()->assertJsonStructure([
            'data' => [
                'id',
                'slug',
                'free',
                'title',
                'cover',
                'legal',
                'about',
                'description',
                'release_date',
                'short_description',
                'updated_at',
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

        $this->putJson(route('dlcs.update', $this->dlc), $data)->assertOk()->assertJson([
            'data' => [
                'free' => $data['free'],
                'legal' => $data['legal'],
                'about' => clean($data['about']),
                'cover' => $data['cover'],
                'release_date' => Carbon::parse($releaseDate)->toISOString(),
                'description' => clean($data['description']),
                'title' => $data['title'],
                'short_description' => $data['short_description'],
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

        $this->putJson(route('dlcs.update', $this->dlc), $data)->assertOk();

        /** @var string $releaseDate */
        $releaseDate = $data['release_date'];

        $this->assertDatabaseHas('dlcs', [
            'free' => $data['free'],
            'legal' => $data['legal'],
            'about' => '<p style="color:#eb4034;">P field</p>',
            'cover' => $data['cover'],
            'release_date' => Carbon::parse($releaseDate)->toDateTimeString(),
            'description' => '<p style="color:#1e1bc2;">P field</p>',
            'title' => $data['title'],
            'short_description' => $data['short_description'],
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
