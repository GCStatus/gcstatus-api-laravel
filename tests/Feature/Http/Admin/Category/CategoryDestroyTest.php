<?php

namespace Tests\Feature\Http\Admin\Category;

use App\Models\{Category, User};
use Tests\Feature\Http\BaseIntegrationTesting;
use Tests\Traits\{
    HasDummyCategory,
    HasDummyPermission,
};

class CategoryDestroyTest extends BaseIntegrationTesting
{
    use HasDummyCategory;
    use HasDummyPermission;

    /**
     * The dummy user.
     *
     * @var \App\Models\User
     */
    private User $user;

    /**
     * The dummy Category.
     *
     * @var \App\Models\Category
     */
    private Category $category;

    /**
     * The required permissions for this action.
     *
     * @var list< string>
     */
    protected array $permissions = [
        'view:categories',
        'delete:categories',
    ];

    /**
     * Setup new test environments.
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->category = $this->createDummyCategory();

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

        $this->deleteJson(route('categories.destroy', $this->category))
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

        $this->deleteJson(route('categories.destroy', $this->category))->assertNotFound();
    }

    /**
     * Test if can soft delete a category.
     *
     * @return void
     */
    public function test_if_can_soft_delete_a_category(): void
    {
        $this->assertNotSoftDeleted($this->category);

        $this->deleteJson(route('categories.destroy', $this->category))->assertOk();

        $this->assertSoftDeleted($this->category);
    }
}
