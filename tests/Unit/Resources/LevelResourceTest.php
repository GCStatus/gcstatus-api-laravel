<?php

namespace Tests\Unit\Resources;

use Mockery;
use App\Models\{User, Level};
use App\Http\Resources\LevelResource;
use Tests\Contracts\Resources\BaseResourceTesting;

class LevelResourceTest extends BaseResourceTesting
{
    /**
     * Define the expected structure for LevelResource.
     *
     * @var array<string, string>
     */
    protected array $expectedStructure = [
        'id' => 'int',
        'level' => 'int',
        'coins' => 'int',
        'experience' => 'int',
        'users' => 'resourceCollection',
    ];

    /**
     * Get the resource class being tested.
     *
     * @return class-string<LevelResource>
     */
    public function resource(): string
    {
        return LevelResource::class;
    }

    /**
     * Provide a mock instance of Level for testing.
     *
     * @return \App\Models\Level
     */
    public function modelInstance(): Level
    {
        $userMock = Mockery::mock(User::class)->makePartial();
        $userMock->shouldAllowMockingMethod('getAttribute');
        $userMock->shouldReceive('getAttribute')->with('id')->andReturn(1);
        $userMock->shouldReceive('getAttribute')->with('name')->andReturn('Test User');
        $userMock->shouldReceive('getAttribute')->with('email')->andReturn('test@example.com');
        $userMock->shouldReceive('getAttribute')->with('nickname')->andReturn('testuser');
        $userMock->shouldReceive('getAttribute')->with('birthdate')->andReturn('2000-01-01');
        $userMock->shouldReceive('getAttribute')->with('experience')->andReturn(1000);

        $levelMock = Mockery::mock(Level::class)->makePartial();
        $levelMock->shouldAllowMockingMethod('getAttribute');
        $levelMock->shouldReceive('getAttribute')->with('id')->andReturn(2);
        $levelMock->shouldReceive('getAttribute')->with('level')->andReturn(2);
        $levelMock->shouldReceive('getAttribute')->with('coins')->andReturn(100);
        $levelMock->shouldReceive('getAttribute')->with('experience')->andReturn(1000);

        $levelMock->shouldReceive('relationLoaded')
            ->with('users')
            ->andReturnTrue();

        $levelMock->shouldReceive('getAttribute')
            ->with('users')
            ->andReturn([$userMock]);

        /** @var \App\Models\Level $levelMock */
        return $levelMock;
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
