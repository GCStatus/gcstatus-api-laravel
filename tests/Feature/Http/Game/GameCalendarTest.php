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

class GameCalendarTest extends BaseIntegrationTesting
{
    use HasDummyTag;
    use HasDummyGame;
    use HasDummyGenre;
    use HasDummyCrack;
    use HasDummyCategory;
    use HasDummyPlatform;
    use HasDummyHeartable;

    /**
     * The dummy games.
     *
     * @var \Illuminate\Database\Eloquent\Collection<int, \App\Models\Game>
     */
    private Collection $games;

    /**
     * Setup new test environments.
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->games = $this->createDummyGames(4, [
            'release_date' => Carbon::today(),
        ]);

        $this->games->each(function (Game $game) {
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

        $this->removeWrongGames();
    }

    /**
     * Test if can't find older than 2 years games on calendar.
     *
     * @return void
     */
    public function test_if_cant_find_older_than_2_years_games_on_calendar(): void
    {
        $this->getJson(route('games.calendar'))->assertJsonCount(4, 'data');

        $this->createDummyGame([
            'release_date' => Carbon::today()->subYears(2),
        ]);

        $this->getJson(route('games.calendar'))->assertJsonCount(4, 'data');
    }

    /**
     * Test if can't find future games on calendar.
     *
     * @return void
     */
    public function test_if_cant_find_future_games_on_calendar(): void
    {
        $this->getJson(route('games.calendar'))->assertJsonCount(4, 'data');

        $this->createDummyGame([
            'release_date' => Carbon::today()->addYear(),
        ]);

        $this->getJson(route('games.calendar'))->assertJsonCount(4, 'data');
    }

    /**
     * Test if can get correct is hearted games.
     *
     * @return void
     */
    public function test_if_can_get_correct_is_hearted_games(): void
    {
        $user = $this->actingAsDummyUser();

        $this->getJson(route('games.calendar'))->assertJson([
            'data' => $this->games->map(function () {
                return [
                    'is_hearted' => false,
                ];
            })->toArray(),
        ]);

        $this->games->each(function (Game $game) {
            $this->createDummyHeartable([
                'heartable_id' => $game->id,
                'heartable_type' => $game::class,
            ]);
        });

        $this->getJson(route('games.calendar'))->assertJson([
            'data' => $this->games->map(function () {
                return [
                    'is_hearted' => false,
                ];
            })->toArray(),
        ]);

        $this->games->each(function (Game $game) use ($user) {
            $this->createDummyHeartable([
                'user_id' => $user->id,
                'heartable_id' => $game->id,
                'heartable_type' => $game::class,
            ]);
        });

        $this->getJson(route('games.calendar'))->assertJson([
            'data' => $this->games->map(function () {
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
        $this->getJson(route('games.calendar'))->assertJsonStructure([
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
        $this->getJson(route('games.calendar'))->assertJson([
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
     * Remove the games created on crack creation.
     *
     * @return void
     */
    public function removeWrongGames(): void
    {
        Game::whereNotIn('id', $this->games->pluck('id')->toArray())->delete();
    }
}
