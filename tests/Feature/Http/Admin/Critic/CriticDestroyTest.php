<?php

namespace Tests\Feature\Http\Admin\Critic;

use App\Models\{Critic, User};
use Tests\Feature\Http\BaseIntegrationTesting;
use Tests\Traits\{
    HasDummyCritic,
    HasDummyPermission,
};

class CriticDestroyTest extends BaseIntegrationTesting
{
    use HasDummyCritic;
    use HasDummyPermission;

    /**
     * The dummy user.
     *
     * @var \App\Models\User
     */
    private User $user;

    /**
     * The dummy critic.
     *
     * @var \App\Models\Critic
     */
    private Critic $critic;

    /**
     * The required permissions for this action.
     *
     * @var list< string>
     */
    protected array $permissions = [
        'view:critics',
        'delete:critics',
    ];

    /**
     * Setup new test environments.
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->critic = $this->createDummyCritic();

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

        $this->deleteJson(route('critics.destroy', $this->critic))
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

        $this->deleteJson(route('critics.destroy', $this->critic))->assertNotFound();
    }

    /**
     * Test if can soft delete a critic.
     *
     * @return void
     */
    public function test_if_can_soft_delete_a_critic(): void
    {
        $this->assertNotSoftDeleted($this->critic);

        $this->deleteJson(route('critics.destroy', $this->critic))->assertOk();

        $this->assertSoftDeleted($this->critic);
    }
}
