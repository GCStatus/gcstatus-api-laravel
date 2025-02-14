<?php

namespace Tests\Unit\Traits;

use Mockery;
use Tests\TestCase;
use App\Models\User;

class HasPermissionsTest extends TestCase
{
    /**
     * Setup a User with fake permissions and role-based permissions.
     *
     * @param array<int, mixed> $directPermissionsScopes
     * @param array<int, mixed> $rolePermissionsScopes
     * @return \App\Models\User
     */
    protected function setupUser(array $directPermissionsScopes = [], array $rolePermissionsScopes = []): User
    {
        $user = Mockery::mock(User::class)->makePartial();

        $user->shouldReceive('loadMissing')->with(['permissions', 'roles.permissions'])->andReturnSelf();

        $user->permissions = collect(array_map(function (string $scope): object { // @phpstan-ignore-line
            return (object)['scope' => $scope];
        }, $directPermissionsScopes));

        $user->roles = collect(array_map(function (array $scopes): object { // @phpstan-ignore-line
            return (object)[
                'permissions' => collect(array_map(function (string $scope): object {
                    return (object)['scope' => $scope];
                }, $scopes))
            ];
        }, $rolePermissionsScopes));

        /** @var \App\Models\User $user */
        return $user;
    }

    /**
     * Test if hasPermission returns true when scope exists directly.
     *
     * @return void
     */
    public function test_if_hasPermission_returns_true_when_scope_exists_directly(): void
    {
        $user = $this->setupUser(['admin', 'editor']);

        $this->assertTrue($user->hasPermission('admin'));
        $this->assertTrue($user->hasPermission('editor'));
        $this->assertFalse($user->hasPermission('moderator'));
    }

    /**
     * Test if hasPermission returns true when scope exists via role.
     *
     * @return void
     */
    public function test_if_hasPermission_returns_true_when_scope_exists_via_role(): void
    {
        $user = $this->setupUser([], [['moderator'], ['user']]);

        $this->assertTrue($user->hasPermission('moderator'));
        $this->assertTrue($user->hasPermission('user'));
        $this->assertFalse($user->hasPermission('admin'));
    }

    /**
     * Test if hasAllPermissions returns true when all scopes exist.
     *
     * @return void
     */
    public function test_if_hasAllPermissions_returns_true_when_all_scopes_exist(): void
    {
        $user = $this->setupUser(['admin', 'editor'], [['user'], ['manager']]);
        $this->assertTrue($user->hasAllPermissions(['admin', 'editor']));

        $user = $this->setupUser(['admin'], [['editor', 'user']]);
        $this->assertTrue($user->hasAllPermissions(['admin', 'editor']));

        $this->assertFalse($user->hasAllPermissions(['admin', 'nonexistent']));
    }

    /**
     * Test if hasOneOfPermissions returns true when at least one scope exists.
     *
     * @return void
     */
    public function test_if_hasOneOfPermissions_returns_true_when_at_least_one_scope_exists(): void
    {
        $user = $this->setupUser(['admin'], [['user']]);

        $this->assertTrue($user->hasOneOfPermissions(['editor', 'admin']));
        $this->assertTrue($user->hasOneOfPermissions(['user', 'editor']));
        $this->assertFalse($user->hasOneOfPermissions(['editor', 'moderator']));
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
