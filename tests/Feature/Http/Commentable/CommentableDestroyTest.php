<?php

namespace Tests\Feature\Http\Commentable;

use App\Models\{User, Game, Commentable};
use Tests\Feature\Http\BaseIntegrationTesting;
use Tests\Traits\{
    HasDummyGame,
    HasDummyCommentable,
};

class CommentableDestroyTest extends BaseIntegrationTesting
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
     * The commentable game.
     *
     * @var \App\Models\Game
     */
    private Game $game;

    /**
     * The dummy commentable.
     *
     * @var \App\Models\Commentable
     */
    private Commentable $commentable;

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

        $this->commentable = $this->createDummyCommentable([
            'user_id' => $this->user->id,
            'commentable_id' => $this->game->id,
            'commentable_type' => $this->game::class,
        ]);
    }

    /**
     * Test if can't delete a comment if not authenticated.
     *
     * @return void
     */
    public function test_if_cant_delete_a_comment_if_not_authenticated(): void
    {
        $this->authService->clearAuthenticationCookies();

        $this->deleteJson(route('comments.destroy', $this->commentable))
            ->assertUnauthorized()
            ->assertSee('We could not authenticate your user. Please, try to login again.');
    }

    /**
     * Test if can't delete another user comment.
     *
     * @return void
     */
    public function test_if_cant_delete_another_user_comment(): void
    {
        $commentable = $this->createDummyCommentable([
            'commentable_id' => $this->game->id,
            'commentable_type' => $this->game::class,
        ]);

        $this->deleteJson(route('comments.destroy', $commentable))
            ->assertForbidden()
            ->assertSee('This comment does not belongs to your user. No one action is allowed.');
    }

    /**
     * Test if can soft delete self comment.
     *
     * @return void
     */
    public function test_if_can_soft_delete_self_comment(): void
    {
        $this->assertNotSoftDeleted($this->commentable);

        $this->deleteJson(route('comments.destroy', $this->commentable))->assertOk();

        $this->assertSoftDeleted($this->commentable);
    }
}
