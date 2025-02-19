<?php

namespace Tests\Unit\Resources\Admin;

use Mockery;
use App\Models\Permission;
use App\Http\Resources\Admin\PermissionResource;
use Tests\Contracts\Resources\BaseResourceTesting;

class PermissionResourceTest extends BaseResourceTesting
{
    /**
     * Define the expected structure for PermissionResource.
     *
     * @var array<string, string>
     */
    protected array $expectedStructure = [
        'id' => 'int',
        'scope' => 'string',
        'created_at' => 'string',
        'updated_at' => 'string',
    ];

    /**
     * Get the resource class being tested.
     *
     * @return class-string<PermissionResource>
     */
    public function resource(): string
    {
        return PermissionResource::class;
    }

    /**
     * Provide a mock instance of Permission for testing.
     *
     * @return \App\Models\Permission
     */
    public function modelInstance(): Permission
    {
        $permissionMock = Mockery::mock(Permission::class)->makePartial();
        $permissionMock->shouldAllowMockingMethod('getAttribute');

        $permissionMock->shouldReceive('getAttribute')->with('id')->andReturn(1);
        $permissionMock->shouldReceive('getAttribute')->with('scope')->andReturn(fake()->word());
        $permissionMock->shouldReceive('getAttribute')->with('created_at')->andReturn(fake()->date());
        $permissionMock->shouldReceive('getAttribute')->with('updated_at')->andReturn(fake()->date());

        /** @var \App\Models\Permission $permissionMock */
        return $permissionMock;
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
