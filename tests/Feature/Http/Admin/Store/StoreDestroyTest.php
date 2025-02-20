<?php

namespace Tests\Feature\Http\Admin\Store;

use App\Models\{Store, User};
use Tests\Feature\Http\BaseIntegrationTesting;
use Tests\Traits\{
    HasDummyStore,
    HasDummyPermission,
};

class StoreDestroyTest extends BaseIntegrationTesting
{
    use HasDummyStore;
    use HasDummyPermission;

    /**
     * The dummy user.
     *
     * @var \App\Models\User
     */
    private User $user;

    /**
     * The dummy store.
     *
     * @var \App\Models\Store
     */
    private Store $store;

    /**
     * The required permissions for this action.
     *
     * @var list< string>
     */
    protected array $permissions = [
        'view:stores',
        'delete:stores',
    ];

    /**
     * Setup new test environments.
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->store = $this->createDummyStore();

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

        $this->deleteJson(route('stores.destroy', $this->store))
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

        $this->deleteJson(route('stores.destroy', $this->store))->assertNotFound();
    }

    /**
     * Test if can soft delete a store.
     *
     * @return void
     */
    public function test_if_can_soft_delete_a_store(): void
    {
        $this->assertNotSoftDeleted($this->store);

        $this->deleteJson(route('stores.destroy', $this->store))->assertOk();

        $this->assertSoftDeleted($this->store);
    }
}
