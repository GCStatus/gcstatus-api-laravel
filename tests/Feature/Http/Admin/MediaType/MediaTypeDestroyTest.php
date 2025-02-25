<?php

namespace Tests\Feature\Http\Admin\MediaType;

use App\Models\{MediaType, User};
use Tests\Feature\Http\BaseIntegrationTesting;
use Tests\Traits\{
    HasDummyMediaType,
    HasDummyPermission,
};

class MediaTypeDestroyTest extends BaseIntegrationTesting
{
    use HasDummyMediaType;
    use HasDummyPermission;

    /**
     * The dummy user.
     *
     * @var \App\Models\User
     */
    private User $user;

    /**
     * The dummy mediaType.
     *
     * @var \App\Models\MediaType
     */
    private MediaType $mediaType;

    /**
     * The required permissions for this action.
     *
     * @var list< string>
     */
    protected array $permissions = [
        'view:media-types',
        'delete:media-types',
    ];

    /**
     * Setup new test environments.
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->mediaType = $this->createDummyMediaType();

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

        $this->deleteJson(route('media-types.destroy', $this->mediaType))
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

        $this->deleteJson(route('media-types.destroy', $this->mediaType))->assertNotFound();
    }

    /**
     * Test if can soft delete a media type.
     *
     * @return void
     */
    public function test_if_can_soft_delete_a_media_type(): void
    {
        $this->assertNotSoftDeleted($this->mediaType);

        $this->deleteJson(route('media-types.destroy', $this->mediaType))->assertOk();

        $this->assertSoftDeleted($this->mediaType);
    }
}
