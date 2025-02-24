<?php

namespace Tests\Unit\Services;

use Mockery;
use Tests\TestCase;
use Mockery\MockInterface;
use App\Models\{Dlc, Game};
use App\Repositories\DlcRepository;
use Illuminate\Database\Eloquent\Collection;
use App\Contracts\Services\DlcServiceInterface;
use App\Contracts\Repositories\DlcRepositoryInterface;
use Illuminate\Database\Eloquent\Relations\MorphToMany;

class DlcServiceTest extends TestCase
{
    /**
     * The dlc repository.
     *
     * @var \Mockery\MockInterface
     */
    private MockInterface $dlcRepository;

    /**
     * The dlc service.
     *
     * @var \App\Contracts\Services\DlcServiceInterface
     */
    private DlcServiceInterface $dlcService;

    /**
     * Setup new test environments.
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->dlcRepository = Mockery::mock(DlcRepositoryInterface::class);

        $this->app->instance(DlcRepositoryInterface::class, $this->dlcRepository);

        $this->dlcService = app(DlcServiceInterface::class);
    }

    /**
     * Test if DlcService uses the Dlc repository correctly.
     *
     * @return void
     */
    public function test_Dlc_repository_uses_Dlc_repository(): void
    {
        $this->app->instance(DlcRepositoryInterface::class, new DlcRepository());

        /** @var \App\Services\DlcService $DlcService */
        $DlcService = app(DlcServiceInterface::class);

        $this->assertInstanceOf(DlcRepository::class, $DlcService->repository());
    }

    /**
     * Test if can get all dlcs for admin.
     *
     * @return void
     */
    public function test_if_can_get_all_dlcs_for_admin(): void
    {
        $collection = Mockery::mock(Collection::class);

        $this->dlcRepository
            ->shouldReceive('allForAdmin')
            ->once()
            ->withNoArgs()
            ->andReturn($collection);

        $result = $this->dlcService->allForAdmin();

        $this->assertSame($collection, $result);

        $this->assertEquals(1, Mockery::getContainer()->mockery_getExpectationCount(), 'Mock expectations met.');
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

        $this->dlcRepository
            ->shouldReceive('detailsForAdmin')
            ->once()
            ->with($id)
            ->andReturn($dlc);

        $result = $this->dlcService->detailsForAdmin($id);

        $this->assertSame($dlc, $result);

        $this->assertEquals(1, Mockery::getContainer()->mockery_getExpectationCount(), 'Mock expectations met.');
    }

    /**
     * Test if can create a new dlc.
     *
     * @return void
     */
    public function test_if_can_create_a_new_dlc(): void
    {
        $dlc = Mockery::mock(Dlc::class);
        $game = Mockery::mock(Game::class);
        $game->shouldReceive('getAttribute')->with('id')->andReturn(1);

        /** @var \App\Models\Game $game */
        $data = [
            'free' => fake()->boolean(),
            'legal' => fake()->text(),
            'about' => clean(fake()->realText()),
            'cover' => 'https://placehold.co/600x400/EEE/31343C',
            'release_date' => fake()->date(),
            'description' => clean(fake()->realText()),
            'title' => fake()->title(),
            'short_description' => fake()->text(),
            'game_id' => $game->id,
        ];

        $this->dlcRepository
            ->shouldReceive('create')
            ->once()
            ->with($data)
            ->andReturn($dlc);

        $result = $this->dlcService->create($data);

        $this->assertSame($dlc, $result);
        $this->assertInstanceOf(Dlc::class, $result);

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
            'free' => fake()->boolean(),
            'legal' => fake()->text(),
            'about' => fake()->realText(),
            'cover' => 'https://placehold.co/600x400/EEE/31343C',
            'release_date' => fake()->date(),
            'description' => fake()->realText(),
            'title' => fake()->title(),
            'short_description' => fake()->text(),
            'tags' => [1, 2],
            'genres' => [3, 4],
            'platforms' => [5, 6],
            'categories' => [7, 8],
            'publishers' => [9, 10],
            'developers' => [11, 12],
        ];

        $expectedData = $data;
        $expectedData['about'] = clean($expectedData['about']);
        $expectedData['description'] = clean($expectedData['description']);

        $dlc = Mockery::mock(Dlc::class);
        $dlc->shouldReceive('getAttribute')->with('id')->andReturn($id);

        $relations = ['tags', 'genres', 'platforms', 'categories', 'publishers', 'developers'];

        foreach ($relations as $relation) {
            $relationMock = Mockery::mock(MorphToMany::class);

            $relationMock->shouldReceive('sync')
                ->once()
                ->with($data[$relation]);

            $dlc->shouldReceive($relation)
                ->once()
                ->andReturn($relationMock);
        }

        $this->dlcRepository
            ->shouldReceive('update')
            ->once()
            ->with($expectedData, $id)
            ->andReturn($dlc);

        $result = $this->dlcService->update($data, $id);

        $this->assertSame($dlc, $result);
        $this->assertInstanceOf(Dlc::class, $result);

        $this->assertEquals(13, Mockery::getContainer()->mockery_getExpectationCount(), 'Mock expectations met.');
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
