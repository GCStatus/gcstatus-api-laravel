<?php

namespace Tests\Feature\Http\Commentable;

use App\Models\{User, Game};
use App\Notifications\DatabaseNotification;
use Illuminate\Support\Facades\Notification;
use Tests\Feature\Http\BaseIntegrationTesting;
use Tests\Traits\{
    HasDummyGame,
    HasDummyCommentable,
};

class CommentableStoreTest extends BaseIntegrationTesting
{
    use HasDummyGame;
    use HasDummyCommentable;

    /**
     * The dummy user.
     *
     * @var \App\Models\User
     */
    private User $user;

    /**
     * The dummy commentable game.
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

        $this->user = $this->actingAsDummyUser();

        $this->game = $this->createDummyGame();
    }

    /**
     * Get the valid payload data.
     *
     * @return array<string, mixed>
     */
    private function getValidPayload(): array
    {
        return [
            'comment' => fake()->text(),
            'commentable_id' => $this->game->id,
            'commentable_type' => $this->game::class,
        ];
    }

    /**
     * Test if can't comment if not authenticated.
     *
     * @return void
     */
    public function test_if_cant_comment_if_not_authenticated(): void
    {
        $this->authService->clearAuthenticationCookies();

        $this->postJson(route('comments.store'), $this->getValidPayload())
            ->assertUnauthorized()
            ->assertSee('We could not authenticate your user. Please, try to login again.');
    }

    /**
     * Test if can't comment without payload.
     *
     * @return void
     */
    public function test_if_cant_comment_without_payload(): void
    {
        $this->postJson(route('comments.store'))->assertUnprocessable();
    }

    /**
     * Test if can throw correct invalid json keys.
     *
     * @return void
     */
    public function test_if_can_throw_correct_invalid_json_keys(): void
    {
        $this->postJson(route('comments.store'))
            ->assertUnprocessable()
            ->assertInvalid(['comment', 'commentable_id', 'commentable_type']);
    }

    /**
     * Test if can throw correct invalid json messages.
     *
     * @return void
     */
    public function test_if_can_throw_correct_invalid_json_messages(): void
    {
        $this->postJson(route('comments.store'))
            ->assertUnprocessable()
            ->assertInvalid(['comment', 'commentable_id', 'commentable_type'])
            ->assertSee('The commentable id field is required. (and 2 more errors)');
    }

    /**
     * Test if can't reply an inexistent comment.
     *
     * @return void
     */
    public function test_if_cant_reply_an_inexistent_comment(): void
    {
        $this->postJson(route('comments.store'), $this->getValidPayload() + [
            'parent_id' => 999,
        ])->assertUnprocessable()
            ->assertInvalid(['parent_id'])
            ->assertSee('The selected parent id is invalid.');
    }

    /**
     * Test if can create a new comment.
     *
     * @return void
     */
    public function test_if_can_create_a_new_comment(): void
    {
        $this->postJson(route('comments.store'), $this->getValidPayload())->assertCreated();
    }

    /**
     * Test if can save a new comment on database.
     *
     * @return void
     */
    public function test_if_can_save_a_new_comment_on_database(): void
    {
        $this->postJson(route('comments.store'), $data = $this->getValidPayload())->assertCreated();

        $this->assertDatabaseHas('commentables', [
            'user_id' => $this->user->id,
            'comment' => $data['comment'],
            'commentable_id' => $data['commentable_id'],
            'commentable_type' => $data['commentable_type'],
        ]);
    }

    /**
     * Test if can reply an existent comment.
     *
     * @return void
     */
    public function test_if_can_reply_an_existent_comment(): void
    {
        $this->postJson(route('comments.store'), $data = $this->getValidPayload() + [
            'parent_id' => $parentId = $this->createDummyCommentable([
                'commentable_id' => $this->game->id,
                'commentable_type' => $this->game::class,
            ])->id,
        ])->assertCreated();

        $this->assertDatabaseHas('commentables', [
            'parent_id' => $parentId,
            'user_id' => $this->user->id,
            'comment' => $data['comment'],
            'commentable_id' => $data['commentable_id'],
            'commentable_type' => $data['commentable_type'],
        ]);
    }

    /**
     * Test if can't generate a notification if I'm replying my own comment.
     *
     * @return void
     */
    public function test_if_cant_generate_a_notification_if_im_replying_my_own_comment(): void
    {
        Notification::fake();

        $toReply = $this->createDummyCommentable([
            'user_id' => $this->user->id,
            'commentable_id' => $this->game->id,
            'commentable_type' => $this->game::class,
        ]);

        $this->postJson(route('comments.store'), $this->getValidPayload() + [
            'parent_id' => $toReply->id,
        ])->assertCreated();

        Notification::assertNothingSent();
    }

    /**
     * Test if can generate a notification for a reply on my comment.
     *
     * @return void
     */
    public function test_if_can_generate_a_notification_for_a_reply_on_my_comment(): void
    {
        Notification::fake();

        $toReply = $this->createDummyCommentable([
            'commentable_id' => $this->game->id,
            'commentable_type' => $this->game::class,
        ]);

        $this->postJson(route('comments.store'), $this->getValidPayload() + [
            'parent_id' => $toReply->id,
        ])->assertCreated();

        /** @var \App\Models\User $receiver */
        $receiver = $toReply->user;

        $replier = $this->user->nickname;

        $gameSlug = $this->game->slug;

        Notification::assertSentTo($receiver, DatabaseNotification::class, function (DatabaseNotification $notification) use ($replier, $gameSlug) {
            return $notification->data['icon'] === 'FaRegComment' &&
                $notification->data['title'] === "$replier just replied your comment." &&
                $notification->data['actionUrl'] === "/games/$gameSlug";
        });
    }

    /**
     * Test if can respond with correct json structure.
     *
     * @return void
     */
    public function test_if_can_respond_with_correct_json_structure(): void
    {
        $this->postJson(route('comments.store'), $this->getValidPayload())->assertCreated()->assertJsonStructure([
            'data' => [
                'id',
                'comment',
                'is_hearted',
                'hearts_count',
                'created_at',
                'updated_at',
                'by' => [
                    'id',
                    'name',
                    'nickname',
                    'level',
                    'photo',
                    'created_at',
                ],
                'replies' => [],
            ],
        ]);
    }

    /**
     * Test if can respond with correct json data.
     *
     * @return void
     */
    public function test_if_can_respond_with_correct_json_data(): void
    {
        /** @var \App\Models\Level $level */
        $level = $this->user->level;

        /** @var \App\Models\Profile $profile */
        $profile = $this->user->profile;

        $this->postJson(route('comments.store'), $data = $this->getValidPayload())->assertCreated()->assertJson([
            'data' => [
                'comment' => $data['comment'],
                'is_hearted' => false,
                'hearts_count' => 0,
                'by' => [
                    'id' => $this->user->id,
                    'name' => $this->user->name,
                    'nickname' => $this->user->nickname,
                    'level' => $level->level,
                    'photo' => storage()->getPath($profile->photo),
                    'created_at' => $this->user->created_at?->toISOString(),
                ],
                'replies' => [],
            ],
        ]);
    }
}
