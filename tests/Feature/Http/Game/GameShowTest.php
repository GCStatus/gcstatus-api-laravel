<?php

namespace Tests\Feature\Http\Game;

use App\Models\Game;
use Tests\Feature\Http\BaseIntegrationTesting;
use Tests\Traits\{
    HasDummyTag,
    HasDummyDlc,
    HasDummyGame,
    HasDummyCrack,
    HasDummyGenre,
    HasDummyTorrent,
    HasDummyPlatform,
    HasDummyCategory,
    HasDummyViewable,
    HasDummyHeartable,
    HasDummyDeveloper,
    HasDummyPublisher,
    HasDummyStoreable,
    HasDummyReviewable,
    HasDummyCriticable,
    HasDummyCommentable,
    HasDummyGalleriable,
    HasDummyGameSupport,
    HasDummyLanguageable,
    HasDummyRequirementable,
};

class GameShowTest extends BaseIntegrationTesting
{
    use HasDummyTag;
    use HasDummyDlc;
    use HasDummyGame;
    use HasDummyCrack;
    use HasDummyGenre;
    use HasDummyTorrent;
    use HasDummyPlatform;
    use HasDummyCategory;
    use HasDummyViewable;
    use HasDummyHeartable;
    use HasDummyDeveloper;
    use HasDummyPublisher;
    use HasDummyStoreable;
    use HasDummyReviewable;
    use HasDummyCriticable;
    use HasDummyCommentable;
    use HasDummyGalleriable;
    use HasDummyGameSupport;
    use HasDummyLanguageable;
    use HasDummyRequirementable;

    /**
     * The dummy game.
     *
     * @var \App\Models\Game
     */
    private Game $game;

    /**
     * Setup new test environments.
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->game = $this->createDummyGame();

        $this->createDummyCrackTo($this->game);
        $this->createDummyTorrent(['game_id' => $this->game->id]);
        $dlc = $this->createDummyDlcTo($this->game);
        $this->createDummyGameSupportTo($this->game);

        $this->game->tags()->save(
            $this->createDummyTag(),
        );
        $this->game->genres()->save(
            $this->createDummyGenre(),
        );
        $this->game->categories()->save(
            $this->createDummyCategory(),
        );
        $this->game->platforms()->save(
            $this->createDummyPlatform(),
        );
        $this->game->developers()->save(
            $this->createDummyDeveloper(),
        );
        $this->game->publishers()->save(
            $this->createDummyPublisher(),
        );

        $this->createDummyStoreable([
            'storeable_id' => $this->game->id,
            'storeable_type' => $this->game::class,
        ]);
        $this->createDummyGalleriables(4, [
            'galleriable_id' => $this->game->id,
            'galleriable_type' => $this->game::class,
        ]);
        $this->createDummyReviewable([
            'reviewable_id' => $this->game->id,
            'reviewable_type' => $this->game::class,
        ]);
        $this->createDummyCriticable([
            'criticable_id' => $this->game->id,
            'criticable_type' => $this->game::class,
        ]);
        $this->createDummyCommentable([
            'commentable_id' => $this->game->id,
            'commentable_type' => $this->game::class,
        ]);
        $this->createDummyLanguageable([
            'languageable_id' => $this->game->id,
            'languageable_type' => $this->game::class,
        ]);
        $this->createDummyRequirementable([
            'requirementable_id' => $this->game->id,
            'requirementable_type' => $this->game::class,
        ]);

        // DLC
        $dlc->tags()->save(
            $this->createDummyTag(),
        );
        $dlc->genres()->save(
            $this->createDummyGenre(),
        );
        $dlc->categories()->save(
            $this->createDummyCategory(),
        );
        $dlc->platforms()->save(
            $this->createDummyPlatform(),
        );
        $dlc->developers()->save(
            $this->createDummyDeveloper(),
        );
        $dlc->publishers()->save(
            $this->createDummyPublisher(),
        );

        $this->createDummyStoreable([
            'storeable_id' => $dlc->id,
            'storeable_type' => $dlc::class,
        ]);
        $this->createDummyGalleriables(4, [
            'galleriable_id' => $dlc->id,
            'galleriable_type' => $dlc::class,
        ]);
    }

    /**
     * Test if can throw a not found if game didn't exists.
     *
     * @return void
     */
    public function test_if_can_throw_a_not_found_if_game_didnt_exists(): void
    {
        $this->getJson(route('games.show', 999999999))
            ->assertNotFound()
            ->assertSee('No query results for model [App\\\\Models\\\\Game].');
    }

    /**
     * Test if can check if game is hearted by auth user.
     *
     * @return void
     */
    public function test_if_can_check_if_game_is_hearted_by_auth_user(): void
    {
        $user = $this->actingAsDummyUser();

        $this->getJson(route('games.show', $this->game->slug))->assertJson([
            'data' => [
                'is_hearted' => false,
            ],
        ]);

        $this->createDummyHeartable([
            'heartable_id' => $this->game->id,
            'heartable_type' => $this->game::class,
        ]);

        $this->getJson(route('games.show', $this->game->slug))->assertJson([
            'data' => [
                'is_hearted' => false,
            ],
        ]);

        $this->createDummyHeartable([
            'user_id' => $user->id,
            'heartable_id' => $this->game->id,
            'heartable_type' => $this->game::class,
        ]);

        $this->getJson(route('games.show', $this->game->slug))->assertJson([
            'data' => [
                'is_hearted' => true,
            ],
        ]);
    }

    /**
     * Test if can check if game comment is hearted by auth user.
     *
     * @return void
     */
    public function test_if_can_check_if_game_comment_is_hearted_by_auth_user(): void
    {
        $user = $this->actingAsDummyUser();

        /** @var \App\Models\Commentable $comment */
        $comment = $this->game->comments[0];

        $this->getJson(route('games.show', $this->game->slug))->assertJson([
            'data' => [
                'comments' => [
                    [
                        'is_hearted' => false,
                    ],
                ],
            ],
        ]);

        $this->createDummyHeartable([
            'heartable_id' => $comment->id,
            'heartable_type' => $comment::class,
        ]);

        $this->getJson(route('games.show', $this->game->slug))->assertJson([
            'data' => [
                'comments' => [
                    [
                        'is_hearted' => false,
                    ],
                ],
            ],
        ]);

        $this->createDummyHeartable([
            'user_id' => $user->id,
            'heartable_id' => $comment->id,
            'heartable_type' => $comment::class,
        ]);

        $this->getJson(route('games.show', $this->game->slug))->assertJson([
            'data' => [
                'comments' => [
                    [
                        'is_hearted' => true,
                    ],
                ],
            ],
        ]);
    }

    /**
     * Test if can check if a comment reply is hearted by auth user.
     *
     * @return void
     */
    public function test_if_can_check_if_a_comment_reply_is_hearted_by_auth_user(): void
    {
        $user = $this->actingAsDummyUser();

        /** @var \App\Models\Commentable $comment */
        $comment = $this->game->comments[0];

        $reply = $this->createDummyCommentable([
            'user_id' => $user->id,
            'parent_id' => $comment->id,
            'commentable_id' => $this->game->id,
            'commentable_type' => $this->game::class,
        ]);

        $this->getJson(route('games.show', $this->game->slug))->assertJson([
            'data' => [
                'comments' => [
                    [
                        'replies' => [
                            [
                                'is_hearted' => false,
                            ],
                        ],
                    ],
                ],
            ],
        ]);

        $this->createDummyHeartable([
            'heartable_id' => $reply->id,
            'heartable_type' => $reply::class,
        ]);

        $this->getJson(route('games.show', $this->game->slug))->assertJson([
            'data' => [
                'comments' => [
                    [
                        'replies' => [
                            [
                                'is_hearted' => false,
                            ],
                        ],
                    ],
                ],
            ],
        ]);

        $this->createDummyHeartable([
            'user_id' => $user->id,
            'heartable_id' => $reply->id,
            'heartable_type' => $reply::class,
        ]);

        $this->getJson(route('games.show', $this->game->slug))->assertJson([
            'data' => [
                'comments' => [
                    [
                        'replies' => [
                            [
                                'is_hearted' => true,
                            ],
                        ],
                    ],
                ],
            ],
        ]);
    }

    /**
     * Test if can get correct json structure.
     *
     * @return void
     */
    public function test_if_can_get_correct_json_structure(): void
    {
        /** @var \App\Models\Commentable $comment */
        $comment = $this->game->comments[0];

        $this->createDummyCommentable([
            'parent_id' => $comment->id,
            'commentable_id' => $this->game->id,
            'commentable_type' => $this->game::class,
        ]);

        dd($this->getJson(route('games.show', $this->game->slug))->json());

        $this->getJson(route('games.show', $this->game->slug))->assertJsonStructure([
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
                'is_hearted',
                'short_description',
                'views_count',
                'hearts_count',
                'created_at',
                'updated_at',
                'support' => [
                    'id',
                    'url',
                    'email',
                    'contact',
                ],
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
                'developers' => [
                    '*' => [
                        'id',
                        'name',
                    ],
                ],
                'publishers' => [
                    '*' => [
                        'id',
                        'name',
                    ],
                ],
                'torrents' => [
                    '*' => [
                        'id',
                        'url',
                        'posted_at',
                        'provider' => [
                            'id',
                            'url',
                            'name',
                            'slug',
                        ],
                    ],
                ],
                'comments' => [
                    '*' => [
                        'id',
                        'is_hearted',
                        'comment',
                        'hearts_count' => [
                            'replies' => [
                                '*' => [
                                    'is',
                                    'is_hearted',
                                    'comment',
                                    'hearts_count',
                                ],
                            ],
                        ],
                    ],
                ],
                'stores' => [
                    '*' => [
                        'id',
                        'url',
                        'price',
                        'store_item_id',
                        'store' => [
                            'id',
                            'url',
                            'name',
                            'slug',
                            'logo',
                        ],
                    ],
                ],
                'requirements' => [
                    'id',
                    'os',
                    'dx',
                    'ram',
                    'rom',
                    'cpu',
                    'gpu',
                    'obs',
                    'network',
                    'type' => [
                        'id',
                        'os',
                        'potential',
                    ],
                ],
                'languages' => [
                    '*' => [
                        'id',
                        'menu',
                        'dubs',
                        'subtitles',
                        'language' => [
                            'id',
                            'name',
                            'slug',
                        ],
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
            ],
        ]);
    }
}
