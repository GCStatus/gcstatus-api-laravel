<?php

namespace Tests\Feature\Http\Admin\Publisher;

use App\Models\{Publisher, User};
use Tests\Feature\Http\BaseIntegrationTesting;
use Tests\Traits\{
    HasDummyPublisher,
    HasDummyPermission,
};

class PublisherDestroyTest extends BaseIntegrationTesting
{
    use HasDummyPublisher;
    use HasDummyPermission;

    /**
     * The dummy user.
     *
     * @var \App\Models\User
     */
    private User $user;

    /**
     * The dummy Publisher.
     *
     * @var \App\Models\Publisher
     */
    private Publisher $publisher;

    /**
     * The required permissions for this action.
     *
     * @var list< string>
     */
    protected array $permissions = [
        'view:publishers',
        'delete:publishers',
    ];

    /**
     * Setup new test environments.
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->publisher = $this->createDummyPublisher();

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

        $this->deleteJson(route('publishers.destroy', $this->publisher))
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

        $this->deleteJson(route('publishers.destroy', $this->publisher))->assertNotFound();
    }

    /**
     * Test if can soft delete a publisher.
     *
     * @return void
     */
    public function test_if_can_soft_delete_a_publisher(): void
    {
        $this->assertNotSoftDeleted($this->publisher);

        $this->deleteJson(route('publishers.destroy', $this->publisher))->assertOk();

        $this->assertSoftDeleted($this->publisher);
    }
}
