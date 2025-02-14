<?php

namespace Tests\Feature\Http\Admin;

use Mockery;
use App\Models\{Role, User};
use Database\Seeders\RoleSeeder;
use Illuminate\Support\Facades\Bus;
use Tests\Feature\Http\BaseIntegrationTesting;
use App\Contracts\Clients\SteamClientInterface;
use Tests\Traits\{
    HasDummyRole,
    HasDummyPermission,
};

class AdminScopeTest extends BaseIntegrationTesting
{
    use HasDummyRole;
    use HasDummyPermission;

    /**
     * The dummy user.
     *
     * @var \App\Models\User
     */
    private User $user;

    /**
     * The required permissions for this action.
     *
     * @var array<(int|string), mixed>
     */
    private array $permissions = [
        'view:games',
        'create:games',
        'create:steam-apps',
    ];

    /**
     * Setup new test environments.
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->seed([RoleSeeder::class]);

        $this->user = $this->actingAsDummyUser();

        $this->user->roles()->attach(Role::TECHNOLOGY_ROLE_ID);

        $fakeSteamClient = Mockery::mock(SteamClientInterface::class);

        $this->app->instance(SteamClientInterface::class, $fakeSteamClient);
    }

    /**
     * Test if can't act if is unauthenticated.
     *
     * @return void
     */
    public function test_if_cant_act_if_user_is_unauthenticated(): void
    {
        $this->authService->clearAuthenticationCookies();

        $postData = ['app_id' => '123'];

        $this->postJson(route('steam.apps.create'), $postData)
            ->assertUnauthorized()
            ->assertSee('We could not authenticate your user. Please, try to login again.');
    }

    /**
     * Test if can't act without required permissions.
     *
     * @return void
     */
    public function test_if_cant_act_without_required_permissions(): void
    {
        $this->actingAsDummyUser();

        $postData = ['app_id' => '123'];

        $this->postJson(route('steam.apps.create'), $postData)->assertNotFound();
    }

    /**
     * Test if can act with tech role.
     *
     * @return void
     */
    public function test_if_can_act_with_tech_role(): void
    {
        Bus::fake();

        $postData = ['app_id' => '123'];

        $this->postJson(route('steam.apps.create'), $postData)->assertOk();
    }

    /**
     * Test if can act having all required permissions to user.
     *
     * @return void
     */
    public function test_if_can_act_having_all_required_permissions_to_user(): void
    {
        Bus::fake();

        $user = $this->actingAsDummyUser();

        foreach ($this->permissions as $permission) {
            $permission = $this->createDummyPermission([
                'scope' => $permission,
            ]);

            $user->permissions()->save($permission);
        }

        $postData = ['app_id' => '123'];

        $this->postJson(route('steam.apps.create'), $postData)->assertOk();
    }

    /**
     * Test if can act having all required permissions to user role.
     *
     * @return void
     */
    public function test_if_can_act_having_all_required_permissions_to_user_role(): void
    {
        Bus::fake();

        $user = $this->actingAsDummyUser();

        $role = $this->createDummyRole();

        $user->roles()->save($role);

        foreach ($this->permissions as $permission) {
            $permission = $this->createDummyPermission([
                'scope' => $permission,
            ]);

            $role->permissions()->save($permission);
        }

        $postData = ['app_id' => '123'];

        $this->postJson(route('steam.apps.create'), $postData)->assertOk();
    }

    /**
     * Test if can act having partial permissions on user and remaining on role.
     *
     * @return void
     */
    public function test_if_can_act_having_partial_permissions_on_user_and_remaining_on_role(): void
    {
        Bus::fake();

        $user = $this->actingAsDummyUser();

        $role = $this->createDummyRole();

        $user->roles()->save($role);

        foreach ($this->permissions as $permission) {
            $permission = $this->createDummyPermission([
                'scope' => $permission,
            ]);

            $role->permissions()->save($permission);
        }

        $postData = ['app_id' => '123'];

        $this->postJson(route('steam.apps.create'), $postData)->assertOk();
    }

    /**
     * Tear down application tests.
     *
     * @return void
     */
    public function tearDown(): void
    {
        Mockery::close();

        parent::tearDown();
    }
}
