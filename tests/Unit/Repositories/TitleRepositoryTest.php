<?php

namespace Tests\Unit\Repositories;

use Mockery;
use Tests\TestCase;
use App\Models\Title;
use PHPUnit\Framework\Attributes\RunInSeparateProcess;
use App\Contracts\Repositories\TitleRepositoryInterface;
use Illuminate\Database\Eloquent\{Builder, Collection, ModelNotFoundException};

class TitleRepositoryTest extends TestCase
{
    /**
     * The title repository.
     *
     * @var \App\Contracts\Repositories\TitleRepositoryInterface
     */
    private TitleRepositoryInterface $titleRepository;

    /**
     * Setup a new test environments.
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->titleRepository = app(TitleRepositoryInterface::class);
    }

    /**
     * Test if can get all titles for auth user.
     *
     * @return void
     */
    #[RunInSeparateProcess]
    public function test_if_can_get_all_titles_for_auth_user(): void
    {
        $builderMock = Mockery::mock(Builder::class);
        $titleMock = Mockery::mock('overload:' . Title::class);

        $titlesCollection = Collection::make([$titleMock]);

        $builderMock
            ->shouldReceive('with')
            ->once()
            ->with('rewardable.sourceable.requirements.userProgress')
            ->andReturnSelf();

        $builderMock
            ->shouldReceive('get')
            ->once()
            ->withNoArgs()
            ->andReturn($titlesCollection);

        $titleMock->shouldReceive('query')->once()->withNoArgs()->andReturn($builderMock);

        $result = $this->titleRepository->allForUser();

        $this->assertSame($titlesCollection, $result);
    }

    /**
     * Test if can find title on find or fail.
     *
     * @return void
     */
    #[RunInSeparateProcess]
    public function test_if_can_find_title_on_find_or_fail(): void
    {
        $titleId = 1;

        $title = Mockery::mock('overload:' . Title::class);

        $title->shouldReceive('getAttribute')->with('id')->andReturn($titleId);

        $title->shouldReceive('findOrFail')->once()->with($titleId)->andReturnSelf();

        $result = $this->titleRepository->findOrFail($titleId);

        $this->assertEquals($title, $result);
        $this->assertInstanceOf(Title::class, $result);

        $this->assertEquals(1, Mockery::getContainer()->mockery_getExpectationCount(), 'Mock expectations met.');
    }

    /**
     * Test if can fail if title doesn't exist on find or fail.
     *
     * @return void
     */
    #[RunInSeparateProcess]
    public function test_if_can_fail_if_title_doesnt_exist_on_find_or_fail(): void
    {
        $title = Mockery::mock('overload:' . Title::class);

        $title->shouldReceive('getAttribute')->with('id')->andReturn(2);

        $title->shouldReceive('findOrFail')->once()->with(1)->andThrow(ModelNotFoundException::class);

        $this->expectException(ModelNotFoundException::class);

        $this->titleRepository->findOrFail(1);

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
