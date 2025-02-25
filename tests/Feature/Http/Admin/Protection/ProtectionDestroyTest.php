<?php

namespace Tests\Feature\Http\Admin\Protection;

use App\Models\{Protection, User};
use Tests\Feature\Http\BaseIntegrationTesting;
use Tests\Traits\{
    HasDummyProtection,
    HasDummyPermission,
};

class ProtectionDestroyTest extends BaseIntegrationTesting
{
    use HasDummyProtection;
    use HasDummyPermission;

    /**
     * The dummy user.
     *
     * @var \App\Models\User
     */
    private User $user;

    /**
     * The dummy protection.
     *
     * @var \App\Models\Protection
     */
    private Protection $protection;

    /**
     * The required permissions for this action.
     *
     * @var list< string>
     */
    protected array $permissions = [
        'view:protections',
        'delete:protections',
    ];

    /**
     * Setup new test environments.
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->protection = $this->createDummyProtection();

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

        $this->deleteJson(route('protections.destroy', $this->protection))
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

        $this->deleteJson(route('protections.destroy', $this->protection))->assertNotFound();
    }

    /**
     * Test if can soft delete a protection.
     *
     * @return void
     */
    public function test_if_can_soft_delete_a_protection(): void
    {
        $this->assertNotSoftDeleted($this->protection);

        $this->deleteJson(route('protections.destroy', $this->protection))->assertOk();

        $this->assertSoftDeleted($this->protection);
    }
}
