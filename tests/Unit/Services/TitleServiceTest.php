<?php

namespace Tests\Unit\Services;

use Mockery;
use Tests\TestCase;
use App\Models\Title;
use Mockery\MockInterface;
use Illuminate\Database\Eloquent\Collection;
use App\Contracts\Services\TitleServiceInterface;
use App\Contracts\Repositories\TitleRepositoryInterface;

class TitleServiceTest extends TestCase
{
    /**
     * The title repository.
     *
     * @var \Mockery\MockInterface
     */
    private MockInterface $titleRepository;

    /**
     * The title service.
     *
     * @var \App\Contracts\Services\TitleServiceInterface
     */
    private TitleServiceInterface $titleService;

    /**
     * Setup new test environments.
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->titleRepository = Mockery::mock(TitleRepositoryInterface::class);

        $this->app->instance(TitleRepositoryInterface::class, $this->titleRepository);

        $this->titleService = app(TitleServiceInterface::class);
    }

    /**
     * Test if can get all titles for authenticated user.
     *
     * @return void
     */
    public function test_if_can_get_all_titles_for_authenticated_user(): void
    {
        $title = Mockery::mock(Title::class);
        $titleCollection = Collection::make([$title]);

        $this->titleRepository
            ->shouldReceive('allForUser')
            ->once()
            ->withNoArgs()
            ->andReturn($titleCollection);

        $result = $this->titleService->allForUser();

        $this->assertSame($result, $titleCollection);
    }

    /**
     * Test if can find title on find or fail.
     *
     * @return void
     */
    public function test_if_can_find_title_on_find_or_fail(): void
    {
        $id = 1;

        $title = Mockery::mock(Title::class);

        $this->titleRepository
            ->shouldReceive('findOrFail')
            ->once()
            ->with($id)
            ->andReturn($title);

        $result = $this->titleService->findOrFail($id);

        $this->assertEquals($title, $result);
        $this->assertInstanceOf(Title::class, $result);

        $this->assertEquals(1, Mockery::getContainer()->mockery_getExpectationCount(), 'Mock expectations met.');
    }

    /**
     * Tear down test environment.
     *
     * @return void
     */
    public function tearDown(): void
    {
        Mockery::close();

        parent::tearDown();
    }
}
