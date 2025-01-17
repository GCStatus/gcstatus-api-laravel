<?php

namespace Tests\Unit\Resources;

use Mockery;
use App\Models\TorrentProvider;
use App\Http\Resources\TorrentProviderResource;
use Tests\Contracts\Resources\BaseResourceTesting;

class TorrentProviderResourceTest extends BaseResourceTesting
{
    /**
     * Define the expected structure for TorrentProviderResource.
     *
     * @var array<string, string>
     */
    protected array $expectedStructure = [
        'id' => 'int',
        'url' => 'string',
        'name' => 'string',
        'slug' => 'string',
    ];

    /**
     * Get the resource class being tested.
     *
     * @return class-string<TorrentProviderResource>
     */
    public function resource(): string
    {
        return TorrentProviderResource::class;
    }

    /**
     * Provide a mock instance of TorrentProvider for testing.
     *
     * @return \App\Models\TorrentProvider
     */
    public function modelInstance(): TorrentProvider
    {
        $torrentProviderMock = Mockery::mock(TorrentProvider::class)->makePartial();
        $torrentProviderMock->shouldAllowMockingMethod('getAttribute');

        $torrentProviderMock->shouldReceive('getAttribute')->with('id')->andReturn(1);
        $torrentProviderMock->shouldReceive('getAttribute')->with('url')->andReturn(fake()->url());
        $torrentProviderMock->shouldReceive('getAttribute')->with('name')->andReturn(fake()->name());
        $torrentProviderMock->shouldReceive('getAttribute')->with('slug')->andReturn(fake()->name());

        /** @var \App\Models\TorrentProvider $torrentProviderMock */
        return $torrentProviderMock;
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
