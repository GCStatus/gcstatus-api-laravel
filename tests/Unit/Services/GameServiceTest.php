<?php

namespace Tests\Unit\Services;

use Mockery;
use Tests\TestCase;
use App\Models\Game;
use Mockery\MockInterface;
use Illuminate\Support\Str;
use App\Repositories\GameRepository;
use Illuminate\Database\Eloquent\Collection;
use App\Contracts\Services\GameServiceInterface;
use App\Contracts\Repositories\GameRepositoryInterface;
use App\Models\Status;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphToMany;

class GameServiceTest extends TestCase
{
    /**
     * The game repository.
     *
     * @var \Mockery\MockInterface
     */
    private MockInterface $gameRepository;

    /**
     * The game service.
     *
     * @var \App\Contracts\Services\GameServiceInterface
     */
    private GameServiceInterface $gameService;

    /**
     * Setup new test environments.
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->gameRepository = Mockery::mock(GameRepositoryInterface::class);

        $this->app->instance(GameRepositoryInterface::class, $this->gameRepository);

        $this->gameService = app(GameServiceInterface::class);
    }

    /**
     * Test if GameService uses the User model correctly.
     *
     * @return void
     */
    public function test_user_repository_uses_user_model(): void
    {
        $this->app->instance(GameRepositoryInterface::class, app(GameRepository::class));

        /** @var \App\Services\GameService $gameService */
        $gameService = app(GameServiceInterface::class);

        $this->assertInstanceOf(GameRepository::class, $gameService->repository());
    }

    /**
     * Test if can get a game details.
     *
     * @return void
     */
    public function test_if_can_get_a_game_details(): void
    {
        $title = fake()->word();
        $slug = Str::slug($title);

        $game = Mockery::mock(Game::class);
        $game->shouldAllowMockingProtectedMethods();

        $this->gameRepository
            ->shouldReceive('details')
            ->once()
            ->with($slug)
            ->andReturn($game);

        $game
            ->shouldReceive('increment')
            ->once()
            ->with('views')
            ->andReturnTrue();

        $result = $this->gameService->details($slug);

        $this->assertEquals($game, $result);
        $this->assertInstanceOf(Game::class, $result);

        $this->assertEquals(2, Mockery::getContainer()->mockery_getExpectationCount(), 'Mock expectations met.');
    }

    /**
     * Test if can get a game details for admin.
     *
     * @return void
     */
    public function test_if_can_get_a_game_details_for_admin(): void
    {
        $id = 1;

        $game = Mockery::mock(Game::class);
        $game->shouldAllowMockingProtectedMethods();

        $this->gameRepository
            ->shouldReceive('detailsForAdmin')
            ->once()
            ->with($id)
            ->andReturn($game);

        $game->shouldNotReceive('increment')->with('views');

        $result = $this->gameService->detailsForAdmin($id);

        $this->assertEquals($game, $result);
        $this->assertInstanceOf(Game::class, $result);

        $this->assertEquals(2, Mockery::getContainer()->mockery_getExpectationCount(), 'Mock expectations met.');
    }

    /**
     * Test if can create a new dlc.
     *
     * @return void
     */
    public function test_if_can_create_a_new_dlc(): void
    {
        $game = Mockery::mock(Game::class);
        $game->shouldReceive('getAttribute')->with('id')->andReturn(1);

        /** @var \App\Models\Game $game */
        $data = [
            'legal' => fake()->text(),
            'title' => fake()->title(),
            'free' => fake()->boolean(),
            'release_date' => fake()->date(),
            'website' => 'https://google.com',
            'great_release' => fake()->boolean(),
            'about' => clean(fake()->realText()),
            'short_description' => fake()->text(),
            'description' => clean(fake()->realText()),
            'age' => fake()->numberBetween(0, 18),
            'cover' => 'https://placehold.co/600x400/EEE/31343C',
            'condition' => fake()->randomElement(['common', 'hot', 'popular', 'unreleased', 'sale']),
        ];

        $this->gameRepository
            ->shouldReceive('create')
            ->once()
            ->with($data)
            ->andReturn($game);

        $result = $this->gameService->create($data);

        $this->assertSame($game, $result);
        $this->assertInstanceOf(Game::class, $result);

        $this->assertEquals(1, Mockery::getContainer()->mockery_getExpectationCount(), 'Mock expectations met.');
    }

    /**
     * Test if can update a dlc.
     *
     * @return void
     */
    public function test_if_can_update_a_dlc(): void
    {
        $id = 1;

        $data = [
            'legal' => fake()->text(),
            'title' => fake()->title(),
            'free' => fake()->boolean(),
            'about' => fake()->realText(),
            'release_date' => fake()->date(),
            'website' => 'https://google.com',
            'description' => fake()->realText(),
            'great_release' => fake()->boolean(),
            'short_description' => fake()->text(),
            'age' => fake()->numberBetween(0, 18),
            'cover' => 'https://placehold.co/600x400/EEE/31343C',
            'condition' => fake()->randomElement(['common', 'hot', 'popular', 'unreleased', 'sale']),
            'tags' => [1, 2],
            'genres' => [3, 4],
            'platforms' => [5, 6],
            'categories' => [7, 8],
            'publishers' => [9, 10],
            'developers' => [11, 12],
            'crack' => [
                'cracked_at' => '2023-01-01',
                'cracker_id' => 10,
                'protection_id' => 20,
                'status' => 'cracked',
            ],
        ];

        $expectedData = $data;
        $expectedData['about'] = clean($expectedData['about']);
        $expectedData['description'] = clean($expectedData['description']);

        $game = Mockery::mock(Game::class);
        $game->shouldReceive('getAttribute')->with('id')->andReturn($id);

        $relations = ['tags', 'genres', 'platforms', 'categories', 'publishers', 'developers'];

        foreach ($relations as $relation) {
            $relationMock = Mockery::mock(MorphToMany::class);

            $relationMock->shouldReceive('sync')
                ->once()
                ->with($data[$relation]);

            $game->shouldReceive($relation)
                ->once()
                ->andReturn($relationMock);
        }

        $crackMock = Mockery::mock(HasOne::class);
        $crackMock->shouldReceive('updateOrCreate')
            ->once()
            ->with([], [
                'cracked_at' => '2023-01-01',
                'cracker_id' => 10,
                'protection_id' => 20,
                'status_id' => Status::TRANSLATE_TO_ID['cracked'],
            ]);
        $game->shouldReceive('crack')
            ->once()
            ->andReturn($crackMock);

        $this->gameRepository
            ->shouldReceive('update')
            ->once()
            ->with($expectedData, $id)
            ->andReturn($game);

        $result = $this->gameService->update($data, $id);

        $this->assertSame($game, $result);
        $this->assertInstanceOf(Game::class, $result);

        $this->assertEquals(15, Mockery::getContainer()->mockery_getExpectationCount(), 'Mock expectations met.');
    }

    /**
     * Test if can get all games calendar.
     *
     * @return void
     */
    public function test_if_can_get_all_games_calendar(): void
    {
        $collection = Mockery::mock(Collection::class);

        $this->gameRepository
            ->shouldReceive('getCalendarGames')
            ->once()
            ->withNoArgs()
            ->andReturn($collection);

        $result = $this->gameService->getCalendarGames();

        $this->assertEquals($result, $collection);
        $this->assertInstanceOf(Collection::class, $result);

        $this->assertEquals(1, Mockery::getContainer()->mockery_getExpectationCount(), 'Mock expectations met.');
    }

    /**
     * Test if can search games.
     *
     * @return void
     */
    public function test_if_can_search_games(): void
    {
        $q = fake()->word();

        $collection = Mockery::mock(Collection::class);

        $this->gameRepository
            ->shouldReceive('search')
            ->once()
            ->with($q)
            ->andReturn($collection);

        $result = $this->gameService->search($q);

        $this->assertEquals($result, $collection);
        $this->assertInstanceOf(Collection::class, $result);

        $this->assertEquals(1, Mockery::getContainer()->mockery_getExpectationCount(), 'Mock expectations met.');
    }

    /**
     * Test if can get all games by condition.
     *
     * @return void
     */
    public function test_if_can_get_all_games_by_condition(): void
    {
        $limit = 100;
        $condition = Game::HOT_CONDITION;

        $collection = Mockery::mock(Collection::class);

        $this->gameRepository
            ->shouldReceive('getGamesByCondition')
            ->once()
            ->with($condition, $limit)
            ->andReturn($collection);

        $result = $this->gameService->getGamesByCondition($condition, $limit);

        $this->assertEquals($result, $collection);
        $this->assertInstanceOf(Collection::class, $result);

        $this->assertEquals(1, Mockery::getContainer()->mockery_getExpectationCount(), 'Mock expectations met.');
    }

    /**
     * Test if can get all games by attribute values.
     *
     * @return void
     */
    public function test_if_can_get_all_games_by_attribute_values(): void
    {
        $attribute = 'tags';
        $value = fake()->word();

        $collection = Mockery::mock(Collection::class);

        $data = ['value' => $value, 'attribute' => $attribute];

        $this->gameRepository
            ->shouldReceive('findByAttribute')
            ->once()
            ->with($data)
            ->andReturn($collection);

        $result = $this->gameService->findByAttribute($data);

        $this->assertEquals($result, $collection);
        $this->assertInstanceOf(Collection::class, $result);

        $this->assertEquals(1, Mockery::getContainer()->mockery_getExpectationCount(), 'Mock expectations met.');
    }

    /**
     * Test if can get all upcoming games.
     *
     * @return void
     */
    public function test_if_can_get_all_upcoming_games(): void
    {
        $limit = 100;

        $collection = Mockery::mock(Collection::class);

        $this->gameRepository
            ->shouldReceive('getUpcomingGames')
            ->once()
            ->with($limit)
            ->andReturn($collection);

        $result = $this->gameService->getUpcomingGames($limit);

        $this->assertEquals($result, $collection);
        $this->assertInstanceOf(Collection::class, $result);

        $this->assertEquals(1, Mockery::getContainer()->mockery_getExpectationCount(), 'Mock expectations met.');
    }

    /**
     * Test if can get all most liked games.
     *
     * @return void
     */
    public function test_if_can_get_all_most_liked_games(): void
    {
        $limit = 100;

        $collection = Mockery::mock(Collection::class);

        $this->gameRepository
            ->shouldReceive('getMostLikedGames')
            ->once()
            ->with($limit)
            ->andReturn($collection);

        $result = $this->gameService->getMostLikedGames($limit);

        $this->assertEquals($result, $collection);
        $this->assertInstanceOf(Collection::class, $result);

        $this->assertEquals(1, Mockery::getContainer()->mockery_getExpectationCount(), 'Mock expectations met.');
    }

    /**
     * Test if can get next great game release.
     *
     * @return void
     */
    public function test_if_can_get_next_great_game_release(): void
    {
        $gameMock = Mockery::mock(Game::class);

        $this->gameRepository
            ->shouldReceive('getNextGreatRelease')
            ->once()
            ->withNoArgs()
            ->andReturn($gameMock);

        $result = $this->gameService->getNextGreatRelease();

        $this->assertEquals($gameMock, $result);
        $this->assertInstanceOf(Game::class, $result);

        $this->assertEquals(1, Mockery::getContainer()->mockery_getExpectationCount(), 'Mock expectations met.');
    }

    /**
     * Tear down application tests.
     *
     * @return void
     */
    public function tearDown(): void
    {
        Mockery::close();

        parent::tearDown();
    }
}
