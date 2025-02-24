<?php

namespace Tests\Unit\Resources\Admin;

use Mockery;
use App\Models\{Game, Dlc};
use App\Http\Resources\Admin\DlcResource;
use Illuminate\Database\Eloquent\Collection;
use Tests\Contracts\Resources\BaseResourceTesting;

class DlcResourceTest extends BaseResourceTesting
{
    /**
     * Define the expected structure for DlcResource.
     *
     * @var array<string, string>
     */
    protected array $expectedStructure = [
        'id' => 'int',
        'free' => 'bool',
        'slug' => 'string',
        'title' => 'string',
        'cover' => 'string',
        'legal' => 'string',
        'about' => 'string',
        'description' => 'string',
        'release_date' => 'string',
        'short_description' => 'string',
        'created_at' => 'string',
        'updated_at' => 'string',
        'game' => 'object',
        'tags' => 'resourceCollection',
        'stores' => 'resourceCollection',
        'genres' => 'resourceCollection',
        'platforms' => 'resourceCollection',
        'galleries' => 'resourceCollection',
        'developers' => 'resourceCollection',
        'publishers' => 'resourceCollection',
        'categories' => 'resourceCollection',
    ];

    /**
     * Get the resource class being tested.
     *
     * @return class-string<DlcResource>
     */
    public function resource(): string
    {
        return DlcResource::class;
    }

    /**
     * Provide a mock instance of Dlc for testing.
     *
     * @return \App\Models\Dlc
     */
    public function modelInstance(): Dlc
    {
        $gameMock = Mockery::mock(Game::class);
        $tagsMock = Mockery::mock(Collection::class);
        $genresMock = Mockery::mock(Collection::class);
        $storesMock = Mockery::mock(Collection::class);
        $platformsMock = Mockery::mock(Collection::class);
        $galleriesMock = Mockery::mock(Collection::class);
        $categoriesMock = Mockery::mock(Collection::class);
        $developersMock = Mockery::mock(Collection::class);
        $publishersMock = Mockery::mock(Collection::class);

        $dlcMock = Mockery::mock(Dlc::class)->makePartial();
        $dlcMock->shouldAllowMockingMethod('getAttribute');

        $dlcMock->shouldReceive('getAttribute')->with('id')->andReturn(1);
        $dlcMock->shouldReceive('getAttribute')->with('slug')->andReturn(fake()->word());
        $dlcMock->shouldReceive('getAttribute')->with('title')->andReturn(fake()->word());
        $dlcMock->shouldReceive('getAttribute')->with('legal')->andReturn(fake()->text());
        $dlcMock->shouldReceive('getAttribute')->with('about')->andReturn(fake()->text());
        $dlcMock->shouldReceive('getAttribute')->with('free')->andReturn(fake()->boolean());
        $dlcMock->shouldReceive('getAttribute')->with('cover')->andReturn(fake()->imageUrl());
        $dlcMock->shouldReceive('getAttribute')->with('created_at')->andReturn(fake()->date());
        $dlcMock->shouldReceive('getAttribute')->with('updated_at')->andReturn(fake()->date());
        $dlcMock->shouldReceive('getAttribute')->with('description')->andReturn(fake()->text());
        $dlcMock->shouldReceive('getAttribute')->with('release_date')->andReturn(fake()->date());
        $dlcMock->shouldReceive('getAttribute')->with('short_description')->andReturn(fake()->text());

        $dlcMock->shouldReceive('getAttribute')->with('game')->andReturn($gameMock);
        $dlcMock->shouldReceive('getAttribute')->with('tags')->andReturn($tagsMock);
        $dlcMock->shouldReceive('getAttribute')->with('stores')->andReturn($storesMock);
        $dlcMock->shouldReceive('getAttribute')->with('genres')->andReturn($genresMock);
        $dlcMock->shouldReceive('getAttribute')->with('platforms')->andReturn($platformsMock);
        $dlcMock->shouldReceive('getAttribute')->with('galleries')->andReturn($galleriesMock);
        $dlcMock->shouldReceive('getAttribute')->with('developers')->andReturn($developersMock);
        $dlcMock->shouldReceive('getAttribute')->with('publishers')->andReturn($publishersMock);
        $dlcMock->shouldReceive('getAttribute')->with('categories')->andReturn($categoriesMock);

        /** @var \App\Models\Dlc $dlcMock */
        return $dlcMock;
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
