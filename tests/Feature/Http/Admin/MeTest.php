<?php

namespace Tests\Feature\Http\Admin;

use App\Models\{Role, User, Permission};
use Tests\Feature\Http\BaseIntegrationTesting;
use Tests\Traits\{
    HasDummyRole,
    HasDummyPermission,
};

class MeTest extends BaseIntegrationTesting
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
     * Setup new test environments.
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->user = $this->actingAsDummyUser();

        $this->user->roles()->save(
            $role = $this->createDummyRole(),
        );
        $this->user->permissions()->save(
            $this->createDummyPermission(),
        );
        $role->permissions()->save(
            $this->createDummyPermission(),
        );
    }

    /**
     * Test if can't get me details if not authenticated.
     *
     * @return void
     */
    public function test_if_cant_get_me_details_if_not_authenticated(): void
    {
        $this->authService->clearAuthenticationCookies();

        $this->getJson(route('admin.me'))
            ->assertUnauthorized()
            ->assertSee('We could not authenticate your user. Please, try to login again.');
    }

    /**
     * Test if can send a get request and returns ok.
     *
     * @return void
     */
    public function test_if_can_send_a_get_request_and_returns_ok(): void
    {
        $this->getJson(route('admin.me'))->assertOk();
    }

    /**
     * Test if can get correctly me json attributes count.
     *
     * @return void
     */
    public function test_if_can_get_correctly_me_json_attributes_count(): void
    {
        $this->getJson(route('admin.me'))->assertOk()->assertJsonCount(8, 'data');
    }

    /**
     * Test if can get correctly me json structure.
     *
     * @return void
     */
    public function test_if_can_get_me_correctly_json_structure(): void
    {
        $this->getJson(route('admin.me'))->assertOk()->assertJsonStructure([
            'data' => [
                'id',
                'name',
                'email',
                'birthdate',
                'created_at',
                'updated_at',
                'roles' => [
                    '*' => [
                        'id',
                        'name',
                        'created_at',
                        'updated_at',
                        'permissions' => [
                            '*' => [
                                'id',
                                'scope',
                                'created_at',
                                'updated_at',
                            ],
                        ],
                    ],
                ],
                'permissions' => [
                    '*' => [
                        'id',
                        'scope',
                        'created_at',
                        'updated_at',
                    ],
                ],
            ],
        ]);
    }

    /**
     * Test if can get correctly me json data.
     *
     * @return void
     */
    public function test_if_can_get_correctly_me_json_data(): void
    {
        /** @var \Illuminate\Database\Eloquent\Collection<int, \App\Models\Role> $roles */
        $roles = $this->user->roles;

        /** @var \Illuminate\Database\Eloquent\Collection<int, \App\Models\Permission> $permissions */
        $permissions = $this->user->permissions;

        $this->getJson(route('admin.me'))->assertOk()->assertJson([
            'data' => [
                'id' => $this->user->id,
                'name' => $this->user->name,
                'email' => $this->user->email,
                'birthdate' => $this->user->birthdate,
                'created_at' => $this->user->created_at?->toISOString(),
                'updated_at' => $this->user->updated_at?->toISOString(),
                'roles' => $roles->map(function (Role $role) {
                    /** @var \Illuminate\Database\Eloquent\Collection<int, \App\Models\Permission> $permissions */
                    $permissions = $role->permissions;

                    return [
                        'id' => $role->id,
                        'name' => $role->name,
                        'created_at' => $role->created_at?->toISOString(),
                        'updated_at' => $role->updated_at?->toISOString(),
                        'permissions' => $permissions->map(function (Permission $permission) {
                            return [
                                'id' => $permission->id,
                                'scope' => $permission->scope,
                                'created_at' => $permission->created_at?->toISOString(),
                                'updated_at' => $permission->updated_at?->toISOString(),
                            ];
                        })->toArray(),
                    ];
                })->toArray(),
                'permissions' => $permissions->map(function (Permission $permission) {
                    return [
                        'id' => $permission->id,
                        'scope' => $permission->scope,
                        'created_at' => $permission->created_at?->toISOString(),
                        'updated_at' => $permission->updated_at?->toISOString(),
                    ];
                })->toArray(),
            ]
        ]);
    }
}
