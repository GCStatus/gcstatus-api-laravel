<?php

namespace Tests\Feature\Http\Admin\Store;

use App\Models\{Store, User};
use Illuminate\Database\Eloquent\Collection;
use Tests\Feature\Http\BaseIntegrationTesting;
use Tests\Traits\{
    HasDummyStore,
    HasDummyPermission,
};

class StoreIndexTest extends BaseIntegrationTesting
{
    use HasDummyStore;
    use HasDummyPermission;

    /**
     * The dummy user.
     *
     * @var \App\Models\User
     */
    private User $user;

    /**
     * The dummy stores.
     *
     * @var \Illuminate\Database\Eloquent\Collection<int, \App\Models\Store>
     */
    private Collection $stores;

    /**
     * The required permissions for this action.
     *
     * @var list< string>
     */
    protected array $permissions = [
        'view:stores',
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

        $this->stores = $this->createDummyStores(4);
    }

    /**
     * Test if can't see if is unauthenticated.
     *
     * @return void
     */
    public function test_if_cant_see_if_user_is_unauthenticated(): void
    {
        $this->authService->clearAuthenticationCookies();

        $this->getJson(route('stores.index'))
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

        $this->getJson(route('stores.index'))->assertNotFound();
    }

    /**
     * Test if can see stores if has permissions.
     *
     * @return void
     */
    public function test_if_can_see_stores_if_has_permissions(): void
    {
        $this->getJson(route('stores.index'))->assertOk();
    }

    /**
     * Test if can get correct json attributes count.
     *
     * @return void
     */
    public function test_if_can_get_correct_json_attributes_count(): void
    {
        $this->getJson(route('stores.index'))->assertOk()->assertJsonCount(4, 'data');
    }

    /**
     * Test if can get correct json structure.
     *
     * @return void
     */
    public function test_if_can_get_correct_json_structure(): void
    {
        $this->getJson(route('stores.index'))->assertOk()->assertJsonStructure([
            'data' => [
                '*' => [
                    'id',
                    'url',
                    'name',
                    'slug',
                    'logo',
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
        $this->getJson(route('stores.index'))->assertOk()->assertJson([
            'data' => $this->stores->map(function (Store $Store) {
                return [
                    'id' => $Store->id,
                    'url' => $Store->url,
                    'name' => $Store->name,
                    'slug' => $Store->slug,
                    'logo' => $Store->logo,
                    'created_at' => $Store->created_at?->toISOString(),
                    'updated_at' => $Store->updated_at?->toISOString(),
                ];
            })->toArray(),
        ]);
    }
}
