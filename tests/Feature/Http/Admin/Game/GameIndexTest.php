<?php

namespace Tests\Feature\Http\Admin\Game;

use Illuminate\Support\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Tests\Feature\Http\BaseIntegrationTesting;
use App\Models\{
    Tag,
    Game,
    User,
    Genre,
    Platform,
    Category,
};
use Tests\Traits\{
    HasDummyTag,
    HasDummyGame,
    HasDummyGenre,
    HasDummyCrack,
    HasDummyCategory,
    HasDummyPlatform,
    HasDummyPermission,
};

class GameIndexTest extends BaseIntegrationTesting
{
    use HasDummyTag;
    use HasDummyGame;
    use HasDummyGenre;
    use HasDummyCrack;
    use HasDummyCategory;
    use HasDummyPlatform;
    use HasDummyPermission;

    /**
     * The dummy user.
     *
     * @var \App\Models\User
     */
    private User $user;

    /**
     * The dummy games.
     *
     * @var \Illuminate\Database\Eloquent\Collection<int, \App\Models\Game>
     */
    private Collection $games;

    /**
     * The required permissions for this action.
     *
     * @var list< string>
     */
    protected array $permissions = [
        'view:games',
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

        $this->games = $this->createDummyGames(4);

        $this->games->each(function (Game $game) {
            $game->tags()->saveMany(
                $this->createDummyTags(2),
            );
            $game->categories()->saveMany(
                $this->createDummyCategories(2),
            );
            $game->genres()->saveMany(
                $this->createDummyGenres(2),
            );
            $game->platforms()->saveMany(
                $this->createDummyPlatforms(2),
            );

            $this->createDummyCrackTo($game);
        });

        $this->removeUnscopedGames();
    }

    /**
     * Test if can't see if is unauthenticated.
     *
     * @return void
     */
    public function test_if_cant_see_if_user_is_unauthenticated(): void
    {
        $this->authService->clearAuthenticationCookies();

        $this->getJson(route('admin.games.index'))
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

        $this->getJson(route('admin.games.index'))->assertNotFound();
    }

    /**
     * Test if can see Games if has permissions.
     *
     * @return void
     */
    public function test_if_can_see_Games_if_has_permissions(): void
    {
        $this->getJson(route('admin.games.index'))->assertOk();
    }

    /**
     * Test if can get correct json attributes count.
     *
     * @return void
     */
    public function test_if_can_get_correct_json_attributes_count(): void
    {
        $this->getJson(route('admin.games.index'))->assertOk()->assertJsonCount(4, 'data');
    }

    /**
     * Test if can get correct json structure.
     *
     * @return void
     */
    public function test_if_can_get_correct_json_structure(): void
    {
        $this->getJson(route('admin.games.index'))->assertOk()->assertJsonStructure([
            'data' => [
                '*' => [
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
                    'crack' => [
                        'id',
                        'cracked_at',
                        'status' => [
                            'id',
                            'name',
                        ],
                        'cracker' => [
                            'id',
                            'name',
                            'slug',
                            'acting',
                        ],
                        'protection' => [
                            'id',
                            'name',
                            'slug',
                        ],
                    ],
                    'tags' => [
                        '*' => [
                            'id',
                            'name',
                            'slug',
                        ],
                    ],
                    'genres' => [
                        '*' => [
                            'id',
                            'name',
                            'slug',
                        ],
                    ],
                    'categories' => [
                        '*' => [
                            'id',
                            'name',
                            'slug',
                        ],
                    ],
                    'platforms' => [
                        '*' => [
                            'id',
                            'name',
                            'slug',
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
        $this->getJson(route('admin.games.index'))->assertOk()->assertJson([
            'data' => $this->games->map(function (Game $game) {
                /** @var \App\Models\Crack $crack */
                $crack = $game->crack;

                /** @var \App\Models\Status $crackStatus */
                $crackStatus = $crack->status;

                /** @var \App\Models\Cracker $cracker */
                $cracker = $crack->cracker;

                /** @var \App\Models\Protection $protection */
                $protection = $crack->protection;

                /** @var \Illuminate\Database\Eloquent\Collection<int, \App\Models\Tag> $tags */
                $tags = $game->tags;

                /** @var \Illuminate\Database\Eloquent\Collection<int, \App\Models\Genre> $genres */
                $genres = $game->genres;

                /** @var \Illuminate\Database\Eloquent\Collection<int, \App\Models\Category> $categories */
                $categories = $game->categories;

                /** @var \Illuminate\Database\Eloquent\Collection<int, \App\Models\Platform> $platforms */
                $platforms = $game->platforms;

                return [
                    'id' => $game->id,
                    'slug' => $game->slug,
                    'title' => $game->title,
                    'cover' => $game->cover,
                    'condition' => $game->condition,
                    'release_date' => Carbon::parse($game->release_date)->toISOString(),
                    'crack' => [
                        'id' => $crack->id,
                        'cracked_at' => Carbon::parse($crack->cracked_at)->toISOString(),
                        'status' => [
                            'id' => $crackStatus->id,
                            'name' => $crackStatus->name,
                        ],
                        'cracker' => [
                            'id' => $cracker->id,
                            'name' => $cracker->name,
                            'slug' => $cracker->slug,
                            'acting' => $cracker->acting,
                        ],
                        'protection' => [
                            'id' => $protection->id,
                            'name' => $protection->name,
                            'slug' => $protection->slug,
                        ],
                    ],
                    'tags' => $tags->map(function (Tag $tag) {
                        return [
                            'id' => $tag->id,
                            'name' => $tag->name,
                            'slug' => $tag->slug,
                        ];
                    })->toArray(),
                    'genres' => $genres->map(function (Genre $genre) {
                        return [
                            'id' => $genre->id,
                            'name' => $genre->name,
                            'slug' => $genre->slug,
                        ];
                    })->toArray(),
                    'categories' => $categories->map(function (Category $category) {
                        return [
                            'id' => $category->id,
                            'name' => $category->name,
                            'slug' => $category->slug,
                        ];
                    })->toArray(),
                    'platforms' => $platforms->map(function (Platform $platform) {
                        return [
                            'id' => $platform->id,
                            'name' => $platform->name,
                            'slug' => $platform->slug,
                        ];
                    })->toArray(),
                ];
            })->toArray(),
        ]);
    }

    /**
     * Remove games that are created with crack.
     *
     * @return void
     */
    private function removeUnscopedGames(): void
    {
        Game::whereNotIn('id', $this->games->pluck('id')->toArray())->delete();
    }
}
