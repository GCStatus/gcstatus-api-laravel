<?php

namespace Tests\Feature\Http\Admin\Game;

use Illuminate\Support\Carbon;
use Tests\Feature\Http\BaseIntegrationTesting;
use App\Models\{
    Tag,
    Dlc,
    Game,
    User,
    Genre,
    Torrent,
    Category,
    Platform,
    Publisher,
    Developer,
    Storeable,
    Criticable,
    Reviewable,
    Galleriable,
    Commentable,
    Languageable,
    Requirementable,
};
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
     * The dummy user.
     *
     * @var \App\Models\User
     */
    private User $user;

    /**
     * The dummy game.
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
     * Test if can't see if is unauthenticated.
     *
     * @return void
     */
    public function test_if_cant_see_if_user_is_unauthenticated(): void
    {
        $this->authService->clearAuthenticationCookies();

        $this->getJson(route('admin.games.show', $this->game))
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

        $this->getJson(route('admin.games.show', $this->game))->assertNotFound();
    }

    /**
     * Test if can throw a not found if game didn't exists.
     *
     * @return void
     */
    public function test_if_can_throw_a_not_found_if_game_didnt_exists(): void
    {
        $this->getJson(route('admin.games.show', 999999999))
            ->assertNotFound()
            ->assertSee('No query results for model [App\\\\Models\\\\Game] 999999999');
    }

    /**
     * Test if can't mark the views count.
     *
     * @return void
     */
    public function test_if_cant_mark_the_views_count(): void
    {
        $this->getJson(route('admin.games.show', $this->game->id))->assertJson([
            'data' => [
                'views_count' => 0,
            ],
        ]);

        $this->getJson(route('admin.games.show', $this->game->id))->assertJson([
            'data' => [
                'views_count' => 0,
            ],
        ]);

        $this->getJson(route('admin.games.show', $this->game->id))->assertJson([
            'data' => [
                'views_count' => 0,
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

        $this->getJson(route('admin.games.show', $this->game->id))->assertJsonStructure([
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
                'support' => [
                    'id',
                    'url',
                    'email',
                    'contact',
                ],
                'crack' => [
                    'id',
                    'cracked_at',
                    'created_at',
                    'updated_at',
                    'status' => [
                        'id',
                        'name',
                        'created_at',
                        'updated_at',
                    ],
                    'cracker' => [
                        'id',
                        'name',
                        'slug',
                        'acting',
                        'created_at',
                        'updated_at',
                    ],
                    'protection' => [
                        'id',
                        'name',
                        'slug',
                        'created_at',
                        'updated_at',
                    ],
                ],
                'tags' => [
                    '*' => [
                        'id',
                        'name',
                        'slug',
                        'created_at',
                        'updated_at',
                    ],
                ],
                'genres' => [
                    '*' => [
                        'id',
                        'name',
                        'slug',
                        'created_at',
                        'updated_at',
                    ],
                ],
                'categories' => [
                    '*' => [
                        'id',
                        'name',
                        'slug',
                        'created_at',
                        'updated_at',
                    ],
                ],
                'platforms' => [
                    '*' => [
                        'id',
                        'name',
                        'slug',
                        'created_at',
                        'updated_at',
                    ],
                ],
                'developers' => [
                    '*' => [
                        'id',
                        'name',
                        'slug',
                        'created_at',
                        'updated_at',
                    ],
                ],
                'publishers' => [
                    '*' => [
                        'id',
                        'name',
                        'slug',
                        'created_at',
                        'updated_at',
                    ],
                ],
                'torrents' => [
                    '*' => [
                        'id',
                        'url',
                        'posted_at',
                        'created_at',
                        'updated_at',
                        'provider' => [
                            'id',
                            'url',
                            'name',
                            'slug',
                            'created_at',
                            'updated_at',
                        ],
                    ],
                ],
                'comments' => [
                    '*' => [
                        'id',
                        'comment',
                        'hearts_count',
                        'created_at',
                        'updated_at',
                        'user' => [
                            'id',
                            'name',
                            'nickname',
                            'level',
                            'photo',
                            'created_at',
                            'updated_at',
                        ],
                        'replies' => [
                            '*' => [
                                'id',
                                'comment',
                                'hearts_count',
                                'created_at',
                                'updated_at',
                                'user' => [
                                    'id',
                                    'name',
                                    'nickname',
                                    'level',
                                    'photo',
                                    'created_at',
                                    'updated_at',
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
                'requirements' => [
                    '*' => [
                        'id',
                        'os',
                        'dx',
                        'ram',
                        'rom',
                        'cpu',
                        'gpu',
                        'obs',
                        'network',
                        'created_at',
                        'updated_at',
                        'type' => [
                            'id',
                            'os',
                            'potential',
                            'created_at',
                            'updated_at',
                        ],
                    ],
                ],
                'languages' => [
                    '*' => [
                        'id',
                        'menu',
                        'dubs',
                        'subtitles',
                        'created_at',
                        'updated_at',
                        'language' => [
                            'id',
                            'name',
                            'slug',
                            'created_at',
                            'updated_at',
                        ],
                    ],
                ],
                'galleries' => [
                    '*' => [
                        'id',
                        'path',
                        'created_at',
                        'updated_at',
                        'type' => [
                            'id',
                            'name',
                            'created_at',
                            'updated_at',
                        ],
                    ],
                ],
                'reviews' => [
                    '*' => [
                        'id',
                        'rate',
                        'review',
                        'consumed',
                        'created_at',
                        'updated_at',
                        'user' => [
                            'id',
                            'name',
                            'nickname',
                            'level',
                            'photo',
                            'created_at',
                            'updated_at',
                        ],
                    ],
                ],
                'critics' => [
                    '*' => [
                        'id',
                        'url',
                        'rate',
                        'posted_at',
                        'created_at',
                        'updated_at',
                        'critic' => [
                            'id',
                            'url',
                            'name',
                            'slug',
                            'logo',
                            'acting',
                            'created_at',
                            'updated_at',
                        ],
                    ],
                ],
                'dlcs' => [
                    '*' => [
                        'id',
                        'slug',
                        'free',
                        'title',
                        'cover',
                        'about',
                        'legal',
                        'description',
                        'release_date',
                        'short_description',
                        'created_at',
                        'updated_at',
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
                        'platforms' => [
                            '*' => [
                                'id',
                                'name',
                                'created_at',
                                'updated_at',
                            ],
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
        /** @var \App\Models\Commentable $comment */
        $comment = $this->game->comments[0];

        $this->createDummyCommentable([
            'parent_id' => $comment->id,
            'commentable_id' => $this->game->id,
            'commentable_type' => $this->game::class,
        ]);

        /** @var \App\Models\GameSupport $support */
        $support = $this->game->support;

        /** @var \App\Models\Crack $crack */
        $crack = $this->game->crack;

        /** @var \App\Models\Status $crackStatus */
        $crackStatus = $crack->status;

        /** @var \App\Models\Cracker $cracker */
        $cracker = $crack->cracker;

        /** @var \App\Models\Protection $protection */
        $protection = $crack->protection;

        /** @var \Illuminate\Database\Eloquent\Collection<int, \App\Models\Tag> $tags */
        $tags = $this->game->tags;

        /** @var \Illuminate\Database\Eloquent\Collection<int, \App\Models\Genre> $genres */
        $genres = $this->game->genres;

        /** @var \Illuminate\Database\Eloquent\Collection<int, \App\Models\Category> $categories */
        $categories = $this->game->categories;

        /** @var \Illuminate\Database\Eloquent\Collection<int, \App\Models\Platform> $platforms */
        $platforms = $this->game->platforms;

        /** @var \Illuminate\Database\Eloquent\Collection<int, \App\Models\Developer> $developers */
        $developers = $this->game->developers;

        /** @var \Illuminate\Database\Eloquent\Collection<int, \App\Models\Publisher> $publishers */
        $publishers = $this->game->publishers;

        /** @var \Illuminate\Database\Eloquent\Collection<int, \App\Models\Torrent> $torrents */
        $torrents = $this->game->torrents;

        /** @var \Illuminate\Database\Eloquent\Collection<int, \App\Models\Commentable> $comments */
        $comments = $this->game->comments;

        /** @var \Illuminate\Database\Eloquent\Collection<int, \App\Models\Storeable> $stores */
        $stores = $this->game->stores;

        /** @var \Illuminate\Database\Eloquent\Collection<int, \App\Models\Requirementable> $requirements */
        $requirements = $this->game->requirements;

        /** @var \Illuminate\Database\Eloquent\Collection<int, \App\Models\Languageable> $languages */
        $languages = $this->game->languages;

        /** @var \Illuminate\Database\Eloquent\Collection<int, \App\Models\Galleriable> $galleries */
        $galleries = $this->game->galleries;

        /** @var \Illuminate\Database\Eloquent\Collection<int, \App\Models\Reviewable> $reviews */
        $reviews = $this->game->reviews;

        /** @var \Illuminate\Database\Eloquent\Collection<int, \App\Models\Criticable> $critics */
        $critics = $this->game->critics;

        /** @var \Illuminate\Database\Eloquent\Collection<int, \App\Models\Dlc> $dlcs */
        $dlcs = $this->game->dlcs;

        $this->getJson(route('admin.games.show', $this->game->id))->assertJson([
            'data' => [
                'id' => $this->game->id,
                'age' => $this->game->age,
                'slug' => $this->game->slug,
                'free' => $this->game->free,
                'title' => $this->game->title,
                'cover' => $this->game->cover,
                'about' => $this->game->about,
                'legal' => $this->game->legal,
                'website' => $this->game->website,
                'views_count' => 0,
                'condition' => $this->game->condition,
                'description' => $this->game->description,
                'release_date' => Carbon::parse($this->game->release_date)->toISOString(),
                'great_release' => $this->game->great_release,
                'short_description' => $this->game->short_description,
                'hearts_count' => 0,
                'created_at' => $this->game->created_at?->toISOString(),
                'updated_at' => $this->game->updated_at?->toISOString(),
                'support' => [
                    'id' => $support->id,
                    'url' => $support->url,
                    'email' => $support->email,
                    'contact' => $support->contact,
                ],
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
                'developers' => $developers->map(function (Developer $developer) {
                    return [
                        'id' => $developer->id,
                        'name' => $developer->name,
                        'slug' => $developer->slug,
                    ];
                })->toArray(),
                'publishers' => $publishers->map(function (Publisher $publisher) {
                    return [
                        'id' => $publisher->id,
                        'name' => $publisher->name,
                        'slug' => $publisher->slug,
                    ];
                })->toArray(),
                'torrents' => $torrents->map(function (Torrent $torrent) {
                    /** @var \App\Models\TorrentProvider $provider */
                    $provider = $torrent->provider;

                    return [
                        'id' => $torrent->id,
                        'url' => $torrent->url,
                        'posted_at' => Carbon::parse($torrent->posted_at)->toISOString(),
                        'provider' => [
                            'id' => $provider->id,
                            'url' => $provider->url,
                            'name' => $provider->name,
                            'slug' => $provider->slug,
                        ],
                    ];
                })->toArray(),
                'comments' => $comments->map(function (Commentable $comment) {
                    /** @var \Illuminate\Database\Eloquent\Collection<int, \App\Models\Commentable> $replies */
                    $replies = $comment->children;

                    /** @var \App\Models\User $user */
                    $user = $comment->user;

                    /** @var \App\Models\Profile $profile */
                    $profile = $user->profile;

                    /** @var \App\Models\Level $level */
                    $level = $user->level;

                    return [
                        'id' => $comment->id,
                        'comment' => $comment->comment,
                        'hearts_count' => $comment->hearts_count,
                        'created_at' => $comment->created_at?->toISOString(),
                        'updated_at' => $comment->updated_at?->toISOString(),
                        'user' => [
                            'id' => $user->id,
                            'name' => $user->name,
                            'nickname' => $user->nickname,
                            'level' => $level->level,
                            'photo' => storage()->getPath($profile->photo),
                            'created_at' => $user->created_at?->toISOString(),
                        ],
                        'replies' => $replies->map(function (Commentable $reply) {
                            /** @var \App\Models\User $user */
                            $user = $reply->user;

                            /** @var \App\Models\Profile $profile */
                            $profile = $user->profile;

                            /** @var \App\Models\Level $level */
                            $level = $user->level;

                            return [
                                'id' => $reply->id,
                                'comment' => $reply->comment,
                                'hearts_count' => $reply->hearts_count,
                                'created_at' => $reply->created_at?->toISOString(),
                                'updated_at' => $reply->updated_at?->toISOString(),
                                'user' => [
                                    'id' => $user->id,
                                    'name' => $user->name,
                                    'nickname' => $user->nickname,
                                    'level' => $level->level,
                                    'photo' => storage()->getPath($profile->photo),
                                    'created_at' => $user->created_at?->toISOString(),
                                ],
                            ];
                        })->toArray(),
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
                        'store' => [
                            'id' => $store->id,
                            'url' => $store->url,
                            'name' => $store->name,
                            'slug' => $store->slug,
                            'logo' => $store->logo,
                        ],
                    ];
                })->toArray(),
                'requirements' => $requirements->map(function (Requirementable $requirement) {
                    /** @var \App\Models\RequirementType $type */
                    $type = $requirement->requirementType;

                    return [
                        'id' => $requirement->id,
                        'os' => $requirement->os,
                        'dx' => $requirement->dx,
                        'ram' => $requirement->ram,
                        'rom' => $requirement->rom,
                        'cpu' => $requirement->cpu,
                        'gpu' => $requirement->gpu,
                        'obs' => $requirement->obs,
                        'network' => $requirement->network,
                        'type' => [
                            'id' => $type->id,
                            'os' => $type->os,
                            'potential' => $type->potential,
                        ],
                    ];
                })->toArray(),
                'languages' => $languages->map(function (Languageable $languageable) {
                    /** @var \App\Models\Language $language */
                    $language = $languageable->language;

                    return [
                        'id' => $languageable->id,
                        'menu' => $languageable->menu,
                        'dubs' => $languageable->dubs,
                        'subtitles' => $languageable->subtitles,
                        'language' => [
                            'id' => $language->id,
                            'name' => $language->name,
                            'slug' => $language->slug,
                        ],
                    ];
                })->toArray(),
                'galleries' => $galleries->map(function (Galleriable $gallery) {
                    /** @var \App\Models\MediaType $type */
                    $type = $gallery->mediaType;

                    return [
                        'id' => $gallery->id,
                        'path' => $gallery->path,
                        'type' => [
                            'id' => $type->id,
                            'name' => $type->name,
                        ],
                    ];
                })->toArray(),
                'reviews' => $reviews->map(function (Reviewable $review) {
                    /** @var \App\Models\User $user */
                    $user = $review->user;

                    /** @var \App\Models\Profile $profile */
                    $profile = $user->profile;

                    /** @var \App\Models\Level $level */
                    $level = $user->level;

                    return [
                        'id' => $review->id,
                        'rate' => $review->rate,
                        'review' => $review->review,
                        'consumed' => $review->consumed,
                        'created_at' => $review->created_at?->toISOString(),
                        'updated_at' => $review->updated_at?->toISOString(),
                        'user' => [
                            'id' => $user->id,
                            'name' => $user->name,
                            'nickname' => $user->nickname,
                            'level' => $level->level,
                            'photo' => storage()->getPath($profile->photo),
                            'created_at' => $user->created_at?->toISOString(),
                        ],
                    ];
                })->toArray(),
                'critics' => $critics->map(function (Criticable $criticable) {
                    /** @var \App\Models\Critic $critic */
                    $critic = $criticable->critic;

                    return [
                        'id' => $criticable->id,
                        'url' => $criticable->url,
                        'rate' => $criticable->rate,
                        'posted_at' => Carbon::parse($criticable->posted_at)->toISOString(),
                        'critic' => [
                            'id' => $critic->id,
                            'url' => $critic->url,
                            'name' => $critic->name,
                            'slug' => $critic->slug,
                            'logo' => $critic->logo,
                            'acting' => $critic->acting,
                        ],
                    ];
                })->toArray(),
                'dlcs' => $dlcs->map(function (Dlc $dlc) {
                    /** @var \Illuminate\Database\Eloquent\Collection<int, \App\Models\Storeable> $stores */
                    $stores = $dlc->stores;

                    /** @var \Illuminate\Database\Eloquent\Collection<int, \App\Models\Platform> $platforms */
                    $platforms = $dlc->platforms;

                    return [
                        'id' => $dlc->id,
                        'slug' => $dlc->slug,
                        'free' => $dlc->free,
                        'title' => $dlc->title,
                        'cover' => $dlc->cover,
                        'about' => $dlc->about,
                        'legal' => $dlc->legal,
                        'description' => $dlc->description,
                        'release_date' => Carbon::parse($dlc->release_date)->toISOString(),
                        'short_description' => $dlc->short_description,
                        'stores' => $stores->map(function (Storeable $storeable) {
                            /** @var \App\Models\Store $store */
                            $store = $storeable->store;

                            return [
                                'id' => $storeable->id,
                                'url' => $storeable->url,
                                'price' => $storeable->price,
                                'store_item_id' => $storeable->store_item_id,
                                'store' => [
                                    'id' => $store->id,
                                    'url' => $store->url,
                                    'name' => $store->name,
                                    'slug' => $store->slug,
                                    'logo' => $store->logo,
                                ],
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
            ],
        ]);
    }
}
