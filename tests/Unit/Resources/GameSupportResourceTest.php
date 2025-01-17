<?php

namespace Tests\Unit\Resources;

use Mockery;
use App\Models\{Game, GameSupport};
use App\Http\Resources\GameSupportResource;
use Tests\Contracts\Resources\BaseResourceTesting;

class GameSupportResourceTest extends BaseResourceTesting
{
    /**
     * Define the expected structure for GameSupportResource.
     *
     * @var array<string, string>
     */
    protected array $expectedStructure = [
        'id' => 'int',
        'url' => 'string',
        'email' => 'string',
        'contact' => 'string',
        'game' => 'object',
    ];

    /**
     * Get the resource class being tested.
     *
     * @return class-string<GameSupportResource>
     */
    public function resource(): string
    {
        return GameSupportResource::class;
    }

    /**
     * Provide a mock instance of GameSupport for testing.
     *
     * @return \App\Models\GameSupport
     */
    public function modelInstance(): GameSupport
    {
        $gameMock = Mockery::mock(Game::class);

        $gameSupportMock = Mockery::mock(GameSupport::class)->makePartial();
        $gameSupportMock->shouldAllowMockingMethod('getAttribute');

        $gameSupportMock->shouldReceive('getAttribute')->with('id')->andReturn(1);
        $gameSupportMock->shouldReceive('getAttribute')->with('url')->andReturn(fake()->url());
        $gameSupportMock->shouldReceive('getAttribute')->with('email')->andReturn(fake()->email());
        $gameSupportMock->shouldReceive('getAttribute')->with('contact')->andReturn(fake()->phoneNumber());

        $gameSupportMock->shouldReceive('getAttribute')->with('game')->andReturn($gameMock);

        /** @var \App\Models\GameSupport $gameSupportMock */
        return $gameSupportMock;
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
