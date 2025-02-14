<?php

namespace Tests\Unit\Services;

use Mockery;
use Tests\TestCase;
use App\Models\{User, Role};
use App\Contracts\Services\PermissionServiceInterface;

class PermissionServiceTest extends TestCase
{
    /**
     * The permission service.
     *
     * @var \App\Contracts\Services\PermissionServiceInterface
     */
    private PermissionServiceInterface $permissionService;

    /**
     * Setup new test environments.
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->permissionService = app(PermissionServiceInterface::class);
    }

    /**
     * Test if can pass if user has tech role.
     *
     * @return void
     */
    public function test_if_can_pass_if_user_has_tech_role(): void
    {
        $user = Mockery::mock(User::class);

        $user
            ->shouldReceive('hasRole')
            ->once()
            ->with(Role::TECHNOLOGY_ROLE_ID)
            ->andReturnTrue();

        /** @var \App\Models\User $user */
        $this->assertTrue(
            $this->permissionService->hasAllPermissions($user, ['permission1']),
        );
    }

    /**
     * Test if can pass if user has all given permissions.
     *
     * @return void
     */
    public function test_if_can_pass_if_user_has_all_given_permissions(): void
    {
        $permissions = [
            'permission1',
            'permission2',
        ];

        $user = Mockery::mock(User::class);

        $user
            ->shouldReceive('hasRole')
            ->once()
            ->with(Role::TECHNOLOGY_ROLE_ID)
            ->andReturnFalse();

        $user
            ->shouldReceive('hasAllPermissions')
            ->once()
            ->with($permissions)
            ->andReturnTrue();

        /** @var \App\Models\User $user */
        $this->assertTrue(
            $this->permissionService->hasAllPermissions($user, $permissions),
        );
    }

    /**
     * Test if can't pass if user hasn't all given permissions.
     *
     * @return void
     */
    public function test_if_cant_pass_if_user_hasnt_all_given_permissions(): void
    {
        $permissions = [
            'permission1',
            'permission2',
        ];

        $user = Mockery::mock(User::class);

        $user
            ->shouldReceive('hasRole')
            ->once()
            ->with(Role::TECHNOLOGY_ROLE_ID)
            ->andReturnFalse();

        $user
            ->shouldReceive('hasAllPermissions')
            ->once()
            ->with($permissions)
            ->andReturnFalse();

        /** @var \App\Models\User $user */
        $this->assertFalse(
            $this->permissionService->hasAllPermissions($user, $permissions),
        );
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
