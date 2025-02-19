<?php

namespace Tests\Unit\Resources\Admin;

use Mockery;
use App\Models\{Role, Permission};
use App\Http\Resources\Admin\RoleResource;
use Tests\Contracts\Resources\BaseResourceTesting;

class RoleResourceTest extends BaseResourceTesting
{
    /**
     * Define the expected structure for RoleResource.
     *
     * @var array<string, string>
     */
    protected array $expectedStructure = [
        'id' => 'int',
        'name' => 'string',
        'created_at' => 'string',
        'updated_at' => 'string',
        'permissions' => 'resourceCollection',
    ];

    /**
     * Get the resource class being tested.
     *
     * @return class-string<RoleResource>
     */
    public function resource(): string
    {
        return RoleResource::class;
    }

    /**
     * Provide a mock instance of Role for testing.
     *
     * @return \App\Models\Role
     */
    public function modelInstance(): Role
    {
        $roleMock = Mockery::mock(Role::class)->makePartial();
        $roleMock->shouldAllowMockingMethod('getAttribute');

        $roleMock->shouldReceive('getAttribute')->with('id')->andReturn(1);
        $roleMock->shouldReceive('getAttribute')->with('name')->andReturn(fake()->name());
        $roleMock->shouldReceive('getAttribute')->with('slug')->andReturn(fake()->name());
        $roleMock->shouldReceive('getAttribute')->with('created_at')->andReturn(fake()->date());
        $roleMock->shouldReceive('getAttribute')->with('updated_at')->andReturn(fake()->date());

        $permissionMock = Mockery::mock(Permission::class)->makePartial();
        $permissionMock->shouldAllowMockingMethod('getAttribute');

        $permissionMock->shouldReceive('getAttribute')->with('id')->andReturn(1);
        $permissionMock->shouldReceive('getAttribute')->with('scope')->andReturn(fake()->word());
        $permissionMock->shouldReceive('getAttribute')->with('created_at')->andReturn(fake()->date());
        $permissionMock->shouldReceive('getAttribute')->with('updated_at')->andReturn(fake()->date());

        $roleMock->shouldReceive('relationLoaded')
            ->with('permissions')
            ->andReturnTrue();

        $roleMock->shouldReceive('getAttribute')
            ->with('permissions')
            ->andReturn([$permissionMock]);

        /** @var \App\Models\Role $roleMock */
        return $roleMock;
    }

    /**
     * Cleanup Mockery after each test.
     *
     * @return void
     */
    protected function tearDown(): void
    {
        Mockery::close();

        parent::tearDown();
    }
}
