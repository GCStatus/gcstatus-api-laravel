<?php

namespace Tests\Unit\Resources;

use Mockery;
use App\Models\{Profile, User};
use App\Http\Resources\ProfileResource;
use Tests\Contracts\Resources\BaseResourceTesting;

class ProfileResourceTest extends BaseResourceTesting
{
    /**
     * Define the expected structure for ProfileResource.
     *
     * @var array<string, string>
     */
    protected array $expectedStructure = [
        'id' => 'int',
        'photo' => 'string',
        'share' => 'bool',
        'phone' => 'string',
        'twitch' => 'string',
        'github' => 'string',
        'twitter' => 'string',
        'youtube' => 'string',
        'facebook' => 'string',
        'instagram' => 'string',
        'user' => 'object',
    ];

    /**
     * Get the resource class being tested.
     *
     * @return class-string<ProfileResource>
     */
    public function resource(): string
    {
        return ProfileResource::class;
    }

    /**
     * Provide a mock instance of Profile for testing.
     *
     * @return \App\Models\Profile
     */
    public function modelInstance(): Profile
    {
        $userMock = Mockery::mock(User::class)->makePartial();
        $userMock->shouldAllowMockingMethod('getAttribute');
        $userMock->shouldReceive('getAttribute')->with('id')->andReturn(1);
        $userMock->shouldReceive('getAttribute')->with('name')->andReturn('Test User');
        $userMock->shouldReceive('getAttribute')->with('email')->andReturn('test@example.com');
        $userMock->shouldReceive('getAttribute')->with('nickname')->andReturn('testuser');
        $userMock->shouldReceive('getAttribute')->with('birthdate')->andReturn('2000-01-01');
        $userMock->shouldReceive('getAttribute')->with('experience')->andReturn(1000);

        $profileMock = Mockery::mock(Profile::class)->makePartial();
        $profileMock->shouldAllowMockingMethod('getAttribute');
        $profileMock->shouldReceive('getAttribute')->with('id')->andReturn(1);
        $profileMock->shouldReceive('getAttribute')->with('share')->andReturn(false);
        $profileMock->shouldReceive('getAttribute')->with('twitch')->andReturn(fake()->url());
        $profileMock->shouldReceive('getAttribute')->with('github')->andReturn(fake()->url());
        $profileMock->shouldReceive('getAttribute')->with('twitter')->andReturn(fake()->url());
        $profileMock->shouldReceive('getAttribute')->with('youtube')->andReturn(fake()->url());
        $profileMock->shouldReceive('getAttribute')->with('facebook')->andReturn(fake()->url());
        $profileMock->shouldReceive('getAttribute')->with('instagram')->andReturn(fake()->url());
        $profileMock->shouldReceive('getAttribute')->with('photo')->andReturn(fake()->imageUrl());
        $profileMock->shouldReceive('getAttribute')->with('phone')->andReturn(fake()->phoneNumber());

        $profileMock->shouldReceive('relationLoaded')
            ->with('user')
            ->andReturn(true);

        $profileMock->shouldReceive('getAttribute')
            ->with('user')
            ->andReturn($userMock);

        /** @var \App\Models\Profile $profileMock */
        return $profileMock;
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
