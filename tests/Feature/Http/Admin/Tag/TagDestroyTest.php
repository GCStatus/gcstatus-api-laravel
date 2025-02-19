<?php

namespace Tests\Feature\Http\Admin\Tag;

use App\Models\{Tag, User};
use Tests\Feature\Http\BaseIntegrationTesting;
use Tests\Traits\{
    HasDummyTag,
    HasDummyPermission,
};

class TagDestroyTest extends BaseIntegrationTesting
{
    use HasDummyTag;
    use HasDummyPermission;

    /**
     * The dummy user.
     *
     * @var \App\Models\User
     */
    private User $user;

    /**
     * The dummy tag.
     *
     * @var \App\Models\Tag
     */
    private Tag $tag;

    /**
     * The required permissions for this action.
     *
     * @var list< string>
     */
    protected array $permissions = [
        'view:tags',
        'delete:tags',
    ];

    /**
     * Setup new test environments.
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->tag = $this->createDummyTag();

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

        $this->deleteJson(route('tags.destroy', $this->tag))
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

        $this->deleteJson(route('tags.destroy', $this->tag))->assertNotFound();
    }

    /**
     * Test if can soft delete a tag.
     *
     * @return void
     */
    public function test_if_can_soft_delete_a_tag(): void
    {
        $this->assertNotSoftDeleted($this->tag);

        $this->deleteJson(route('tags.destroy', $this->tag))->assertOk();

        $this->assertSoftDeleted($this->tag);
    }
}
