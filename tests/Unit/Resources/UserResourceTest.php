<?php

namespace Tests\Unit\Resources;

use Mockery;
use App\Http\Resources\UserResource;
use Tests\Contracts\Resources\BaseResourceTesting;
use App\Models\{User, Level, Profile, Title, Wallet};

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
        'created_at' => 'string',
        'updated_at' => 'string',
        'wallet' => 'object',
        'profile' => 'object',
        'title' => 'object',
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
        $levelMock = Mockery::mock(Level::class);
        $levelMock->shouldReceive('getAttribute')->with('level')->andReturn(5);

        $profileMock = Mockery::mock(Profile::class);
        $profileMock->shouldReceive('getAttribute')->with('id')->andReturn(1);
        $profileMock->shouldReceive('getAttribute')->with('share')->andReturn(false);
        $profileMock->shouldReceive('getAttribute')->with('photo')->andReturn('https://example.com/photo.jpg');

        $walletMock = Mockery::mock(Wallet::class);
        $walletMock->shouldReceive('getAttribute')->with('id')->andReturn(1);
        $walletMock->shouldReceive('getAttribute')->with('balance')->andReturn(100);

        $titleMock = Mockery::mock(Title::class);
        $titleMock->shouldReceive('getAttribute')->with('id')->andReturn(1);
        $titleMock->shouldReceive('getAttribute')->with('title')->andReturn(fake()->title());

        $userMock = Mockery::mock(User::class)->makePartial();
        $userMock->shouldAllowMockingMethod('getAttribute');

        $userMock->shouldReceive('getAttribute')->with('id')->andReturn(1);
        $userMock->shouldReceive('getAttribute')->with('name')->andReturn('Test User');
        $userMock->shouldReceive('getAttribute')->with('email')->andReturn('test@example.com');
        $userMock->shouldReceive('getAttribute')->with('nickname')->andReturn('testuser');
        $userMock->shouldReceive('getAttribute')->with('birthdate')->andReturn('2000-01-01');
        $userMock->shouldReceive('getAttribute')->with('experience')->andReturn(1000);
        $userMock->shouldReceive('getAttribute')->with('created_at')->andReturn(now()->toISOString());
        $userMock->shouldReceive('getAttribute')->with('updated_at')->andReturn(now()->toISOString());

        $userMock->shouldReceive('getAttribute')->with('level')->andReturn($levelMock);
        $userMock->shouldReceive('getAttribute')->with('profile')->andReturn($profileMock);
        $userMock->shouldReceive('getAttribute')->with('wallet')->andReturn($walletMock);
        $userMock->shouldReceive('getAttribute')->with('title')->andReturn($titleMock);

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
