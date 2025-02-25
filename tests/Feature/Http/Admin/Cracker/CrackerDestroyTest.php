<?php

namespace Tests\Feature\Http\Admin\Cracker;

use App\Models\{Cracker, User};
use Tests\Feature\Http\BaseIntegrationTesting;
use Tests\Traits\{
    HasDummyCracker,
    HasDummyPermission,
};

class CrackerDestroyTest extends BaseIntegrationTesting
{
    use HasDummyCracker;
    use HasDummyPermission;

    /**
     * The dummy user.
     *
     * @var \App\Models\User
     */
    private User $user;

    /**
     * The dummy cracker.
     *
     * @var \App\Models\Cracker
     */
    private Cracker $cracker;

    /**
     * The required permissions for this action.
     *
     * @var list< string>
     */
    protected array $permissions = [
        'view:crackers',
        'delete:crackers',
    ];

    /**
     * Setup new test environments.
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->cracker = $this->createDummyCracker();

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

        $this->deleteJson(route('crackers.destroy', $this->cracker))
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

        $this->deleteJson(route('crackers.destroy', $this->cracker))->assertNotFound();
    }

    /**
     * Test if can soft delete a cracker.
     *
     * @return void
     */
    public function test_if_can_soft_delete_a_cracker(): void
    {
        $this->assertNotSoftDeleted($this->cracker);

        $this->deleteJson(route('crackers.destroy', $this->cracker))->assertOk();

        $this->assertSoftDeleted($this->cracker);
    }
}
