<?php

namespace Tests\Unit\Repositories;

use Mockery;
use Tests\TestCase;
use App\Models\Dlc;
use App\Repositories\DlcRepository;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Contracts\Repositories\DlcRepositoryInterface;
use Illuminate\Database\Eloquent\{Builder, Collection};

class DlcRepositoryTest extends TestCase
{
    /**
     * The dlc repository.
     *
     * @var \App\Contracts\Repositories\DlcRepositoryInterface
     */
    private DlcRepositoryInterface $dlcRepository;

    /**
     * Setup new test environments.
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->dlcRepository = app(DlcRepositoryInterface::class);
    }

    /**
     * Test if DlcRepository uses the Dlc model correctly.
     *
     * @return void
     */
    public function test_Dlc_repository_uses_Dlc_model(): void
    {
        /** @var \App\Repositories\DlcRepository $dlcRepository */
        $dlcRepository = $this->dlcRepository;

        $this->assertInstanceOf(Dlc::class, $dlcRepository->model());
    }

    /**
     * Test if can get all dlcs for admin.
     *
     * @return void
     */
    public function test_if_can_get_all_dlcs_for_admin(): void
    {
        $dlc = Mockery::mock(Dlc::class);
        $builder = Mockery::mock(Builder::class);
        $collection = Mockery::mock(Collection::class);

        $belongsTo = Mockery::mock(BelongsTo::class);
        $belongsTo->shouldReceive('withoutEagerLoads')
            ->once()
            ->andReturnSelf();

        $builder->shouldReceive('with')
            ->once()
            ->with(Mockery::on(function (array $with) use ($belongsTo) {
                if (!isset($with['game']) || !is_callable($with['game'])) {
                    return false;
                }

                $with['game']($belongsTo);

                return true;
            }))->andReturnSelf();

        $builder->shouldReceive('get')
            ->once()
            ->withNoArgs()
            ->andReturn($collection);

        $dlc->shouldReceive('query')
            ->once()
            ->andReturn($builder);

        $repoMock = Mockery::mock(DlcRepository::class)
            ->makePartial()
            ->shouldAllowMockingProtectedMethods();

        $repoMock->shouldReceive('model')
            ->once()
            ->andReturn($dlc);

        /** @var \App\Contracts\Repositories\DlcRepositoryInterface $repoMock */
        $result = $repoMock->allForAdmin();

        $this->assertSame($collection, $result);

        $this->assertEquals(5, Mockery::getContainer()->mockery_getExpectationCount(), 'Mock expectations met.');
    }

    /**
     * Test if can get details for admin.
     *
     * @return void
     */
    public function test_if_can_get_details_for_admin(): void
    {
        $id = 1;

        $dlc = Mockery::mock(Dlc::class);
        $builder = Mockery::mock(Builder::class);

        $belongsTo = Mockery::mock(BelongsTo::class);
        $belongsTo->shouldReceive('withoutEagerLoads')
            ->once()
            ->andReturnSelf();

        $builder->shouldReceive('with')
            ->once()
            ->with(Mockery::on(function (array $with) use ($belongsTo) {
                if (!isset($with['game']) || !is_callable($with['game'])) {
                    return false;
                }

                $with['game']($belongsTo);

                $expectedRelations = [
                    'tags',
                    'genres',
                    'platforms',
                    'categories',
                    'publishers',
                    'developers',
                    'stores.store',
                    'galleries.mediaType',
                ];

                foreach ($expectedRelations as $relation) {
                    if (!in_array($relation, $with, true)) {
                        return false;
                    }
                }

                return true;
            }))->andReturnSelf();

        $builder->shouldReceive('findOrFail')
            ->once()
            ->with($id)
            ->andReturn($dlc);

        $dlc->shouldReceive('query')
            ->once()
            ->andReturn($builder);

        $repoMock = Mockery::mock(DlcRepository::class)
            ->makePartial()
            ->shouldAllowMockingProtectedMethods();

        $repoMock->shouldReceive('model')
            ->once()
            ->andReturn($dlc);

        /** @var \App\Contracts\Repositories\DlcRepositoryInterface $repoMock */
        $result = $repoMock->detailsForAdmin($id);

        $this->assertSame($dlc, $result);

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
