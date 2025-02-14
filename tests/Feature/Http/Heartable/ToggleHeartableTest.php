<?php

namespace Tests\Feature\Http\Heartable;

use App\Models\User;
use Tests\Feature\Http\BaseIntegrationTesting;
use Tests\Traits\{
    HasDummyGame,
    HasDummyHeartable,
    HasDummyCommentable,
};

class ToggleHeartableTest extends BaseIntegrationTesting
{
    use HasDummyGame;
    use HasDummyHeartable;
    use HasDummyCommentable;

    /**
     * The dummy user.
     *
     * @var \App\Models\User
     */
    private User $user;

    /**
     * Setup new test environments.
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->user = $this->actingAsDummyUser();
    }

    /**
     * Test if can't store a heart if not authenticated.
     *
     * @return void
     */
    public function test_if_cant_store_a_heart_if_not_authenticated(): void
    {
        $this->authService->clearAuthenticationCookies();

        $this->postJson(route('hearts.toggle'))
            ->assertUnauthorized()
            ->assertSee('We could not authenticate your user. Please, try to login again.');
    }

    /**
     * Test if can create a heartable for a given game.
     *
     * @return void
     */
    public function test_if_can_create_a_heartable_for_a_given_game(): void
    {
        $game = $this->createDummyGame();

        $data = [
            'heartable_id' => $game->id,
            'heartable_type' => $game::class,
        ];

        $this->postJson(route('hearts.toggle'), $data)->assertOk();

        $this->assertDatabaseHas('heartables', [
            'user_id' => $this->user->id,
            'heartable_id' => $data['heartable_id'],
            'heartable_type' => $data['heartable_type'],
        ]);
    }

    /**
     * Test if can create a heartable for a given comment.
     *
     * @return void
     */
    public function test_if_can_create_a_heartable_for_a_given_comment(): void
    {
        $game = $this->createDummyGame();

        $comment = $this->createDummyCommentable([
            'commentable_id' => $game->id,
            'commentable_type' => $game::class,
        ]);

        $data = [
            'heartable_id' => $comment->id,
            'heartable_type' => $comment::class,
        ];

        $this->postJson(route('hearts.toggle'), $data)->assertOk();

        $this->assertDatabaseHas('heartables', [
            'user_id' => $this->user->id,
            'heartable_id' => $data['heartable_id'],
            'heartable_type' => $data['heartable_type'],
        ]);
    }

    /**
     * Test if can delete a heart if already exists for user.
     *
     * @return void
     */
    public function test_if_can_delete_a_heart_if_already_exists_for_user(): void
    {
        $game = $this->createDummyGame();

        $this->createDummyHeartable([
            'heartable_id' => $game->id,
            'user_id' => $this->user->id,
            'heartable_type' => $game::class,
        ]);

        $data = [
            'heartable_id' => $game->id,
            'heartable_type' => $game::class,
        ];

        $this->assertDatabaseHas('heartables', [
            'user_id' => $this->user->id,
            'heartable_type' => $game::class,
            'heartable_id' => $data['heartable_id'],
        ]);

        $this->postJson(route('hearts.toggle'), $data)->assertOk();

        $this->assertDatabaseMissing('heartables', [
            'user_id' => $this->user->id,
            'heartable_type' => $game::class,
            'heartable_id' => $data['heartable_id'],
        ]);
    }
}
