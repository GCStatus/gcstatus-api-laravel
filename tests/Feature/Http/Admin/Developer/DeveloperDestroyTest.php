<?php

namespace Tests\Feature\Http\Admin\Developer;

use App\Models\{Developer, User};
use Tests\Feature\Http\BaseIntegrationTesting;
use Tests\Traits\{
    HasDummyDeveloper,
    HasDummyPermission,
};

class DeveloperDestroyTest extends BaseIntegrationTesting
{
    use HasDummyDeveloper;
    use HasDummyPermission;

    /**
     * The dummy user.
     *
     * @var \App\Models\User
     */
    private User $user;

    /**
     * The dummy Developer.
     *
     * @var \App\Models\Developer
     */
    private Developer $developer;

    /**
     * The required permissions for this action.
     *
     * @var list< string>
     */
    protected array $permissions = [
        'view:developers',
        'delete:developers',
    ];

    /**
     * Setup new test environments.
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->developer = $this->createDummyDeveloper();

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

        $this->deleteJson(route('developers.destroy', $this->developer))
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

        $this->deleteJson(route('developers.destroy', $this->developer))->assertNotFound();
    }

    /**
     * Test if can soft delete a developer.
     *
     * @return void
     */
    public function test_if_can_soft_delete_a_developer(): void
    {
        $this->assertNotSoftDeleted($this->developer);

        $this->deleteJson(route('developers.destroy', $this->developer))->assertOk();

        $this->assertSoftDeleted($this->developer);
    }
}
