<?php

namespace Tests\Unit\Repositories;

use Mockery;
use Tests\TestCase;
use App\Models\Game;
use Illuminate\Support\Carbon;
use App\Repositories\GameRepository;
use Illuminate\Database\Eloquent\{Builder, Collection};
use App\Contracts\Repositories\GameRepositoryInterface;

class GameRepositoryTest extends TestCase
{
    /**
     * The game repository.
     *
     * @var \App\Contracts\Repositories\GameRepositoryInterface
     */
    private GameRepositoryInterface $gameRepository;

    /**
     * Setup new test environments.
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->gameRepository = app(GameRepositoryInterface::class);
    }

    /**
     * Test if GameRepository uses the Game model correctly.
     *
     * @return void
     */
    public function test_game_repository_uses_game_model(): void
    {
        /** @var \App\Repositories\GameRepository $gameRepository */
        $gameRepository = $this->gameRepository;

        $this->assertInstanceOf(Game::class, $gameRepository->model());
    }

    /**
     * Test if can get games by condition.
     *
     * @return void
     */
    public function test_if_can_get_games_by_condition(): void
    {
        $limit = 100;
        $condition = Game::HOT_CONDITION;

        $gameMock = Mockery::mock(Game::class);
        $builder = Mockery::mock(Builder::class);
        $collection = Mockery::mock(Collection::class);

        $builder
            ->shouldReceive('where')
            ->once()
            ->with('condition', $condition)
            ->andReturnSelf();

        $builder
            ->shouldReceive('withIsHearted')
            ->once()
            ->withNoArgs()
            ->andReturnSelf();

        $builder
            ->shouldReceive('limit')
            ->once()
            ->with($limit)
            ->andReturnSelf();

        $builder
            ->shouldReceive('get')
            ->once()
            ->withNoArgs()
            ->andReturn($collection);

        $gameMock->shouldReceive('query')->once()->withNoArgs()->andReturn($builder);

        $repoMock = Mockery::mock(GameRepository::class)
            ->makePartial()
            ->shouldAllowMockingProtectedMethods();

        $repoMock->shouldReceive('model')
            ->once()
            ->withNoArgs()
            ->andReturn($gameMock);

        /** @var \App\Contracts\Repositories\GameRepositoryInterface $repoMock */
        $result = $repoMock->getGamesByCondition($condition, $limit);

        $this->assertEquals($result, $collection);
        $this->assertInstanceOf(Collection::class, $result);

        $this->assertEquals(6, Mockery::getContainer()->mockery_getExpectationCount(), 'Mock expectations met.');
    }

    /**
     * Test if can get upcoming games.
     *
     * @return void
     */
    public function test_if_can_get_upcoming_games(): void
    {
        $limit = 100;

        $gameMock = Mockery::mock(Game::class);
        $builder = Mockery::mock(Builder::class);
        $collection = Mockery::mock(Collection::class);

        $builder
            ->shouldReceive('where')
            ->once()
            ->with('release_date', '>', Mockery::type(Carbon::class))
            ->andReturnSelf();

        $builder
            ->shouldReceive('withIsHearted')
            ->once()
            ->withNoArgs()
            ->andReturnSelf();

        $builder
            ->shouldReceive('limit')
            ->once()
            ->with($limit)
            ->andReturnSelf();

        $builder
            ->shouldReceive('get')
            ->once()
            ->withNoArgs()
            ->andReturn($collection);

        $gameMock->shouldReceive('query')->once()->withNoArgs()->andReturn($builder);

        $repoMock = Mockery::mock(GameRepository::class)
            ->makePartial()
            ->shouldAllowMockingProtectedMethods();

        $repoMock->shouldReceive('model')
            ->once()
            ->withNoArgs()
            ->andReturn($gameMock);

        /** @var \App\Contracts\Repositories\GameRepositoryInterface $repoMock */
        $result = $repoMock->getUpcomingGames($limit);

        $this->assertEquals($collection, $result);
        $this->assertInstanceOf(Collection::class, $result);

        $this->assertEquals(6, Mockery::getContainer()->mockery_getExpectationCount(), 'Mock expectations met.');
    }

    /**
     * Test if can get the most liked games.
     *
     * @return void
     */
    public function test_if_can_get_the_most_liked_games(): void
    {
        $limit = 100;

        $gameMock = Mockery::mock(Game::class);
        $builder = Mockery::mock(Builder::class);
        $collection = Mockery::mock(Collection::class);

        $builder
            ->shouldReceive('orderByDesc')
            ->once()
            ->with('hearts_count')
            ->andReturnSelf();

        $builder
            ->shouldReceive('withIsHearted')
            ->once()
            ->withNoArgs()
            ->andReturnSelf();

        $builder
            ->shouldReceive('limit')
            ->once()
            ->with($limit)
            ->andReturnSelf();

        $builder
            ->shouldReceive('get')
            ->once()
            ->withNoArgs()
            ->andReturn($collection);

        $gameMock->shouldReceive('query')->once()->withNoArgs()->andReturn($builder);

        $repoMock = Mockery::mock(GameRepository::class)
            ->makePartial()
            ->shouldAllowMockingProtectedMethods();

        $repoMock->shouldReceive('model')
            ->once()
            ->withNoArgs()
            ->andReturn($gameMock);

        /** @var \App\Contracts\Repositories\GameRepositoryInterface $repoMock */
        $result = $repoMock->getMostLikedGames($limit);

        $this->assertEquals($collection, $result);
        $this->assertInstanceOf(Collection::class, $result);

        $this->assertEquals(6, Mockery::getContainer()->mockery_getExpectationCount(), 'Mock expectations met.');
    }

    /**
     * Test if can get the next game release.
     *
     * @return void
     */
    public function test_if_can_get_the_next_game_release(): void
    {
        $gameMock = Mockery::mock(Game::class);
        $builder = Mockery::mock(Builder::class);

        $builder
            ->shouldReceive('where')
            ->once()
            ->with('great_release', true)
            ->andReturnSelf();

        $builder
            ->shouldReceive('where')
            ->once()
            ->with('release_date', '>=', Mockery::type(Carbon::class))
            ->andReturnSelf();

        $builder
            ->shouldReceive('first')
            ->once()
            ->withNoArgs()
            ->andReturn($gameMock);

        $gameMock->shouldReceive('query')->once()->withNoArgs()->andReturn($builder);

        $repoMock = Mockery::mock(GameRepository::class)
            ->makePartial()
            ->shouldAllowMockingProtectedMethods();

        $repoMock->shouldReceive('model')
            ->once()
            ->withNoArgs()
            ->andReturn($gameMock);

        /** @var \App\Contracts\Repositories\GameRepositoryInterface $repoMock */
        $result = $repoMock->getNextGreatRelease();

        $this->assertEquals($gameMock, $result);
        $this->assertInstanceOf(Game::class, $result);

        $this->assertEquals(5, Mockery::getContainer()->mockery_getExpectationCount(), 'Mock expectations met.');
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
