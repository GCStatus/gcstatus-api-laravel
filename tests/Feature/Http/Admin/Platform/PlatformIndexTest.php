<?php

namespace Tests\Feature\Http\Admin\Tag;

use App\Models\{Platform, User};
use Illuminate\Database\Eloquent\Collection;
use Tests\Feature\Http\BaseIntegrationTesting;
use Tests\Traits\{
    HasDummyPlatform,
    HasDummyPermission,
};

class PlatformIndexTest extends BaseIntegrationTesting
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
     * The dummy platforms.
     *
     * @var \Illuminate\Database\Eloquent\Collection<int, \App\Models\Platform>
     */
    private Collection $platforms;

    /**
     * The required permissions for this action.
     *
     * @var list< string>
     */
    protected array $permissions = [
        'view:platforms',
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

        $this->platforms = $this->createDummyPlatforms(4);
    }

    /**
     * Test if can't see if is unauthenticated.
     *
     * @return void
     */
    public function test_if_cant_see_if_user_is_unauthenticated(): void
    {
        $this->authService->clearAuthenticationCookies();

        $this->getJson(route('platforms.index'))
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

        $this->getJson(route('platforms.index'))->assertNotFound();
    }

    /**
     * Test if can see platforms if has permissions.
     *
     * @return void
     */
    public function test_if_can_see_platforms_if_has_permissions(): void
    {
        $this->getJson(route('platforms.index'))->assertOk();
    }

    /**
     * Test if can get correct json attributes count.
     *
     * @return void
     */
    public function test_if_can_get_correct_json_attributes_count(): void
    {
        $this->getJson(route('platforms.index'))->assertOk()->assertJsonCount(4, 'data');
    }

    /**
     * Test if can get correct json structure.
     *
     * @return void
     */
    public function test_if_can_get_correct_json_structure(): void
    {
        $this->getJson(route('platforms.index'))->assertOk()->assertJsonStructure([
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
        $this->getJson(route('platforms.index'))->assertOk()->assertJson([
            'data' => $this->platforms->map(function (Platform $platform) {
                return [
                    'id' => $platform->id,
                    'name' => $platform->name,
                    'created_at' => $platform->created_at?->toISOString(),
                    'updated_at' => $platform->updated_at?->toISOString(),
                ];
            })->toArray(),
        ]);
    }
}
