<?php

namespace Tests\Feature\Http\Admin\MediaType;

use App\Models\{MediaType, User};
use Illuminate\Database\Eloquent\Collection;
use Tests\Feature\Http\BaseIntegrationTesting;
use Tests\Traits\{
    HasDummyMediaType,
    HasDummyPermission,
};

class MediaTypeIndexTest extends BaseIntegrationTesting
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
     * The dummy media types.
     *
     * @var \Illuminate\Database\Eloquent\Collection<int, \App\Models\MediaType>
     */
    private Collection $mediaTypes;

    /**
     * The required permissions for this action.
     *
     * @var list< string>
     */
    protected array $permissions = [
        'view:media-types',
    ];

    /**
     * Setup new test environments.
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->user = $this->actingAsDummyUser();

        $this->bootUserPermissions($this->user);

        MediaType::all()->each(fn (MediaType $m) => $m->delete());

        $this->mediaTypes = $this->createDummyMediaTypes(4);
    }

    /**
     * Test if can't see if is unauthenticated.
     *
     * @return void
     */
    public function test_if_cant_see_if_user_is_unauthenticated(): void
    {
        $this->authService->clearAuthenticationCookies();

        $this->getJson(route('media-types.index'))
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

        $this->getJson(route('media-types.index'))->assertNotFound();
    }

    /**
     * Test if can see MediaTypes if has permissions.
     *
     * @return void
     */
    public function test_if_can_see_MediaTypes_if_has_permissions(): void
    {
        $this->getJson(route('media-types.index'))->assertOk();
    }

    /**
     * Test if can get correct json attributes count.
     *
     * @return void
     */
    public function test_if_can_get_correct_json_attributes_count(): void
    {
        $this->getJson(route('media-types.index'))->assertOk()->assertJsonCount(4, 'data');
    }

    /**
     * Test if can get correct json structure.
     *
     * @return void
     */
    public function test_if_can_get_correct_json_structure(): void
    {
        $this->getJson(route('media-types.index'))->assertOk()->assertJsonStructure([
            'data' => [
                '*' => [
                    'id',
                    'name',
                    'created_at',
                    'updated_at',
                ],
            ],
        ]);
    }

    /**
     * Test if can get correct json data.
     *
     * @return void
     */
    public function test_if_can_get_correct_json_data(): void
    {
        $this->getJson(route('media-types.index'))->assertOk()->assertJson([
            'data' => $this->mediaTypes->map(function (MediaType $mediaType) {
                return [
                    'id' => $mediaType->id,
                    'name' => $mediaType->name,
                    'created_at' => $mediaType->created_at?->toISOString(),
                    'updated_at' => $mediaType->updated_at?->toISOString(),
                ];
            })->toArray(),
        ]);
    }
}
