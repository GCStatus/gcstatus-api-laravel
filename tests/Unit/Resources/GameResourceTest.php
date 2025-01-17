<?php

namespace Tests\Unit\Resources;

use Mockery;
use App\Http\Resources\GameResource;
use App\Models\{Game, Crack, GameSupport};
use Illuminate\Database\Eloquent\Collection;
use Tests\Contracts\Resources\BaseResourceTesting;

class GameResourceTest extends BaseResourceTesting
{
    /**
     * Define the expected structure for GameResource.
     *
     * @var array<string, string>
     */
    protected array $expectedStructure = [
        'id' => 'int',
        'free' => 'bool',
        'age' => 'string',
        'slug' => 'string',
        'title' => 'string',
        'cover' => 'string',
        'legal' => 'string',
        'about' => 'string',
        'website' => 'string',
        'is_hearted' => 'bool',
        'views_count' => 'int',
        'hearts_count' => 'int',
        'condition' => 'string',
        'created_at' => 'string',
        'updated_at' => 'string',
        'great_release' => 'bool',
        'description' => 'string',
        'release_date' => 'string',
        'short_description' => 'string',
        'crack' => 'object',
        'support' => 'object',
        'tags' => 'resourceCollection',
        'dlcs' => 'resourceCollection',
        'stores' => 'resourceCollection',
        'genres' => 'resourceCollection',
        'reviews' => 'resourceCollection',
        'critics' => 'resourceCollection',
        'comments' => 'resourceCollection',
        'torrents' => 'resourceCollection',
        'platforms' => 'resourceCollection',
        'languages' => 'resourceCollection',
        'galleries' => 'resourceCollection',
        'developers' => 'resourceCollection',
        'publishers' => 'resourceCollection',
        'categories' => 'resourceCollection',
        'requirements' => 'resourceCollection',
    ];

    /**
     * Get the resource class being tested.
     *
     * @return class-string<GameResource>
     */
    public function resource(): string
    {
        return GameResource::class;
    }

    /**
     * Provide a mock instance of Game for testing.
     *
     * @return \App\Models\Game
     */
    public function modelInstance(): Game
    {
        $crackMock = Mockery::mock(Crack::class);
        $supportMock = Mockery::mock(GameSupport::class);

        $tagsMock = Mockery::mock(Collection::class);
        $dlcsMock = Mockery::mock(Collection::class);
        $genresMock = Mockery::mock(Collection::class);
        $storesMock = Mockery::mock(Collection::class);
        $criticsMock = Mockery::mock(Collection::class);
        $reviewsMock = Mockery::mock(Collection::class);
        $commentsMock = Mockery::mock(Collection::class);
        $torrentsMock = Mockery::mock(Collection::class);
        $platformsMock = Mockery::mock(Collection::class);
        $languagesMock = Mockery::mock(Collection::class);
        $galleriesMock = Mockery::mock(Collection::class);
        $categoriesMock = Mockery::mock(Collection::class);
        $developersMock = Mockery::mock(Collection::class);
        $publishersMock = Mockery::mock(Collection::class);
        $requirementsMock = Mockery::mock(Collection::class);

        $gameMock = Mockery::mock(Game::class)->makePartial();
        $gameMock->shouldAllowMockingMethod('getAttribute');

        $gameMock->shouldReceive('getAttribute')->with('id')->andReturn(1);
        $gameMock->shouldReceive('getAttribute')->with('slug')->andReturn(fake()->word());
        $gameMock->shouldReceive('getAttribute')->with('title')->andReturn(fake()->word());
        $gameMock->shouldReceive('getAttribute')->with('legal')->andReturn(fake()->text());
        $gameMock->shouldReceive('getAttribute')->with('about')->andReturn(fake()->text());
        $gameMock->shouldReceive('getAttribute')->with('website')->andReturn(fake()->url());
        $gameMock->shouldReceive('getAttribute')->with('free')->andReturn(fake()->boolean());
        $gameMock->shouldReceive('getAttribute')->with('cover')->andReturn(fake()->imageUrl());
        $gameMock->shouldReceive('getAttribute')->with('created_at')->andReturn(fake()->date());
        $gameMock->shouldReceive('getAttribute')->with('updated_at')->andReturn(fake()->date());
        $gameMock->shouldReceive('getAttribute')->with('description')->andReturn(fake()->text());
        $gameMock->shouldReceive('getAttribute')->with('release_date')->andReturn(fake()->date());
        $gameMock->shouldReceive('getAttribute')->with('is_hearted')->andReturn(fake()->boolean());
        $gameMock->shouldReceive('getAttribute')->with('great_release')->andReturn(fake()->boolean());
        $gameMock->shouldReceive('getAttribute')->with('short_description')->andReturn(fake()->text());
        $gameMock->shouldReceive('getAttribute')->with('age')->andReturn((string)fake()->numberBetween(1, 18));
        $gameMock->shouldReceive('getAttribute')->with('views_count')->andReturn(fake()->numberBetween(1, 999));
        $gameMock->shouldReceive('getAttribute')->with('hearts_count')->andReturn(fake()->numberBetween(1, 999));
        $gameMock->shouldReceive('getAttribute')->with('condition')->andReturn(fake()->randomElement(['hot', 'popular', 'sale', 'commom']));

        $gameMock->shouldReceive('getAttribute')->with('crack')->andReturn($crackMock);
        $gameMock->shouldReceive('getAttribute')->with('support')->andReturn($supportMock);

        $gameMock->shouldReceive('getAttribute')->with('tags')->andReturn($tagsMock);
        $gameMock->shouldReceive('getAttribute')->with('dlcs')->andReturn($dlcsMock);
        $gameMock->shouldReceive('getAttribute')->with('stores')->andReturn($storesMock);
        $gameMock->shouldReceive('getAttribute')->with('genres')->andReturn($genresMock);
        $gameMock->shouldReceive('getAttribute')->with('reviews')->andReturn($reviewsMock);
        $gameMock->shouldReceive('getAttribute')->with('critics')->andReturn($criticsMock);
        $gameMock->shouldReceive('getAttribute')->with('comments')->andReturn($commentsMock);
        $gameMock->shouldReceive('getAttribute')->with('torrents')->andReturn($torrentsMock);
        $gameMock->shouldReceive('getAttribute')->with('platforms')->andReturn($platformsMock);
        $gameMock->shouldReceive('getAttribute')->with('languages')->andReturn($languagesMock);
        $gameMock->shouldReceive('getAttribute')->with('galleries')->andReturn($galleriesMock);
        $gameMock->shouldReceive('getAttribute')->with('developers')->andReturn($developersMock);
        $gameMock->shouldReceive('getAttribute')->with('publishers')->andReturn($publishersMock);
        $gameMock->shouldReceive('getAttribute')->with('categories')->andReturn($categoriesMock);
        $gameMock->shouldReceive('getAttribute')->with('requirements')->andReturn($requirementsMock);

        /** @var \App\Models\Game $gameMock */
        return $gameMock;
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
