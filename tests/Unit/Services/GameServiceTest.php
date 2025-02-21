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
