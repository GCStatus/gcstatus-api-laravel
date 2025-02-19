<?php

namespace Tests\Feature\Http\Admin\Platform;

use App\Models\{Platform, User};
use Tests\Feature\Http\BaseIntegrationTesting;
use Tests\Traits\{
    HasDummyPlatform,
    HasDummyPermission,
};

class platformDestroyTest extends BaseIntegrationTesting
{
    use HasDummyPlatform;
    use HasDummyPermission;

    /**
     * The dummy user.
     *
     * @var \App\Models\User
     */
    private User $user;

    /**
     * The dummy platform.
     *
     * @var \App\Models\Platform
     */
    private Platform $platform;

    /**
     * The required permissions for this action.
     *
     * @var list< string>
     */
    protected array $permissions = [
        'view:platforms',
        'delete:platforms',
    ];

    /**
     * Setup new test environments.
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->platform = $this->createDummyPlatform();

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

        $this->deleteJson(route('platforms.destroy', $this->platform))
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

        $this->deleteJson(route('platforms.destroy', $this->platform))->assertNotFound();
    }

    /**
     * Test if can soft delete a platform.
     *
     * @return void
     */
    public function test_if_can_soft_delete_a_platform(): void
    {
        $this->assertNotSoftDeleted($this->platform);

        $this->deleteJson(route('platforms.destroy', $this->platform))->assertOk();

        $this->assertSoftDeleted($this->platform);
    }
}
