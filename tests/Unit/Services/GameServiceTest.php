<?php

namespace Tests\Unit\Services;

use Mockery;
use Tests\TestCase;
use App\Models\Game;
use Mockery\MockInterface;
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
        $this->app->instance(GameRepositoryInterface::class, new GameRepository());

        /** @var \App\Services\GameService $gameService */
        $gameService = app(GameServiceInterface::class);

        $this->assertInstanceOf(GameRepository::class, $gameService->repository());
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
