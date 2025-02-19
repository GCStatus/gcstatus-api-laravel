<?php

namespace Tests\Feature\Http\Admin\Tag;

use App\Models\{Developer, User};
use Illuminate\Database\Eloquent\Collection;
use Tests\Feature\Http\BaseIntegrationTesting;
use Tests\Traits\{
    HasDummyDeveloper,
    HasDummyPermission,
};

class DeveloperIndexTest extends BaseIntegrationTesting
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
     * The dummy developers.
     *
     * @var \Illuminate\Database\Eloquent\Collection<int, \App\Models\Developer>
     */
    private Collection $developers;

    /**
     * The required permissions for this action.
     *
     * @var list< string>
     */
    protected array $permissions = [
        'view:developers',
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

        $this->developers = $this->createDummyDevelopers(4);
    }

    /**
     * Test if can't see if is unauthenticated.
     *
     * @return void
     */
    public function test_if_cant_see_if_user_is_unauthenticated(): void
    {
        $this->authService->clearAuthenticationCookies();

        $this->getJson(route('developers.index'))
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

        $this->getJson(route('developers.index'))->assertNotFound();
    }

    /**
     * Test if can see developers if has permissions.
     *
     * @return void
     */
    public function test_if_can_see_developers_if_has_permissions(): void
    {
        $this->getJson(route('developers.index'))->assertOk();
    }

    /**
     * Test if can get correct json attributes count.
     *
     * @return void
     */
    public function test_if_can_get_correct_json_attributes_count(): void
    {
        $this->getJson(route('developers.index'))->assertOk()->assertJsonCount(4, 'data');
    }

    /**
     * Test if can get correct json structure.
     *
     * @return void
     */
    public function test_if_can_get_correct_json_structure(): void
    {
        $this->getJson(route('developers.index'))->assertOk()->assertJsonStructure([
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
        $this->getJson(route('developers.index'))->assertOk()->assertJson([
            'data' => $this->developers->map(function (Developer $developer) {
                return [
                    'id' => $developer->id,
                    'name' => $developer->name,
                    'created_at' => $developer->created_at?->toISOString(),
                    'updated_at' => $developer->updated_at?->toISOString(),
                ];
            })->toArray(),
        ]);
    }
}
