<?php

namespace Tests\Unit\Repositories;

use Mockery;
use Tests\TestCase;
use App\Models\{Game, Heartable};
use App\Repositories\HeartableRepository;
use Illuminate\Database\Eloquent\Builder;
use App\Contracts\Repositories\HeartableRepositoryInterface;

class HeartableRepositoryTest extends TestCase
{
    /**
     * The game repository.
     *
     * @var \App\Contracts\Repositories\HeartableRepositoryInterface
     */
    private HeartableRepositoryInterface $heartableRepository;

    /**
     * Setup new test environments.
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->heartableRepository = app(HeartableRepositoryInterface::class);
    }

    /**
     * Test if heartableRepository uses the Heartable model correctly.
     *
     * @return void
     */
    public function test_game_repository_uses_heartable_model(): void
    {
        /** @var \App\Repositories\HeartableRepository $heartableRepository */
        $heartableRepository = $this->heartableRepository;

        $this->assertInstanceOf(Heartable::class, $heartableRepository->model());
    }

    /**
     * Test if can find a heartable by user.
     *
     * @return void
     */
    public function test_if_can_find_a_heartable_by_user(): void
    {
        $userId = 1;

        $data = [
            'heartable_id' => 1,
            'heartable_type' => Game::class,
        ];

        $builder = Mockery::mock(Builder::class);
        $heartable = Mockery::mock(Heartable::class);

        $builder
            ->shouldReceive('where')
            ->once()
            ->with('user_id', $userId)
            ->andReturnSelf();

        $builder
            ->shouldReceive('where')
            ->once()
            ->with('heartable_id', $data['heartable_id'])
            ->andReturnSelf();

        $builder
            ->shouldReceive('where')
            ->once()
            ->with('heartable_type', $data['heartable_type'])
            ->andReturnSelf();

        $builder
            ->shouldReceive('first')
            ->once()
            ->withNoArgs()
            ->andReturn($heartable);

        $heartable->shouldReceive('query')->once()->withNoArgs()->andReturn($builder);

        $repoMock = Mockery::mock(HeartableRepository::class)
            ->makePartial()
            ->shouldAllowMockingProtectedMethods();

        $repoMock->shouldReceive('model')
            ->once()
            ->withNoArgs()
            ->andReturn($heartable);

        /** @var \App\Contracts\Repositories\HeartableRepositoryInterface $repoMock */
        $result = $repoMock->findByUser($userId, $data);

        $this->assertEquals($result, $heartable);
        $this->assertInstanceOf(Heartable::class, $result);

        $this->assertEquals(6, Mockery::getContainer()->mockery_getExpectationCount(), 'Mock expectations met.');
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
