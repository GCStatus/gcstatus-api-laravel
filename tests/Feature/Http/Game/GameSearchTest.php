<?php

namespace Tests\Feature\Http\Game;

use Illuminate\Support\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Tests\Feature\Http\BaseIntegrationTesting;
use App\Models\{
    Tag,
    Game,
    Genre,
    Category,
    Platform,
};
use Tests\Traits\{
    HasDummyTag,
    HasDummyGame,
    HasDummyGenre,
    HasDummyCrack,
    HasDummyCategory,
    HasDummyPlatform,
    HasDummyHeartable,
};

class GameSearchTest extends BaseIntegrationTesting
{
    use HasDummyTag;
    use HasDummyGame;
    use HasDummyGenre;
    use HasDummyCrack;
    use HasDummyCategory;
    use HasDummyPlatform;
    use HasDummyHeartable;

    /**
     * Test if can't find any games if title don't match.
     *
     * @return void
     */
    public function test_if_cant_find_any_games_if_title_dont_match(): void
    {
        $this->getJson(route('games.search', [
            'q' => 'Missing',
        ]))->assertOk()->assertJsonCount(0, 'data');

        $this->createDummyGames(4, [
            'title' => 'Test',
        ]);

        $this->getJson(route('games.search', [
            'q' => 'Missing',
        ]))->assertOk()->assertJsonCount(0, 'data');
    }
    /**
     * Test if can get correct is hearted games.
     *
     * @return void
     */
    public function test_if_can_get_correct_is_hearted_games(): void
    {
        $user = $this->actingAsDummyUser();

        $games = $this->createDummyGames(4, [
            'title' => 'Test',
        ]);

        $this->getJson(route('games.search', [
            'q' => 'Test',
        ]))->assertOk()->assertJson([
            'data' => $games->map(function () {
                return [
                    'is_hearted' => false,
                ];
            })->toArray(),
        ]);

        $games->each(function (Game $game) {
            $this->createDummyHeartable([
                'heartable_id' => $game->id,
                'heartable_type' => $game::class,
            ]);
        });

        $this->getJson(route('games.search', [
            'q' => 'Test',
        ]))->assertOk()->assertJson([
            'data' => $games->map(function () {
                return [
                    'is_hearted' => false,
                ];
            })->toArray(),
        ]);

        $games->each(function (Game $game) use ($user) {
            $this->createDummyHeartable([
                'user_id' => $user->id,
                'heartable_id' => $game->id,
                'heartable_type' => $game::class,
            ]);
        });

        $this->getJson(route('games.search', [
            'q' => 'Test',
        ]))->assertOk()->assertJson([
            'data' => $games->map(function () {
                return [
                    'is_hearted' => true,
                ];
            })->toArray(),
        ]);
    }

    /**
     * Test if can't get more than 100 games for search.
     *
     * @return void
     */
    public function test_if_cant_get_more_than_100_games_for_search(): void
    {
        $this->createDummyGames(101, [
            'title' => 'Test',
        ]);

        $this->getJson(route('games.search', [
            'q' => 'Test',
        ]))->assertOk()->assertJsonCount(100, 'data');
    }

    /**
     * Test if can get correct games count. (two games with other name. four in total. two found)
     *
     * @return void
     */
    public function test_if_can_get_correct_games_count(): void
    {
        $this->createDummyGames(2, [
            'title' => 'Missing',
        ]);

        $this->createDummyGames(2, [
            'title' => 'Test',
        ]);

        $this->getJson(route('games.search', [
            'q' => 'Test',
        ]))->assertOk()->assertJsonCount(2, 'data');
    }

    /**
     * Test if can get correct json structure.
     *
     * @return void
     */
    public function test_if_can_get_correct_json_structure(): void
    {
        $games = $this->createDummyGames(2, [
            'title' => 'Test',
        ])->each(function (Game $game) {
            $game->tags()->save(
                $this->createDummyTag(),
            );

            $game->genres()->save(
                $this->createDummyGenre(),
            );

            $game->categories()->save(
                $this->createDummyCategory(),
            );

            $game->platforms()->save(
                $this->createDummyPlatform(),
            );

            $this->createDummyCrackTo($game);
        });

        $this->removeWrongGames($games);

        $this->getJson(route('games.search', [
            'q' => 'Test',
        ]))->assertJsonStructure([
            'data' => [
                '*' => [
                    'id',
                    'slug',
                    'title',
                    'cover',
                    'condition',
                    'release_date',
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
        $games = $this->createDummyGames(2, [
            'title' => 'Test',
        ])->each(function (Game $game) {
            $game->tags()->save(
                $this->createDummyTag(),
            );

            $game->genres()->save(
                $this->createDummyGenre(),
            );

            $game->categories()->save(
                $this->createDummyCategory(),
            );

            $game->platforms()->save(
                $this->createDummyPlatform(),
            );

            $this->createDummyCrackTo($game);
        });

        $this->removeWrongGames($games);

        $this->getJson(route('games.search', [
            'q' => 'Test',
        ]))->assertJson([
            'data' => $games->map(function (Game $game) {
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
     * Remove the games created on crack creation.
     *
     * @param \Illuminate\Database\Eloquent\Collection<int, \App\Models\Game> $games
     * @return void
     */
    private function removeWrongGames(Collection $games): void
    {
        Game::whereNotIn('id', $games->pluck('id')->toArray())->delete();
    }
}
