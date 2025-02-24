<?php

namespace Tests\Feature\Http\Admin\Game;

use App\Models\{Game, User};
use Tests\Feature\Http\BaseIntegrationTesting;
use Tests\Traits\{
    HasDummyGame,
    HasDummyPermission,
};

class GameDestroyTest extends BaseIntegrationTesting
{
    use HasDummyGame;
    use HasDummyPermission;

    /**
     * The dummy user.
     *
     * @var \App\Models\User
     */
    private User $user;

    /**
     * The dummy Game.
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
        'delete:games',
    ];

    /**
     * Setup new test environments.
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->game = $this->createDummyGame();

        $this->user = $this->actingAsDummyUser();

        $this->bootUserPermissions($this->user);
    }

    /**
     * Test if can't see if is unauthenticated.
     *
     * @return void
     */
    public function test_if_cant_see_if_user_is_unauthenticated(): void
    {
        $this->authService->clearAuthenticationCookies();

        $this->deleteJson(route('admin.games.destroy', $this->game))
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

        $this->deleteJson(route('admin.games.destroy', $this->game))->assertNotFound();
    }

    /**
     * Test if can soft delete a game.
     *
     * @return void
     */
    public function test_if_can_soft_delete_a_game(): void
    {
        $this->assertNotSoftDeleted($this->game);

        $this->deleteJson(route('admin.games.destroy', $this->game))->assertOk();

        $this->assertSoftDeleted($this->game);
    }
}
