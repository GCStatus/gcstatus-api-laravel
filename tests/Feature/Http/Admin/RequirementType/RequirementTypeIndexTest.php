<?php

namespace Tests\Feature\Http\Admin\RequirementType;

use App\Models\{RequirementType, User};
use Illuminate\Database\Eloquent\Collection;
use Tests\Feature\Http\BaseIntegrationTesting;
use Tests\Traits\{
    HasDummyPermission,
    HasDummyRequirementType,
};

class RequirementTypeIndexTest extends BaseIntegrationTesting
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
     * The dummy RequirementTypes.
     *
     * @var \Illuminate\Database\Eloquent\Collection<int, \App\Models\RequirementType>
     */
    private Collection $RequirementTypes;

    /**
     * The required permissions for this action.
     *
     * @var list< string>
     */
    protected array $permissions = [
        'view:requirement-types',
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

        RequirementType::all()->each(fn (RequirementType $t) => $t->delete());

        $this->RequirementTypes = $this->createDummyRequirementTypes(4);
    }

    /**
     * Test if can't see if is unauthenticated.
     *
     * @return void
     */
    public function test_if_cant_see_if_user_is_unauthenticated(): void
    {
        $this->authService->clearAuthenticationCookies();

        $this->getJson(route('requirement-types.index'))
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

        $this->getJson(route('requirement-types.index'))->assertNotFound();
    }

    /**
     * Test if can see RequirementTypes if has permissions.
     *
     * @return void
     */
    public function test_if_can_see_RequirementTypes_if_has_permissions(): void
    {
        $this->getJson(route('requirement-types.index'))->assertOk();
    }

    /**
     * Test if can get correct json attributes count.
     *
     * @return void
     */
    public function test_if_can_get_correct_json_attributes_count(): void
    {
        $this->getJson(route('requirement-types.index'))->assertOk()->assertJsonCount(4, 'data');
    }

    /**
     * Test if can get correct json structure.
     *
     * @return void
     */
    public function test_if_can_get_correct_json_structure(): void
    {
        $this->getJson(route('requirement-types.index'))->assertOk()->assertJsonStructure([
            'data' => [
                '*' => [
                    'id',
                    'os',
                    'potential',
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
        $this->getJson(route('requirement-types.index'))->assertOk()->assertJson([
            'data' => $this->RequirementTypes->map(function (RequirementType $RequirementType) {
                return [
                    'id' => $RequirementType->id,
                    'os' => $RequirementType->os,
                    'potential' => $RequirementType->potential,
                    'created_at' => $RequirementType->created_at?->toISOString(),
                    'updated_at' => $RequirementType->updated_at?->toISOString(),
                ];
            })->toArray(),
        ]);
    }
}
