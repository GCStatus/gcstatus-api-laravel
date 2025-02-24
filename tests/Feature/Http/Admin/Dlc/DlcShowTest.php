<?php

namespace Tests\Feature\Http\Admin\Dlc;

use Tests\Feature\Http\BaseIntegrationTesting;
use App\Models\{
    Tag,
    Dlc,
    User,
    Genre,
    Category,
    Platform,
    Developer,
    Publisher,
    Storeable,
    Galleriable,
};
use Illuminate\Support\Carbon;
use Tests\Traits\{
    HasDummyTag,
    HasDummyDlc,
    HasDummyGenre,
    HasDummyPlatform,
    HasDummyCategory,
    HasDummyPublisher,
    HasDummyStoreable,
    HasDummyDeveloper,
    HasDummyPermission,
    HasDummyGalleriable,
};

class DlcShowTest extends BaseIntegrationTesting
{
    use HasDummyDlc;
    use HasDummyTag;
    use HasDummyGenre;
    use HasDummyCategory;
    use HasDummyPlatform;
    use HasDummyDeveloper;
    use HasDummyPublisher;
    use HasDummyStoreable;
    use HasDummyPermission;
    use HasDummyGalleriable;

    /**
     * The dummy user.
     *
     * @var \App\Models\User
     */
    private User $user;

    /**
     * The dummy dlc.
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

        $this->bootUserPermissions($this->user);

        $this->dlc = $this->createDummyDlc();

        $this->dlc->tags()->saveMany(
            $this->createDummyTags(2),
        );
        $this->dlc->categories()->saveMany(
            $this->createDummyCategories(2),
        );
        $this->dlc->genres()->saveMany(
            $this->createDummyGenres(2),
        );
        $this->dlc->platforms()->saveMany(
            $this->createDummyPlatforms(2),
        );
        $this->dlc->publishers()->saveMany(
            $this->createDummyPublishers(2),
        );
        $this->dlc->developers()->saveMany(
            $this->createDummyDevelopers(2),
        );
        $this->dlc->stores()->save(
            $this->createDummyStoreable([
                'storeable_id' => $this->dlc->id,
                'storeable_type' => $this->dlc::class,
            ]),
        );
        $this->dlc->galleries()->saveMany(
            $this->createDummyGalleriables(2, [
                'galleriable_id' => $this->dlc->id,
                'galleriable_type' => $this->dlc::class,
            ]),
        );
    }

    /**
     * Test if can't see if is unauthenticated.
     *
     * @return void
     */
    public function test_if_cant_see_if_user_is_unauthenticated(): void
    {
        $this->authService->clearAuthenticationCookies();

        $this->getJson(route('dlcs.show', $this->dlc))
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

        $this->getJson(route('dlcs.show', $this->dlc))->assertNotFound();
    }

    /**
     * Test if can see dlcs if has permissions.
     *
     * @return void
     */
    public function test_if_can_see_dlcs_if_has_permissions(): void
    {
        $this->getJson(route('dlcs.show', $this->dlc))->assertOk();
    }

    /**
     * Test if can get correct json attributes count.
     *
     * @return void
     */
    public function test_if_can_get_correct_json_attributes_count(): void
    {
        $this->getJson(route('dlcs.show', $this->dlc))->assertOk()->assertJsonCount(21, 'data');
    }

    /**
     * Test if can get correct json structure.
     *
     * @return void
     */
    public function test_if_can_get_correct_json_structure(): void
    {
        $this->getJson(route('dlcs.show', $this->dlc))->assertOk()->assertJsonStructure([
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
                'created_at',
                'updated_at',
                'game' => [
                    'id',
                    'age',
                    'slug',
                    'free',
                    'title',
                    'cover',
                    'about',
                    'legal',
                    'website',
                    'views_count',
                    'condition',
                    'description',
                    'release_date',
                    'great_release',
                    'short_description',
                    'hearts_count',
                    'created_at',
                    'updated_at',
                ],
                'tags' => [
                    '*' => [
                        'id',
                        'slug',
                        'name',
                        'created_at',
                        'updated_at',
                    ],
                ],
                'genres' => [
                    '*' => [
                        'id',
                        'slug',
                        'name',
                        'created_at',
                        'updated_at',
                    ],
                ],
                'platforms' => [
                    '*' => [
                        'id',
                        'slug',
                        'name',
                        'created_at',
                        'updated_at',
                    ],
                ],
                'categories' => [
                    '*' => [
                        'id',
                        'slug',
                        'name',
                        'created_at',
                        'updated_at',
                    ],
                ],
                'galleries' => [
                    '*' => [
                        'id',
                        'path',
                        'type' => [
                            'id',
                            'name',
                        ],
                    ],
                ],
                'publishers' => [
                    '*' => [
                        'id',
                        'slug',
                        'name',
                        'acting',
                        'created_at',
                        'updated_at',
                    ],
                ],
                'developers' => [
                    '*' => [
                        'id',
                        'slug',
                        'name',
                        'acting',
                        'created_at',
                        'updated_at',
                    ],
                ],
                'stores' => [
                    '*' => [
                        'id',
                        'url',
                        'price',
                        'store_item_id',
                        'created_at',
                        'updated_at',
                        'store' => [
                            'id',
                            'url',
                            'name',
                            'slug',
                            'logo',
                            'created_at',
                            'updated_at',
                        ],
                    ],
                ],
            ],
        ]);
    }

    /**
     * Test if can get correct json data.
     *
     * @return void
     */
    public function test_if_can_get_correct_json_data(): void
    {
        /** @var \App\Models\Game $game */
        $game = $this->dlc->game;

        /** @var \Illuminate\Database\Eloquent\Collection<int, \App\Models\Tag> $tags */
        $tags = $this->dlc->tags;

        /** @var \Illuminate\Database\Eloquent\Collection<int, \App\Models\Genre> $genres */
        $genres = $this->dlc->genres;

        /** @var \Illuminate\Database\Eloquent\Collection<int, \App\Models\Platform> $platforms */
        $platforms = $this->dlc->platforms;

        /** @var \Illuminate\Database\Eloquent\Collection<int, \App\Models\Category> $categories */
        $categories = $this->dlc->categories;

        /** @var \Illuminate\Database\Eloquent\Collection<int, \App\Models\Galleriable> $galleries */
        $galleries = $this->dlc->galleries;

        /** @var \Illuminate\Database\Eloquent\Collection<int, \App\Models\Publisher> $publishers */
        $publishers = $this->dlc->publishers;

        /** @var \Illuminate\Database\Eloquent\Collection<int, \App\Models\Developer> $developers */
        $developers = $this->dlc->developers;

        /** @var \Illuminate\Database\Eloquent\Collection<int, \App\Models\Storeable> $stores */
        $stores = $this->dlc->stores;

        $this->getJson(route('dlcs.show', $this->dlc))->assertOk()->assertJson([
            'data' => [
                'id' => $this->dlc->id,
                'slug' => $this->dlc->slug,
                'free' => $this->dlc->free,
                'title' => $this->dlc->title,
                'cover' => $this->dlc->cover,
                'legal' => $this->dlc->legal,
                'about' => $this->dlc->about,
                'description' => $this->dlc->description,
                'release_date' => Carbon::parse($this->dlc->release_date)->toISOString(),
                'short_description' => $this->dlc->short_description,
                'created_at' => $this->dlc->created_at?->toISOString(),
                'updated_at' => $this->dlc->updated_at?->toISOString(),
                'game' => [
                    'id' => $game->id,
                    'age' => $game->age,
                    'slug' => $game->slug,
                    'free' => $game->free,
                    'title' => $game->title,
                    'cover' => $game->cover,
                    'about' => $game->about,
                    'legal' => $game->legal,
                    'website' => $game->website,
                    'views_count' => 0,
                    'condition' => $game->condition,
                    'description' => $game->description,
                    'release_date' => Carbon::parse($game->release_date)->toISOString(),
                    'great_release' => $game->great_release,
                    'short_description' => $game->short_description,
                    'hearts_count' => 0,
                    'comments_count' => 0,
                    'created_at' => $game->created_at?->toISOString(),
                    'updated_at' => $game->updated_at?->toISOString(),
                ],
                'tags' => $tags->map(function (Tag $tag) {
                    return [
                        'id' => $tag->id,
                        'slug' => $tag->slug,
                        'name' => $tag->name,
                        'created_at' => $tag->created_at?->toISOString(),
                        'updated_at' => $tag->updated_at?->toISOString(),
                    ];
                })->toArray(),
                'genres' => $genres->map(function (Genre $genre) {
                    return [
                        'id' => $genre->id,
                        'slug' => $genre->slug,
                        'name' => $genre->name,
                        'created_at' => $genre->created_at?->toISOString(),
                        'updated_at' => $genre->updated_at?->toISOString(),
                    ];
                })->toArray(),
                'platforms' => $platforms->map(function (Platform $platform) {
                    return [
                        'id' => $platform->id,
                        'slug' => $platform->slug,
                        'name' => $platform->name,
                        'created_at' => $platform->created_at?->toISOString(),
                        'updated_at' => $platform->updated_at?->toISOString(),
                    ];
                })->toArray(),
                'categories' => $categories->map(function (Category $category) {
                    return [
                        'id' => $category->id,
                        'slug' => $category->slug,
                        'name' => $category->name,
                        'created_at' => $category->created_at?->toISOString(),
                        'updated_at' => $category->updated_at?->toISOString(),
                    ];
                })->toArray(),
                'galleries' => $galleries->map(function (Galleriable $galleriable) {
                    /** @var \App\Models\MediaType $mediaType */
                    $mediaType = $galleriable->mediaType;

                    return [
                        'id' => $galleriable->id,
                        'path' => $galleriable->path,
                        'type' => [
                            'id' => $mediaType->id,
                            'name' => $mediaType->name,
                        ],
                    ];
                })->toArray(),
                'publishers' => $publishers->map(function (Publisher $publisher) {
                    return [
                        'id' => $publisher->id,
                        'slug' => $publisher->slug,
                        'name' => $publisher->name,
                        'acting' => $publisher->acting,
                        'created_at' => $publisher->created_at?->toISOString(),
                        'updated_at' => $publisher->updated_at?->toISOString(),
                    ];
                })->toArray(),
                'developers' => $developers->map(function (Developer $developer) {
                    return [
                        'id' => $developer->id,
                        'slug' => $developer->slug,
                        'name' => $developer->name,
                        'acting' => $developer->acting,
                        'created_at' => $developer->created_at?->toISOString(),
                        'updated_at' => $developer->updated_at?->toISOString(),
                    ];
                })->toArray(),
                'stores' => $stores->map(function (Storeable $storeable) {
                    /** @var \App\Models\Store $store */
                    $store = $storeable->store;

                    return [
                        'id' => $storeable->id,
                        'url' => $storeable->url,
                        'price' => $storeable->price,
                        'store_item_id' => $storeable->store_item_id,
                        'created_at' => $storeable->created_at?->toISOString(),
                        'updated_at' => $storeable->updated_at?->toISOString(),
                        'store' => [
                            'id' => $store->id,
                            'url' => $store->url,
                            'name' => $store->name,
                            'slug' => $store->slug,
                            'logo' => $store->logo,
                            'created_at' => $store->created_at?->toISOString(),
                            'updated_at' => $store->updated_at?->toISOString(),
                        ],
                    ];
                })->toArray(),
            ],
        ]);
    }
}
