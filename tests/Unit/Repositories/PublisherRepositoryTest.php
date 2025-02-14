<?php

namespace Tests\Unit\Repositories;

use Mockery;
use Tests\TestCase;
use App\Models\Publisher;
use App\Repositories\PublisherRepository;
use Illuminate\Database\Eloquent\Builder;
use App\Contracts\Repositories\PublisherRepositoryInterface;

class PublisherRepositoryTest extends TestCase
{
    /**
     * The publisher repository.
     *
     * @var \App\Contracts\Repositories\PublisherRepositoryInterface
     */
    private PublisherRepositoryInterface $publisherRepository;

    /**
     * Setup new test environments.
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->publisherRepository = app(PublisherRepositoryInterface::class);
    }

    /**
     * Test if PublisherRepository uses the Publisher model correctly.
     *
     * @return void
     */
    public function test_Publisher_repository_uses_Publisher_model(): void
    {
        /** @var \App\Repositories\PublisherRepository $publisherRepository */
        $publisherRepository = $this->publisherRepository;

        $this->assertInstanceOf(Publisher::class, $publisherRepository->model());
    }

    /**
     * Test if can check if Publisher exists by name.
     *
     * @return void
     */
    public function test_if_can_check_if_Publisher_exists_by_name(): void
    {
        $name = fake()->name();

        $builder = Mockery::mock(Builder::class);
        $publisher = Mockery::mock(Publisher::class);

        $builder
            ->shouldReceive('where')
            ->once()
            ->with('name', $name)
            ->andReturnSelf();

        $builder
            ->shouldReceive('exists')
            ->once()
            ->withNoArgs()
            ->andReturnTrue();

        $publisher
            ->shouldReceive('query')
            ->once()
            ->withNoArgs()
            ->andReturn($builder);

        $repoMock = Mockery::mock(PublisherRepository::class)
            ->makePartial()
            ->shouldAllowMockingProtectedMethods();

        $repoMock->shouldReceive('model')
            ->once()
            ->andReturn($publisher);

        /** @var \App\Contracts\Repositories\PublisherRepositoryInterface $repoMock */
        $repoMock->existsByName($name);

        $this->assertEquals(4, Mockery::getContainer()->mockery_getExpectationCount(), 'Mock expectations met.');
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
