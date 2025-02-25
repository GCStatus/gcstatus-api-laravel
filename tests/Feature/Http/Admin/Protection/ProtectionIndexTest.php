<?php

namespace Tests\Feature\Http\Admin\Protection;

use App\Models\{Protection, User};
use Illuminate\Database\Eloquent\Collection;
use Tests\Feature\Http\BaseIntegrationTesting;
use Tests\Traits\{
    HasDummyProtection,
    HasDummyPermission,
};

class ProtectionIndexTest extends BaseIntegrationTesting
{
    use HasDummyProtection;
    use HasDummyPermission;

    /**
     * The dummy user.
     *
     * @var \App\Models\User
     */
    private User $user;

    /**
     * The dummy protections.
     *
     * @var \Illuminate\Database\Eloquent\Collection<int, \App\Models\Protection>
     */
    private Collection $protections;

    /**
     * The required permissions for this action.
     *
     * @var list< string>
     */
    protected array $permissions = [
        'view:protections',
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

        $this->protections = $this->createDummyProtections(4);
    }

    /**
     * Test if can't see if is unauthenticated.
     *
     * @return void
     */
    public function test_if_cant_see_if_user_is_unauthenticated(): void
    {
        $this->authService->clearAuthenticationCookies();

        $this->getJson(route('protections.index'))
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

        $this->getJson(route('protections.index'))->assertNotFound();
    }

    /**
     * Test if can see protections if has permissions.
     *
     * @return void
     */
    public function test_if_can_see_protections_if_has_permissions(): void
    {
        $this->getJson(route('protections.index'))->assertOk();
    }

    /**
     * Test if can get correct json attributes count.
     *
     * @return void
     */
    public function test_if_can_get_correct_json_attributes_count(): void
    {
        $this->getJson(route('protections.index'))->assertOk()->assertJsonCount(4, 'data');
    }

    /**
     * Test if can get correct json structure.
     *
     * @return void
     */
    public function test_if_can_get_correct_json_structure(): void
    {
        $this->getJson(route('protections.index'))->assertOk()->assertJsonStructure([
            'data' => [
                '*' => [
                    'id',
                    'name',
                    'slug',
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
        $this->getJson(route('protections.index'))->assertOk()->assertJson([
            'data' => $this->protections->map(function (Protection $protection) {
                return [
                    'id' => $protection->id,
                    'name' => $protection->name,
                    'slug' => $protection->slug,
                    'created_at' => $protection->created_at?->toISOString(),
                    'updated_at' => $protection->updated_at?->toISOString(),
                ];
            })->toArray(),
        ]);
    }
}
