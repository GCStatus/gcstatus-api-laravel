<?php

namespace Tests\Feature\Http\Game;

use Illuminate\Support\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Tests\Feature\Http\BaseIntegrationTesting;
use App\Models\{
    Tag,
    Game,
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
    HasDummyHeartable,
};

class GameFiltersTest extends BaseIntegrationTesting
{
    use HasDummyTag;
    use HasDummyGame;
    use HasDummyGenre;
    use HasDummyCrack;
    use HasDummyCategory;
    use HasDummyPlatform;
    use HasDummyHeartable;

    /**
     * Test if can't find an invalid attribute filter search.
     *
     * @return void
     */
    public function test_if_cant_find_an_invalid_attribute_filter_search(): void
    {
        $tag = $this->createDummyTag([
            'slug' => 'tag-slug',
        ]);

        $data = [
            'attribute' => 'invalid',
            'value' => $tag->slug,
        ];

        $this->getJson(route('games.filters.find', $data))
            ->assertUnprocessable()
            ->assertSee('This attribute is not accepted!');
    }

    /**
     * Test if can find correctly games quantity by filters.
     *
     * @return void
     */
    public function test_if_can_find_correctly_games_quantity_by_filters(): void
    {
        $tag = $this->createDummyTag([
            'slug' => 'tag-slug',
        ]);

        $data = [
            'attribute' => 'tags',
            'value' => $tag->slug,
        ];

        $this->getJson(route('games.filters.find', $data))->assertOk()->assertJsonCount(0, 'data');

        $this->createDummyGames(4)->each(function (Game $game) use ($tag) {
            $game->tags()->save($tag);
        });

        $this->getJson(route('games.filters.find', $data))->assertOk()->assertJsonCount(4, 'data');
    }

    /**
     * Test if can't find another filtered games.
     *
     * @return void
     */
    public function test_if_cant_find_another_filtered_games(): void
    {
        $tag = $this->createDummyTag([
            'slug' => 'tag-slug',
        ]);

        $data = [
            'attribute' => 'tags',
            'value' => $tag->slug,
        ];

        $this->getJson(route('games.filters.find', $data))->assertOk()->assertJsonCount(0, 'data');

        $this->createDummyGames(4)->each(function (Game $game) use ($tag) {
            $game->tags()->save($tag);
        });

        $this->getJson(route('games.filters.find', $data))->assertOk()->assertJsonCount(4, 'data');

        $this->createDummyGames(4)->each(function (Game $game) {
            $game->platforms()->save(
                $this->createDummyPlatform(),
            );
        });

        $this->getJson(route('games.filters.find', $data))->assertOk()->assertJsonCount(4, 'data');
    }

    /**
     * Test if can get correct is hearted games.
     *
     * @return void
     */
    public function test_if_can_get_correct_is_hearted_games(): void
    {
        $tag = $this->createDummyTag([
            'slug' => 'tag-slug',
        ]);

        $data = [
            'attribute' => 'tags',
            'value' => $tag->slug,
        ];

        $user = $this->actingAsDummyUser();

        $games = $this->createDummyGames(4)->each(function (Game $game) use ($tag) {
            $game->tags()->save($tag);

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

        $this->getJson(route('games.filters.find', $data))->assertOk()->assertJson([
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

        $this->getJson(route('games.filters.find', $data))->assertOk()->assertJson([
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

        $this->getJson(route('games.filters.find', $data))->assertOk()->assertJson([
            'data' => $games->map(function () {
                return [
                    'is_hearted' => true,
                ];
            })->toArray(),
        ]);
    }

    /**
     * Test if can get correct json structure.
     *
     * @return void
     */
    public function test_if_can_get_correct_json_structure(): void
    {
        $tag = $this->createDummyTag([
            'slug' => 'tag-slug',
        ]);

        $data = [
            'attribute' => 'tags',
            'value' => $tag->slug,
        ];

        $games = $this->createDummyGames(4);

        $games->each(function (Game $game) use ($tag) {
            $game->tags()->save($tag);

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

        $this->getJson(route('games.filters.find', $data))->assertOk()->assertJsonStructure([
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
        $tag = $this->createDummyTag([
            'slug' => 'tag-slug',
        ]);

        $data = [
            'attribute' => 'tags',
            'value' => $tag->slug,
        ];

        $games = $this->createDummyGames(4);

        $games->each(function (Game $game) use ($tag) {
            $game->tags()->save($tag);

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

        $games = $games->sortByDesc('release_date')->values();

        $this->getJson(route('games.filters.find', $data))->assertOk()->assertJson([
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
