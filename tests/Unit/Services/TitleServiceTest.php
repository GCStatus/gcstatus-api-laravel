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
