<?php

namespace Tests\Feature\Http\Home;

use App\Models\{Game, Banner};
use Illuminate\Support\Carbon;
use Tests\Feature\Http\BaseIntegrationTesting;
use Tests\Traits\{
    HasDummyTag,
    HasDummyGame,
    HasDummyCrack,
    HasDummyGenre,
    HasDummyBanner,
    HasDummyCategory,
    HasDummyHeartable,
    HasDummyPlatform,
};

class HomeStructureTest extends BaseIntegrationTesting
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

        $this->removeCreatedGameOnCrack($game);

        $this->getJson(route('home'))->assertOk()->assertJsonStructure([
            'data' => [
                'upcoming' => [
                    '*' => [
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
                        'is_hearted',
                        'short_description',
                        'views_count',
                        'hearts_count',
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
                            ],
                        ],
                        'genres' => [
                            '*' => [
                                'id',
                                'name',
                            ],
                        ],
                        'categories' => [
                            '*' => [
                                'id',
                                'name',
                            ],
                        ],
                        'platforms' => [
                            '*' => [
                                'id',
                                'name',
                            ],
                        ],
                    ],
                ],
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

        $this->removeCreatedGameOnCrack($game);

        $this->getJson(route('home'))->assertOk()->assertJsonStructure([
            'data' => [
                'hot' => [
                    '*' => [
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
                        'is_hearted',
                        'short_description',
                        'views_count',
                        'hearts_count',
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
                            ],
                        ],
                        'genres' => [
                            '*' => [
                                'id',
                                'name',
                            ],
                        ],
                        'categories' => [
                            '*' => [
                                'id',
                                'name',
                            ],
                        ],
                        'platforms' => [
                            '*' => [
                                'id',
                                'name',
                            ],
                        ],
                    ],
                ],
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

        $this->removeCreatedGameOnCrack($game);

        $this->getJson(route('home'))->assertOk()->assertJsonStructure([
            'data' => [
                'popular' => [
                    '*' => [
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
                        'is_hearted',
                        'short_description',
                        'views_count',
                        'hearts_count',
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
                            ],
                        ],
                        'genres' => [
                            '*' => [
                                'id',
                                'name',
                            ],
                        ],
                        'categories' => [
                            '*' => [
                                'id',
                                'name',
                            ],
                        ],
                        'platforms' => [
                            '*' => [
                                'id',
                                'name',
                            ],
                        ],
                    ],
                ],
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
        $game = $this->createDummyGame();

        $this->createDummyHeartable([
            'heartable_id' => $game->id,
            'heartable_type' => $game::class,
        ]);

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

        $this->removeCreatedGameOnCrack($game);

        $this->getJson(route('home'))->assertOk()->assertJsonStructure([
            'data' => [
                'most_liked' => [
                    '*' => [
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
                        'is_hearted',
                        'short_description',
                        'views_count',
                        'hearts_count',
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
                            ],
                        ],
                        'genres' => [
                            '*' => [
                                'id',
                                'name',
                            ],
                        ],
                        'categories' => [
                            '*' => [
                                'id',
                                'name',
                            ],
                        ],
                        'platforms' => [
                            '*' => [
                                'id',
                                'name',
                            ],
                        ],
                    ],
                ],
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

        $this->removeCreatedGameOnCrack($game);

        $this->getJson(route('home'))->assertOk()->assertJsonStructure([
            'data' => [
                'next_release' => [
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
                    'is_hearted',
                    'short_description',
                    'views_count',
                    'hearts_count',
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
                        ],
                    ],
                    'genres' => [
                        '*' => [
                            'id',
                            'name',
                        ],
                    ],
                    'categories' => [
                        '*' => [
                            'id',
                            'name',
                        ],
                    ],
                    'platforms' => [
                        '*' => [
                            'id',
                            'name',
                        ],
                    ],
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

        $this->removeCreatedGameOnCrack($game);

        $this->createDummyBanner([
            'bannerable_id' => $game->id,
            'bannerable_type' => $game::class,
            'component' => Banner::HOME_HEADER_CAROUSEL_BANNERS,
        ]);

        $this->getJson(route('home'))->assertOk()->assertJsonStructure([
            'data' => [
                'banners' => [
                    '*' => [
                        'id',
                        'type',
                        'bannerable' => [
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
                            'is_hearted',
                            'short_description',
                            'views_count',
                            'hearts_count',
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
                                ],
                            ],
                            'genres' => [
                                '*' => [
                                    'id',
                                    'name',
                                ],
                            ],
                            'categories' => [
                                '*' => [
                                    'id',
                                    'name',
                                ],
                            ],
                            'platforms' => [
                                '*' => [
                                    'id',
                                    'name',
                                ],
                            ],
                        ],
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
}
