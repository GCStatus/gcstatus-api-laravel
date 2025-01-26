<?php

namespace Tests\Unit\Repositories;

use Mockery;
use Tests\TestCase;
use App\Models\Game;
use App\Repositories\GameRepository;
use Illuminate\Support\{Str, Carbon};
use App\Contracts\Strategies\FilterStrategyInterface;
use Illuminate\Database\Eloquent\{Builder, Collection};
use App\Contracts\Repositories\GameRepositoryInterface;
use App\Contracts\Factories\FilterStrategyFactoryInterface;

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
     * Test if can get games calendar.
     *
     * @return void
     */
    public function test_if_can_get_games_calendar(): void
    {
        $gameMock = Mockery::mock(Game::class);
        $builder = Mockery::mock(Builder::class);
        $collection = Mockery::mock(Collection::class);

        $builder
            ->shouldReceive('select')
            ->once()
            ->with(
                'id',
                'slug',
                'title',
                'cover',
                'views',
                'condition',
                'release_date',
            )->andReturnSelf();

        $builder
            ->shouldReceive('withCount')
            ->once()
            ->with('hearts')
            ->andReturnSelf();


        $builder
            ->shouldReceive('withIsHearted')
            ->once()
            ->withNoArgs()
            ->andReturnSelf();

        $builder
            ->shouldReceive('where')
            ->once()
            ->with(Mockery::on(function (callable $closure) {
                $queryMock = Mockery::mock(Builder::class);

                $queryMock
                    ->shouldReceive('whereYear')
                    ->once()
                    ->with('release_date', now()->year)
                    ->andReturnSelf();

                $queryMock
                    ->shouldReceive('orWhereYear')
                    ->once()
                    ->with('release_date', now()->year - 1)
                    ->andReturnSelf();

                $closure($queryMock);

                return true;
            }))->andReturnSelf();

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
        $result = $repoMock->getCalendarGames();

        $this->assertEquals($result, $collection);
        $this->assertInstanceOf(Collection::class, $result);

        $this->assertEquals(9, Mockery::getContainer()->mockery_getExpectationCount(), 'Mock expectations met.');
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
        $builder = Mockery::mock(Builder::class);

        $game->shouldReceive('getAttribute')->with('title')->andReturn($title);
        $game->shouldReceive('getAttribute')->with('slug')->andReturn($slug);

        $builder
            ->shouldReceive('withIsHearted')
            ->once()
            ->andReturnSelf();

        $builder
            ->shouldReceive('with')
            ->once()
            ->with(Mockery::on(function ($relationships) {
                $expectedRelationships = [
                    'support',
                    'dlcs.tags',
                    'developers',
                    'publishers',
                    'dlcs.genres',
                    'reviews.user',
                    'stores.store',
                    'critics.critic',
                    'dlcs.platforms',
                    'dlcs.categories',
                    'dlcs.developers',
                    'dlcs.publishers',
                    'dlcs.stores.store',
                    'torrents.provider',
                    'languages.language',
                    'galleries.mediaType',
                    'dlcs.galleries.mediaType',
                    'requirements.requirementType',
                ];

                foreach ($expectedRelationships as $relationship) {
                    if (!in_array($relationship, $relationships, true)) {
                        return false;
                    }
                }

                return isset($relationships['comments']) && is_callable($relationships['comments']);
            }))->andReturnSelf();

        $builder
            ->shouldReceive('where')
            ->once()
            ->with('slug', $slug)
            ->andReturnSelf();

        $builder
            ->shouldReceive('firstOrFail')
            ->once()
            ->withNoArgs()
            ->andReturn($game);

        $game->shouldReceive('query')->once()->withNoArgs()->andReturn($builder);

        $repoMock = Mockery::mock(GameRepository::class)
            ->makePartial()
            ->shouldAllowMockingProtectedMethods();

        $repoMock->shouldReceive('model')
            ->once()
            ->withNoArgs()
            ->andReturn($game);

        /** @var \App\Contracts\Repositories\GameRepositoryInterface $repoMock */
        $result = $repoMock->details($slug);

        $this->assertEquals($game, $result);
        $this->assertInstanceOf(Game::class, $result);

        $this->assertEquals(6, Mockery::getContainer()->mockery_getExpectationCount(), 'Mock expectations met.');
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
     * Test if can get games by attribute.
     *
     * @return void
     */
    public function test_if_can_get_games_by_attribute(): void
    {
        $limit = 100;
        $attribute = 'tags';
        $value = fake()->word();

        $gameMock = Mockery::mock(Game::class);
        $builder = Mockery::mock(Builder::class);
        $collection = Mockery::mock(Collection::class);
        $mockStrategy = Mockery::mock(FilterStrategyInterface::class);
        $mockFactory = Mockery::mock(FilterStrategyFactoryInterface::class);

        $mockFactory->shouldReceive('resolve')
            ->once()
            ->with($attribute)
            ->andReturn($mockStrategy);

        $mockStrategy->shouldReceive('apply')
            ->once()
            ->with(Mockery::any(), $value)
            ->andReturn($builder);

        $builder->shouldReceive('withIsHearted')->once()->andReturnSelf();
        $builder->shouldReceive('limit')->once()->with($limit)->andReturnSelf();
        $builder->shouldReceive('orderByDesc')->once()->with('release_date')->andReturnSelf();
        $builder->shouldReceive('get')->once()->andReturn($collection);

        $gameMock->shouldReceive('query')->once()->withNoArgs()->andReturn($builder);

        $repoMock = Mockery::mock(GameRepository::class, [$mockFactory])
            ->makePartial()
            ->shouldAllowMockingProtectedMethods();

        $repoMock->shouldReceive('model')
            ->once()
            ->withNoArgs()
            ->andReturn($gameMock);

        $data = ['attribute' => $attribute, 'value' => $value];

        /** @var \App\Contracts\Repositories\GameRepositoryInterface $repoMock */
        $result = $repoMock->findByAttribute($data);

        $this->assertEquals($result, $collection);
        $this->assertInstanceOf(Collection::class, $result);

        $this->assertEquals(8, Mockery::getContainer()->mockery_getExpectationCount(), 'Mock expectations met.');
    }

    /**
     * Test if can search games.
     *
     * @return void
     */
    public function test_if_can_search_games(): void
    {
        $limit = 100;
        $q = fake()->word();

        $gameMock = Mockery::mock(Game::class);
        $builder = Mockery::mock(Builder::class);
        $collection = Mockery::mock(Collection::class);

        $builder
            ->shouldReceive('withIsHearted')
            ->once()
            ->withNoArgs()
            ->andReturnSelf();

        $builder
            ->shouldReceive('where')
            ->once()
            ->with('title', 'LIKE', "%$q%")
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
        $result = $repoMock->search($q);

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
