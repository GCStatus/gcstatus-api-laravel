<?php

namespace Tests\Feature\Http\Admin\Critic;

use App\Models\{Critic, User};
use Illuminate\Database\Eloquent\Collection;
use Tests\Feature\Http\BaseIntegrationTesting;
use Tests\Traits\{
    HasDummyCritic,
    HasDummyPermission,
};

class CriticIndexTest extends BaseIntegrationTesting
{
    use HasDummyCritic;
    use HasDummyPermission;

    /**
     * The dummy user.
     *
     * @var \App\Models\User
     */
    private User $user;

    /**
     * The dummy Critics.
     *
     * @var \Illuminate\Database\Eloquent\Collection<int, \App\Models\Critic>
     */
    private Collection $critics;

    /**
     * The required permissions for this action.
     *
     * @var list< string>
     */
    protected array $permissions = [
        'view:critics',
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

        $this->critics = $this->createDummyCritics(4);
    }

    /**
     * Test if can't see if is unauthenticated.
     *
     * @return void
     */
    public function test_if_cant_see_if_user_is_unauthenticated(): void
    {
        $this->authService->clearAuthenticationCookies();

        $this->getJson(route('critics.index'))
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

        $this->getJson(route('critics.index'))->assertNotFound();
    }

    /**
     * Test if can see Critics if has permissions.
     *
     * @return void
     */
    public function test_if_can_see_Critics_if_has_permissions(): void
    {
        $this->getJson(route('critics.index'))->assertOk();
    }

    /**
     * Test if can get correct json attributes count.
     *
     * @return void
     */
    public function test_if_can_get_correct_json_attributes_count(): void
    {
        $this->getJson(route('critics.index'))->assertOk()->assertJsonCount(4, 'data');
    }

    /**
     * Test if can get correct json structure.
     *
     * @return void
     */
    public function test_if_can_get_correct_json_structure(): void
    {
        $this->getJson(route('critics.index'))->assertOk()->assertJsonStructure([
            'data' => [
                '*' => [
                    'id',
                    'url',
                    'name',
                    'slug',
                    'logo',
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
        $this->getJson(route('critics.index'))->assertOk()->assertJson([
            'data' => $this->critics->map(function (Critic $critic) {
                return [
                    'id' => $critic->id,
                    'url' => $critic->url,
                    'name' => $critic->name,
                    'slug' => $critic->slug,
                    'logo' => $critic->logo,
                    'acting' => $critic->acting,
                    'created_at' => $critic->created_at?->toISOString(),
                    'updated_at' => $critic->updated_at?->toISOString(),
                ];
            })->toArray(),
        ]);
    }
}
