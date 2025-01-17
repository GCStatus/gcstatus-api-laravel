<?php

namespace Tests\Feature\Http\Home;

use Illuminate\Support\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Tests\Feature\Http\BaseIntegrationTesting;
use App\Models\{
    Tag,
    Game,
    Crack,
    Genre,
    Banner,
    Status,
    Cracker,
    Platform,
    Category,
    Protection,
};
use Tests\Traits\{
    HasDummyTag,
    HasDummyGame,
    HasDummyCrack,
    HasDummyGenre,
    HasDummyBanner,
    HasDummyPlatform,
    HasDummyCategory,
    HasDummyHeartable,
};

class HomeJsonTest extends BaseIntegrationTesting
{
    use HasDummyTag;
    use HasDummyGame;
    use HasDummyCrack;
    use HasDummyGenre;
    use HasDummyBanner;
    use HasDummyCategory;
    use HasDummyPlatform;
    use HasDummyHeartable;

    /**
     * Test if can get correct empty json when no games are created.
     *
     * @return void
     */
    public function test_if_can_get_correct_empty_json_when_no_games_are_created(): void
    {
        $this->getJson(route('home'))->assertOk()->assertJson([
            'data' => [
                'hot' => [],
                'popular' => [],
                'banners' => [],
                'upcoming' => [],
                'most_liked' => [],
                'next_release' => null,
            ]
        ]);
    }

    /**
     * Test if can get correct upcoming games json structure.
     *
     * @return void
     */
    public function test_if_can_get_correct_upcoming_games_json_structure(): void
    {
        $game = $this->createDummyGame([
            'release_date' => Carbon::today()->addWeek(),
        ]);

        $game->tags()->save(
            $tag = $this->createDummyTag(),
        );

        $game->genres()->save(
            $genre = $this->createDummyGenre(),
        );

        $game->categories()->save(
            $category = $this->createDummyCategory(),
        );

        $game->platforms()->save(
            $platform = $this->createDummyPlatform(),
        );

        $crack = $this->createDummyCrackTo($game);

        $this->removeCreatedGameOnCrack($game);

        $games = Collection::make([$game]);
        $tags = Collection::make([$tag]);
        $genres = Collection::make([$genre]);
        $categories = Collection::make([$category]);
        $platforms = Collection::make([$platform]);

        /** @var \App\Models\Status $status */
        $status = $crack->status;

        /** @var \App\Models\Cracker $cracker */
        $cracker = $crack->cracker;

        /** @var \App\Models\Protection $protection */
        $protection = $crack->protection;

        $this->getJson(route('home'))->assertOk()->assertJson([
            'data' => [
                'upcoming' => $this->getDataStructure(
                    $games,
                    $crack,
                    $status,
                    $cracker,
                    $protection,
                    $tags,
                    $genres,
                    $categories,
                    $platforms,
                ),
            ],
        ]);
    }

    /**
     * Test if can get correct hot games json structure.
     *
     * @return void
     */
    public function test_if_can_get_correct_hot_games_json_structure(): void
    {
        $game = $this->createDummyGame([
            'condition' => Game::HOT_CONDITION,
        ]);

        $game->tags()->save(
            $tag = $this->createDummyTag(),
        );

        $game->genres()->save(
            $genre = $this->createDummyGenre(),
        );

        $game->categories()->save(
            $category = $this->createDummyCategory(),
        );

        $game->platforms()->save(
            $platform = $this->createDummyPlatform(),
        );

        $crack = $this->createDummyCrackTo($game);

        $this->removeCreatedGameOnCrack($game);

        $games = Collection::make([$game]);
        $tags = Collection::make([$tag]);
        $genres = Collection::make([$genre]);
        $categories = Collection::make([$category]);
        $platforms = Collection::make([$platform]);

        /** @var \App\Models\Status $status */
        $status = $crack->status;

        /** @var \App\Models\Cracker $cracker */
        $cracker = $crack->cracker;

        /** @var \App\Models\Protection $protection */
        $protection = $crack->protection;

        $this->getJson(route('home'))->assertOk()->assertJson([
            'data' => [
                'hot' => $this->getDataStructure(
                    $games,
                    $crack,
                    $status,
                    $cracker,
                    $protection,
                    $tags,
                    $genres,
                    $categories,
                    $platforms,
                ),
            ],
        ]);
    }

    /**
     * Test if can get correct popular games json structure.
     *
     * @return void
     */
    public function test_if_can_get_correct_popular_games_json_structure(): void
    {
        $game = $this->createDummyGame([
            'condition' => Game::POPULAR_CONDITION,
        ]);

        $game->tags()->save(
            $tag = $this->createDummyTag(),
        );

        $game->genres()->save(
            $genre = $this->createDummyGenre(),
        );

        $game->categories()->save(
            $category = $this->createDummyCategory(),
        );

        $game->platforms()->save(
            $platform = $this->createDummyPlatform(),
        );

        $crack = $this->createDummyCrackTo($game);

        $this->removeCreatedGameOnCrack($game);

        $games = Collection::make([$game]);
        $tags = Collection::make([$tag]);
        $genres = Collection::make([$genre]);
        $categories = Collection::make([$category]);
        $platforms = Collection::make([$platform]);

        /** @var \App\Models\Status $status */
        $status = $crack->status;

        /** @var \App\Models\Cracker $cracker */
        $cracker = $crack->cracker;

        /** @var \App\Models\Protection $protection */
        $protection = $crack->protection;

        $this->getJson(route('home'))->assertOk()->assertJson([
            'data' => [
                'popular' => $this->getDataStructure(
                    $games,
                    $crack,
                    $status,
                    $cracker,
                    $protection,
                    $tags,
                    $genres,
                    $categories,
                    $platforms,
                ),
            ],
        ]);
    }

    /**
     * Test if can get correct most liked games json structure.
     *
     * @return void
     */
    public function test_if_can_get_correct_most_liked_games_json_structure(): void
    {
        $game = $this->createDummyGame([
            'release_date' => Carbon::today()->addWeek(),
        ]);

        $game->tags()->save(
            $tag = $this->createDummyTag(),
        );

        $game->genres()->save(
            $genre = $this->createDummyGenre(),
        );

        $game->categories()->save(
            $category = $this->createDummyCategory(),
        );

        $game->platforms()->save(
            $platform = $this->createDummyPlatform(),
        );

        $crack = $this->createDummyCrackTo($game);

        $this->removeCreatedGameOnCrack($game);

        $games = Collection::make([$game]);
        $tags = Collection::make([$tag]);
        $genres = Collection::make([$genre]);
        $categories = Collection::make([$category]);
        $platforms = Collection::make([$platform]);

        /** @var \App\Models\Status $status */
        $status = $crack->status;

        /** @var \App\Models\Cracker $cracker */
        $cracker = $crack->cracker;

        /** @var \App\Models\Protection $protection */
        $protection = $crack->protection;

        $this->getJson(route('home'))->assertOk()->assertJson([
            'data' => [
                'upcoming' => $this->getDataStructure(
                    $games,
                    $crack,
                    $status,
                    $cracker,
                    $protection,
                    $tags,
                    $genres,
                    $categories,
                    $platforms,
                ),
            ],
        ]);
    }

    /**
     * Test if can get correct json structure for next game release.
     *
     * @return void
     */
    public function test_if_can_get_correct_json_structure_for_next_game_release(): void
    {
        $game = $this->createDummyGame([
            'great_release' => true,
            'release_date' => Carbon::today()->addWeek(),
        ]);

        $game->tags()->save(
            $tag = $this->createDummyTag(),
        );

        $game->genres()->save(
            $genre = $this->createDummyGenre(),
        );

        $game->categories()->save(
            $category = $this->createDummyCategory(),
        );

        $game->platforms()->save(
            $platform = $this->createDummyPlatform(),
        );

        $crack = $this->createDummyCrackTo($game);

        $this->removeCreatedGameOnCrack($game);

        $tags = Collection::make([$tag]);
        $genres = Collection::make([$genre]);
        $categories = Collection::make([$category]);
        $platforms = Collection::make([$platform]);

        /** @var \App\Models\Status $status */
        $status = $crack->status;

        /** @var \App\Models\Cracker $cracker */
        $cracker = $crack->cracker;

        /** @var \App\Models\Protection $protection */
        $protection = $crack->protection;

        $this->getJson(route('home'))->assertOk()->assertJson([
            'data' => [
                'next_release' => [
                    'id' => $game->id,
                    'age' => $game->age,
                    'slug' => $game->slug,
                    'free' => $game->free,
                    'title' => $game->title,
                    'cover' => $game->cover,
                    'about' => $game->about,
                    'legal' => $game->legal,
                    'website' => $game->website,
                    'condition' => $game->condition,
                    'description' => $game->description,
                    'release_date' => Carbon::parse($game->release_date)->toISOString(),
                    'great_release' => $game->great_release,
                    'is_hearted' => false,
                    'short_description' => $game->short_description,
                    'views_count' => 0,
                    'hearts_count' => 0,
                    'created_at' => $game->created_at?->toISOString(),
                    'updated_at' => $game->updated_at?->toISOString(),
                    'crack' => [
                        'id' => $crack->id,
                        'cracked_at' => Carbon::parse($crack->cracked_at)->toISOString(),
                        'status' => [
                            'id' => $status->id,
                            'name' => $status->name,
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
                        ];
                    })->toArray(),
                    'genres' => $genres->map(function (Genre $genre) {
                        return [
                            'id' => $genre->id,
                            'name' => $genre->name,
                        ];
                    })->toArray(),
                    'categories' => $categories->map(function (Category $category) {
                        return [
                            'id' => $category->id,
                            'name' => $category->name,
                        ];
                    })->toArray(),
                    'platforms' => $platforms->map(function (Platform $platform) {
                        return [
                            'id' => $platform->id,
                            'name' => $platform->name,
                        ];
                    })->toArray(),
                ],
            ],
        ]);
    }

    /**
     * Test if can get correct json structure for banners.
     *
     * @return void
     */
    public function test_if_can_get_correct_json_structure_for_banners(): void
    {
        $game = $this->createDummyGame([
            'great_release' => true,
            'release_date' => Carbon::today()->addWeek(),
        ]);

        $game->tags()->save(
            $tag = $this->createDummyTag(),
        );

        $game->genres()->save(
            $genre = $this->createDummyGenre(),
        );

        $game->categories()->save(
            $category = $this->createDummyCategory(),
        );

        $game->platforms()->save(
            $platform = $this->createDummyPlatform(),
        );

        $crack = $this->createDummyCrackTo($game);

        $this->removeCreatedGameOnCrack($game);

        $games = Collection::make([$game]);
        $tags = Collection::make([$tag]);
        $genres = Collection::make([$genre]);
        $categories = Collection::make([$category]);
        $platforms = Collection::make([$platform]);

        /** @var \App\Models\Status $status */
        $status = $crack->status;

        /** @var \App\Models\Cracker $cracker */
        $cracker = $crack->cracker;

        /** @var \App\Models\Protection $protection */
        $protection = $crack->protection;

        $banner = $this->createDummyBanner([
            'bannerable_id' => $game->id,
            'bannerable_type' => $game::class,
            'component' => Banner::HOME_HEADER_CAROUSEL_BANNERS,
        ]);

        $banners = Collection::make([$banner]);

        $this->getJson(route('home'))->assertOk()->assertJson([
            'data' => [
                'banners' => $banners->map(function (Banner $banner) use (
                    $game,
                    $crack,
                    $status,
                    $cracker,
                    $protection,
                    $tags,
                    $genres,
                    $categories,
                    $platforms,
                ) {
                    return [
                        'id' => $banner->id,
                        'type' => $banner->bannerable_type,
                        'bannerable' => [
                            'id' => $game->id,
                            'age' => $game->age,
                            'slug' => $game->slug,
                            'free' => $game->free,
                            'title' => $game->title,
                            'cover' => $game->cover,
                            'about' => $game->about,
                            'legal' => $game->legal,
                            'website' => $game->website,
                            'condition' => $game->condition,
                            'description' => $game->description,
                            'release_date' => Carbon::parse($game->release_date)->toISOString(),
                            'great_release' => $game->great_release,
                            'is_hearted' => false,
                            'short_description' => $game->short_description,
                            'views_count' => 0,
                            'hearts_count' => 0,
                            'created_at' => $game->created_at?->toISOString(),
                            'updated_at' => $game->updated_at?->toISOString(),
                            'crack' => [
                                'id' => $crack->id,
                                'cracked_at' => Carbon::parse($crack->cracked_at)->toISOString(),
                                'status' => [
                                    'id' => $status->id,
                                    'name' => $status->name,
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
                                ];
                            })->toArray(),
                            'genres' => $genres->map(function (Genre $genre) {
                                return [
                                    'id' => $genre->id,
                                    'name' => $genre->name,
                                ];
                            })->toArray(),
                            'categories' => $categories->map(function (Category $category) {
                                return [
                                    'id' => $category->id,
                                    'name' => $category->name,
                                ];
                            })->toArray(),
                            'platforms' => $platforms->map(function (Platform $platform) {
                                return [
                                    'id' => $platform->id,
                                    'name' => $platform->name,
                                ];
                            })->toArray(),
                        ],
                    ];
                })->toArray(),
            ],
        ]);
    }

    /**
     * Test if can get correct is hearted attribute based on user's heart.
     *
     * @return void
     */
    public function test_if_can_get_correct_is_hearted_attribute_based_on_users_heart(): void
    {
        $user = $this->actingAsDummyUser();

        $game = $this->createDummyGame([
            'condition' => Game::HOT_CONDITION,
        ]);

        $this->getJson(route('home'))->assertJson([
            'data' => [
                'hot' => [
                    [
                        'is_hearted' => false,
                    ],
                ],
            ],
        ]);

        $this->createDummyHeartable([
            'heartable_id' => $game->id,
            'heartable_type' => $game::class,
        ]);

        $this->getJson(route('home'))->assertJson([
            'data' => [
                'hot' => [
                    [
                        'is_hearted' => false,
                    ],
                ],
            ],
        ]);

        $this->createDummyHeartable([
            'user_id' => $user->id,
            'heartable_id' => $game->id,
            'heartable_type' => $game::class,
        ]);

        $this->getJson(route('home'))->assertJson([
            'data' => [
                'hot' => [
                    [
                        'is_hearted' => true,
                    ],
                ],
            ],
        ]);
    }

    /**
     * Remove game created with crack creation (factory).
     *
     * @param \App\Models\Game $game
     * @return void
     */
    private function removeCreatedGameOnCrack(Game $game): void
    {
        Game::where('id', '!=', $game->id)->delete();
    }

    /**
     * Get game or collection has given data structure.
     *
     * @param \Illuminate\Database\Eloquent\Collection<int, \App\Models\Game> $games
     * @param \App\Models\Crack $crack
     * @param \App\Models\Status $status
     * @param \App\Models\Cracker $cracker
     * @param \App\Models\Protection $protection
     * @param \Illuminate\Database\Eloquent\Collection<int, \App\Models\Tag> $tags
     * @param \Illuminate\Database\Eloquent\Collection<int, \App\Models\Genre> $genres
     * @param \Illuminate\Database\Eloquent\Collection<int, \App\Models\Category> $categories
     * @param \Illuminate\Database\Eloquent\Collection<int, \App\Models\Platform> $platforms
     * @return array<string, mixed>
     */
    private function getDataStructure(
        Collection $games,
        Crack $crack,
        Status $status,
        Cracker $cracker,
        Protection $protection,
        Collection $tags,
        Collection $genres,
        Collection $categories,
        Collection $platforms,
    ): array {
        return $games->map(function (Game $game) use (
            $crack,
            $status,
            $cracker,
            $protection,
            $tags,
            $genres,
            $categories,
            $platforms,
        ) {
            return [
                'id' => $game->id,
                'age' => $game->age,
                'slug' => $game->slug,
                'free' => $game->free,
                'title' => $game->title,
                'cover' => $game->cover,
                'about' => $game->about,
                'legal' => $game->legal,
                'website' => $game->website,
                'condition' => $game->condition,
                'description' => $game->description,
                'release_date' => Carbon::parse($game->release_date)->toISOString(),
                'great_release' => $game->great_release,
                'is_hearted' => false,
                'short_description' => $game->short_description,
                'views_count' => 0,
                'hearts_count' => 0,
                'created_at' => $game->created_at?->toISOString(),
                'updated_at' => $game->updated_at?->toISOString(),
                'crack' => [
                    'id' => $crack->id,
                    'cracked_at' => Carbon::parse($crack->cracked_at)->toISOString(),
                    'status' => [
                        'id' => $status->id,
                        'name' => $status->name,
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
                    ];
                })->toArray(),
                'genres' => $genres->map(function (Genre $genre) {
                    return [
                        'id' => $genre->id,
                        'name' => $genre->name,
                    ];
                })->toArray(),
                'categories' => $categories->map(function (Category $category) {
                    return [
                        'id' => $category->id,
                        'name' => $category->name,
                    ];
                })->toArray(),
                'platforms' => $platforms->map(function (Platform $platform) {
                    return [
                        'id' => $platform->id,
                        'name' => $platform->name,
                    ];
                })->toArray(),
            ];
        })->toArray();
    }
}
