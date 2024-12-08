<?php

namespace Tests\Unit\Repositories;

use Mockery;
use Tests\TestCase;
use App\Models\Title;
use Illuminate\Database\Eloquent\Collection;
use PHPUnit\Framework\Attributes\RunInSeparateProcess;
use App\Contracts\Repositories\TitleRepositoryInterface;

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
        $titleMock = Mockery::mock('overload:' . Title::class);

        $titlesCollection = Collection::make([$titleMock]);

        $titleMock
            ->shouldReceive('all')
            ->once()
            ->withNoArgs()
            ->andReturn($titlesCollection);

        $result = $this->titleRepository->allForUser();

        $this->assertSame($titlesCollection, $result);
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
