<?php

namespace Tests\Unit\Traits;

use Mockery;
use Tests\TestCase;
use App\Models\User;
use Illuminate\Database\Eloquent\Relations\MorphToMany;

class HasRolesTest extends TestCase
{
    /**
     * Test if hasRole returns true when role exists.
     *
     * @return void
     */
    public function test_if_hasRole_returns_true_when_role_exists(): void
    {
        $user = Mockery::mock(User::class)->makePartial();

        $fakeRelation = Mockery::mock(MorphToMany::class);
        $fakeRelation->shouldReceive('where')
            ->with('role_id', 1)
            ->once()
            ->andReturnSelf();
        $fakeRelation->shouldReceive('exists')
            ->once()
            ->andReturnTrue();

        $user->shouldReceive('roles')->andReturn($fakeRelation);

        /** @var \App\Models\User $user */
        $this->assertTrue($user->hasRole(1));
    }

    /**
     * Test if hasAllRoles returns true when all roles exist.
     *
     * @return void
     */
    public function test_if_hasAllRoles_returns_true_when_all_roles_exist(): void
    {
        $rolesToCheck = [1, 2];

        $user = Mockery::mock(User::class)->makePartial();

        $fakeRelation = Mockery::mock(MorphToMany::class);

        $fakeRelation->shouldReceive('whereIn')
            ->with('role_id', $rolesToCheck)
            ->once()
            ->andReturnSelf();

        $fakeRelation->shouldReceive('count')
            ->once()
            ->andReturn(count($rolesToCheck));

        $user->shouldReceive('roles')->andReturn($fakeRelation);

        /** @var \App\Models\User $user */
        $this->assertTrue($user->hasAllRoles($rolesToCheck));
    }

    /**
     * Test if hasOneOfRoles returns true when one role exists.
     *
     * @return void
     */
    public function test_if_hasOneOfRoles_returns_true_when_one_role_exists(): void
    {
        $rolesToCheck = [2, 3];

        $user = Mockery::mock(User::class)->makePartial();

        $fakeRelation = Mockery::mock(MorphToMany::class);

        $fakeRelation->shouldReceive('whereIn')
            ->with('role_id', $rolesToCheck)
            ->once()
            ->andReturnSelf();

        $fakeRelation->shouldReceive('exists')
            ->once()
            ->andReturnTrue();

        $user->shouldReceive('roles')->andReturn($fakeRelation);

        /** @var \App\Models\User $user */
        $this->assertTrue($user->hasOneOfRoles($rolesToCheck));
    }

    /**
     * Test if hasRole returns false when role doesn't exist.
     *
     * @return void
     */
    public function test_if_hasRole_returns_false_when_role_does_not_exist(): void
    {
        $user = Mockery::mock(User::class)->makePartial();

        $fakeRelation = Mockery::mock(MorphToMany::class);

        $fakeRelation->shouldReceive('where')
            ->with('role_id', 5)
            ->once()
            ->andReturnSelf();

        $fakeRelation->shouldReceive('exists')
            ->once()
            ->andReturnFalse();

        $user->shouldReceive('roles')->andReturn($fakeRelation);

        /** @var \App\Models\User $user */
        $this->assertFalse($user->hasRole(5));
    }

    /**
     * Test if hasAllRoles returns false when role doesn't exist.
     *
     * @return void
     */
    public function test_if_hasAllRoles_returns_false_when_all_roles_does_not_exist(): void
    {
        $rolesToCheck = [1, 2];

        $user = Mockery::mock(User::class)->makePartial();

        $fakeRelation = Mockery::mock(MorphToMany::class);

        $fakeRelation->shouldReceive('whereIn')
            ->with('role_id', $rolesToCheck)
            ->once()
            ->andReturnSelf();

        $fakeRelation->shouldReceive('count')
            ->once()
            ->andReturn(0);

        $user->shouldReceive('roles')->andReturn($fakeRelation);

        /** @var \App\Models\User $user */
        $this->assertFalse($user->hasAllRoles($rolesToCheck));
    }

    /**
     * Test if hasOneOfRoles returns false when no one role exists.
     *
     * @return void
     */
    public function test_if_hasOneOfRoles_returns_false_when_no_one_role_exists(): void
    {
        $rolesToCheck = [2, 3];

        $user = Mockery::mock(User::class)->makePartial();

        $fakeRelation = Mockery::mock(MorphToMany::class);

        $fakeRelation->shouldReceive('whereIn')
            ->with('role_id', $rolesToCheck)
            ->once()
            ->andReturnSelf();

        $fakeRelation->shouldReceive('exists')
            ->once()
            ->andReturnFalse();

        $user->shouldReceive('roles')->andReturn($fakeRelation);

        /** @var \App\Models\User $user */
        $this->assertFalse($user->hasOneOfRoles($rolesToCheck));
    }

    /**
     * Tear down test applications.
     *
     * @return void
     */
    public function tearDown(): void
    {
        Mockery::close();

        parent::tearDown();
    }
}
