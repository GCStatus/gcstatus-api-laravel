<?php

namespace Tests\Feature\Http\Admin\RequirementType;

use App\Models\{RequirementType, User};
use Tests\Feature\Http\BaseIntegrationTesting;
use Tests\Traits\{
    HasDummyPermission,
    HasDummyRequirementType,
};

class RequirementTypeDestroyTest extends BaseIntegrationTesting
{
    use HasDummyPermission;
    use HasDummyRequirementType;

    /**
     * The dummy user.
     *
     * @var \App\Models\User
     */
    private User $user;

    /**
     * The dummy requirement type.
     *
     * @var \App\Models\RequirementType
     */
    private RequirementType $requirementType;

    /**
     * The required permissions for this action.
     *
     * @var list< string>
     */
    protected array $permissions = [
        'view:requirement-types',
        'delete:requirement-types',
    ];

    /**
     * Setup new test environments.
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->requirementType = $this->createDummyRequirementType();

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

        $this->deleteJson(route('requirement-types.destroy', $this->requirementType))
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

        $this->deleteJson(route('requirement-types.destroy', $this->requirementType))->assertNotFound();
    }

    /**
     * Test if can soft delete a torrent provider.
     *
     * @return void
     */
    public function test_if_can_soft_delete_a_torrent_provider(): void
    {
        $this->assertNotSoftDeleted($this->requirementType);

        $this->deleteJson(route('requirement-types.destroy', $this->requirementType))->assertOk();

        $this->assertSoftDeleted($this->requirementType);
    }
}
