<?php

namespace Tests\Unit\Resources;

use Mockery;
use App\Http\Resources\TorrentResource;
use App\Models\{Game, Torrent, TorrentProvider};
use Tests\Contracts\Resources\BaseResourceTesting;

class TorrentResourceTest extends BaseResourceTesting
{
    /**
     * Define the expected structure for TorrentResource.
     *
     * @var array<string, string>
     */
    protected array $expectedStructure = [
        'id' => 'int',
        'url' => 'string',
        'posted_at' => 'string',
        'game' => 'object',
        'provider' => 'object',
    ];

    /**
     * Get the resource class being tested.
     *
     * @return class-string<TorrentResource>
     */
    public function resource(): string
    {
        return TorrentResource::class;
    }

    /**
     * Provide a mock instance of Torrent for testing.
     *
     * @return \App\Models\Torrent
     */
    public function modelInstance(): Torrent
    {
        $gameMock = Mockery::mock(Game::class);
        $torrentProviderMock = Mockery::mock(TorrentProvider::class);

        $torrentMock = Mockery::mock(Torrent::class)->makePartial();
        $torrentMock->shouldAllowMockingMethod('getAttribute');

        $torrentMock->shouldReceive('getAttribute')->with('id')->andReturn(1);
        $torrentMock->shouldReceive('getAttribute')->with('url')->andReturn(fake()->url());
        $torrentMock->shouldReceive('getAttribute')->with('posted_at')->andReturn(fake()->date());

        $torrentMock->shouldReceive('getAttribute')->with('game')->andReturn($gameMock);
        $torrentMock->shouldReceive('getAttribute')->with('user')->andReturn($torrentProviderMock);

        /** @var \App\Models\Torrent $torrentMock */
        return $torrentMock;
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
