<?php

namespace Tests\Unit\Resources\Admin;

use Mockery;
use App\Models\{Role, Permission, User};
use App\Http\Resources\Admin\MeResource;
use Tests\Contracts\Resources\BaseResourceTesting;

class MeResourceTest extends BaseResourceTesting
{
    /**
     * Define the expected structure for MeResource.
     *
     * @var array<string, string>
     */
    protected array $expectedStructure = [
        'id' => 'int',
        'name' => 'string',
        'email' => 'string',
        'birthdate' => 'string',
        'created_at' => 'string',
        'updated_at' => 'string',
        'roles' => 'resourceCollection',
        'permissions' => 'resourceCollection',
    ];

    /**
     * Get the resource class being tested.
     *
     * @return class-string<MeResource>
     */
    public function resource(): string
    {
        return MeResource::class;
    }

    /**
     * Provide a mock instance of User for testing.
     *
     * @return \App\Models\User
     */
    public function modelInstance(): User
    {
        $userMock = Mockery::mock(User::class);
        $userMock->shouldReceive('getAttribute')->with('id')->andReturn(1);
        $userMock->shouldReceive('getAttribute')->with('name')->andReturn('Test User');
        $userMock->shouldReceive('getAttribute')->with('birthdate')->andReturn('2000-01-01');
        $userMock->shouldReceive('getAttribute')->with('email')->andReturn('test@example.com');
        $userMock->shouldReceive('getAttribute')->with('created_at')->andReturn(now()->toISOString());
        $userMock->shouldReceive('getAttribute')->with('updated_at')->andReturn(now()->toISOString());

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

        $userMock->shouldReceive('relationLoaded')
            ->with('roles')
            ->andReturnTrue();

        $userMock->shouldReceive('relationLoaded')
            ->with('permissions')
            ->andReturnTrue();

        $userMock->shouldReceive('getAttribute')
            ->with('roles')
            ->andReturn([$roleMock]);

        $userMock->shouldReceive('getAttribute')
            ->with('permissions')
            ->andReturn([$permissionMock]);

        /** @var \App\Models\User $userMock */
        return $userMock;
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
