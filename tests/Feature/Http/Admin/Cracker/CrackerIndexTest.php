<?php

namespace Tests\Feature\Http\Admin\Cracker;

use App\Models\{Cracker, User};
use Illuminate\Database\Eloquent\Collection;
use Tests\Feature\Http\BaseIntegrationTesting;
use Tests\Traits\{
    HasDummyCracker,
    HasDummyPermission,
};

class CrackerIndexTest extends BaseIntegrationTesting
{
    use HasDummyCracker;
    use HasDummyPermission;

    /**
     * The dummy user.
     *
     * @var \App\Models\User
     */
    private User $user;

    /**
     * The dummy Crackers.
     *
     * @var \Illuminate\Database\Eloquent\Collection<int, \App\Models\Cracker>
     */
    private Collection $crackers;

    /**
     * The required permissions for this action.
     *
     * @var list< string>
     */
    protected array $permissions = [
        'view:crackers',
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

        $this->crackers = $this->createDummyCrackers(4);
    }

    /**
     * Test if can't see if is unauthenticated.
     *
     * @return void
     */
    public function test_if_cant_see_if_user_is_unauthenticated(): void
    {
        $this->authService->clearAuthenticationCookies();

        $this->getJson(route('crackers.index'))
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

        $this->getJson(route('crackers.index'))->assertNotFound();
    }

    /**
     * Test if can see Crackers if has permissions.
     *
     * @return void
     */
    public function test_if_can_see_Crackers_if_has_permissions(): void
    {
        $this->getJson(route('crackers.index'))->assertOk();
    }

    /**
     * Test if can get correct json attributes count.
     *
     * @return void
     */
    public function test_if_can_get_correct_json_attributes_count(): void
    {
        $this->getJson(route('crackers.index'))->assertOk()->assertJsonCount(4, 'data');
    }

    /**
     * Test if can get correct json structure.
     *
     * @return void
     */
    public function test_if_can_get_correct_json_structure(): void
    {
        $this->getJson(route('crackers.index'))->assertOk()->assertJsonStructure([
            'data' => [
                '*' => [
                    'id',
                    'name',
                    'acting',
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
        $this->getJson(route('crackers.index'))->assertOk()->assertJson([
            'data' => $this->crackers->map(function (Cracker $cracker) {
                return [
                    'id' => $cracker->id,
                    'name' => $cracker->name,
                    'acting' => $cracker->acting,
                    'created_at' => $cracker->created_at?->toISOString(),
                    'updated_at' => $cracker->updated_at?->toISOString(),
                ];
            })->toArray(),
        ]);
    }
}
