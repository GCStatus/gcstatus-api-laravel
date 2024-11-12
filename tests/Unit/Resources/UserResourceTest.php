<?php

namespace Tests\Unit\Resources;

use Mockery;
use App\Models\{User, Level};
use App\Http\Resources\UserResource;
use Tests\Contracts\Resources\BaseResourceTesting;

class UserResourceTest extends BaseResourceTesting
{
    /**
     * Define the expected structure for UserResource.
     *
     * @var array<string, string>
     */
    protected array $expectedStructure = [
        'id' => 'int',
        'name' => 'string',
        'email' => 'string',
        'nickname' => 'string',
        'level' => 'int',
        'birthdate' => 'string',
        'experience' => 'int',
    ];

    /**
     * Get the resource class being tested.
     *
     * @return class-string<UserResource>
     */
    public function resource(): string
    {
        return UserResource::class;
    }

    /**
     * Provide a mock instance of User for testing.
     *
     * @return \App\Models\User
     */
    public function modelInstance(): User
    {
        $userMock = Mockery::mock(User::class)->makePartial();
        $userMock->shouldAllowMockingMethod('setAttribute');

        $userMock->shouldReceive('getAttribute')->with('id')->andReturn(1);
        $userMock->shouldReceive('getAttribute')->with('name')->andReturn('Test User');
        $userMock->shouldReceive('getAttribute')->with('email')->andReturn('test@example.com');
        $userMock->shouldReceive('getAttribute')->with('nickname')->andReturn('testuser');
        $userMock->shouldReceive('getAttribute')->with('birthdate')->andReturn('2000-01-01');
        $userMock->shouldReceive('getAttribute')->with('experience')->andReturn(1000);

        $levelMock = Mockery::mock(Level::class);
        $levelMock->shouldReceive('getAttribute')->with('level')->andReturn(5);
        $userMock->shouldReceive('getAttribute')->with('level')->andReturn($levelMock);

        /** @var \App\Models\User $castedUser */
        $castedUser = $userMock;

        return $castedUser;
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
