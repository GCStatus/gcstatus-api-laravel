<?php

namespace Tests\Unit\Repositories;

use Mockery;
use Tests\TestCase;
use App\Models\Banner;
use Illuminate\Database\Eloquent\{Builder, Collection};
use App\Contracts\Repositories\BannerRepositoryInterface;
use PHPUnit\Framework\Attributes\RunInSeparateProcess;

class BannerRepositoryTest extends TestCase
{
    /**
     * The banner repository.
     *
     * @var \App\Contracts\Repositories\BannerRepositoryInterface
     */
    private BannerRepositoryInterface $bannerRepository;

    /**
     * Setup new test environments.
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->bannerRepository = app(BannerRepositoryInterface::class);
    }

    /**
     * Test if can get all banners based on component.
     *
     * @return void
     */
    #[RunInSeparateProcess]
    public function test_if_can_get_all_banners_based_on_component(): void
    {
        $component = 'home-header-carousel';

        $builder = Mockery::mock(Builder::class);
        $collection = Mockery::mock(Collection::class);

        $bannerMock = Mockery::mock('alias:' . Banner::class);
        $bannerMock
            ->shouldReceive('query')
            ->once()
            ->withNoArgs()
            ->andReturn($builder);

        $builder
            ->shouldReceive('where')
            ->once()
            ->with('component', $component)
            ->andReturnSelf();

        $builder
            ->shouldReceive('get')
            ->once()
            ->withNoArgs()
            ->andReturn($collection);

        $result = $this->bannerRepository->allBasedOnComponent($component);

        $this->assertEquals($collection, $result);
        $this->assertInstanceOf(Collection::class, $result);
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
