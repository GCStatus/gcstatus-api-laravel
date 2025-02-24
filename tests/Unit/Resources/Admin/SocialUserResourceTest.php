<?php

namespace Tests\Unit\Resources\Admin;

use Mockery;
use App\Models\{Level, Profile, User};
use App\Http\Resources\Admin\SocialUserResource;
use Tests\Contracts\Resources\BaseResourceTesting;

class SocialUserResourceTest extends BaseResourceTesting
{
    /**
     * Define the expected structure for SocialUserResource.
     *
     * @var array<string, string>
     */
    protected array $expectedStructure = [
        'id' => 'int',
        'level' => 'int',
        'name' => 'string',
        'photo' => 'string',
        'nickname' => 'string',
        'created_at' => 'string',
        'updated_at' => 'string',
    ];

    /**
     * Get the resource class being tested.
     *
     * @return class-string<SocialUserResource>
     */
    public function resource(): string
    {
        return SocialUserResource::class;
    }

    /**
     * Provide a mock instance of SocialUser for testing.
     *
     * @return \App\Models\User
     */
    public function modelInstance(): User
    {
        $levelMock = Mockery::mock(Level::class)->makePartial();
        $levelMock->shouldReceive('getAttribute')->with('level')->andReturn(1);

        $profileMock = Mockery::mock(Profile::class)->makePartial();
        $profileMock->shouldReceive('getAttribute')->with('photo')->andReturn(fake()->imageUrl());

        $sociaulUserMock = Mockery::mock(User::class)->makePartial();
        $sociaulUserMock->shouldAllowMockingMethod('getAttribute');

        $sociaulUserMock->shouldReceive('getAttribute')->with('id')->andReturn(1);
        $sociaulUserMock->shouldReceive('getAttribute')->with('name')->andReturn(fake()->name());
        $sociaulUserMock->shouldReceive('getAttribute')->with('created_at')->andReturn(fake()->date());
        $sociaulUserMock->shouldReceive('getAttribute')->with('updated_at')->andReturn(fake()->date());
        $sociaulUserMock->shouldReceive('getAttribute')->with('nickname')->andReturn(fake()->userName());

        $sociaulUserMock->shouldReceive('getAttribute')->with('level')->andReturn($levelMock);
        $sociaulUserMock->shouldReceive('getAttribute')->with('profile')->andReturn($profileMock);

        /** @var \App\Models\User $sociaulUserMock */
        return $sociaulUserMock;
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
