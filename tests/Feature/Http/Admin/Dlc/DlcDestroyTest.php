<?php

namespace Tests\Feature\Http\Admin\Dlc;

use App\Models\{Dlc, User};
use Tests\Feature\Http\BaseIntegrationTesting;
use Tests\Traits\{
    HasDummyDlc,
    HasDummyPermission,
};

class DlcDestroyTest extends BaseIntegrationTesting
{
    use HasDummyDlc;
    use HasDummyPermission;

    /**
     * The dummy user.
     *
     * @var \App\Models\User
     */
    private User $user;

    /**
     * The dummy Dlc.
     *
     * @var \App\Models\Dlc
     */
    private Dlc $dlc;

    /**
     * The required permissions for this action.
     *
     * @var list< string>
     */
    protected array $permissions = [
        'view:dlcs',
        'delete:dlcs',
    ];

    /**
     * Setup new test environments.
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->dlc = $this->createDummyDlc();

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

        $this->deleteJson(route('dlcs.destroy', $this->dlc))
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

        $this->deleteJson(route('dlcs.destroy', $this->dlc))->assertNotFound();
    }

    /**
     * Test if can soft delete a dlc.
     *
     * @return void
     */
    public function test_if_can_soft_delete_a_dlc(): void
    {
        $this->assertNotSoftDeleted($this->dlc);

        $this->deleteJson(route('dlcs.destroy', $this->dlc))->assertOk();

        $this->assertSoftDeleted($this->dlc);
    }
}
