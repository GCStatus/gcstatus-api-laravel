<?php

namespace Tests\Feature\Http\Admin\Publisher;

use App\Models\{Publisher, User};
use Illuminate\Database\Eloquent\Collection;
use Tests\Feature\Http\BaseIntegrationTesting;
use Tests\Traits\{
    HasDummyPublisher,
    HasDummyPermission,
};

class PublisherIndexTest extends BaseIntegrationTesting
{
    use HasDummyPublisher;
    use HasDummyPermission;

    /**
     * The dummy user.
     *
     * @var \App\Models\User
     */
    private User $user;

    /**
     * The dummy publishers.
     *
     * @var \Illuminate\Database\Eloquent\Collection<int, \App\Models\Publisher>
     */
    private Collection $publishers;

    /**
     * The required permissions for this action.
     *
     * @var list< string>
     */
    protected array $permissions = [
        'view:publishers',
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

        $this->publishers = $this->createDummyPublishers(4);
    }

    /**
     * Test if can't see if is unauthenticated.
     *
     * @return void
     */
    public function test_if_cant_see_if_user_is_unauthenticated(): void
    {
        $this->authService->clearAuthenticationCookies();

        $this->getJson(route('publishers.index'))
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

        $this->getJson(route('publishers.index'))->assertNotFound();
    }

    /**
     * Test if can see publishers if has permissions.
     *
     * @return void
     */
    public function test_if_can_see_publishers_if_has_permissions(): void
    {
        $this->getJson(route('publishers.index'))->assertOk();
    }

    /**
     * Test if can get correct json attributes count.
     *
     * @return void
     */
    public function test_if_can_get_correct_json_attributes_count(): void
    {
        $this->getJson(route('publishers.index'))->assertOk()->assertJsonCount(4, 'data');
    }

    /**
     * Test if can get correct json structure.
     *
     * @return void
     */
    public function test_if_can_get_correct_json_structure(): void
    {
        $this->getJson(route('publishers.index'))->assertOk()->assertJsonStructure([
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
        $this->getJson(route('publishers.index'))->assertOk()->assertJson([
            'data' => $this->publishers->map(function (Publisher $publisher) {
                return [
                    'id' => $publisher->id,
                    'name' => $publisher->name,
                    'created_at' => $publisher->created_at?->toISOString(),
                    'updated_at' => $publisher->updated_at?->toISOString(),
                ];
            })->toArray(),
        ]);
    }
}
