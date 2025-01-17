<?php

namespace Tests\Unit\Services;

use Mockery;
use Tests\TestCase;
use App\Models\Banner;
use Mockery\MockInterface;
use Illuminate\Database\Eloquent\Collection;
use App\Contracts\Services\BannerServiceInterface;
use App\Contracts\Repositories\BannerRepositoryInterface;

class BannerServiceTest extends TestCase
{
    /**
     * The banner repository.
     *
     * @var \Mockery\MockInterface
     */
    private MockInterface $bannerRepository;

    /**
     * The banner service.
     *
     * @var \App\Contracts\Services\BannerServiceInterface
     */
    private BannerServiceInterface $bannerService;

    /**
     * Setup new test environments.
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->bannerRepository = Mockery::mock(BannerRepositoryInterface::class);

        $this->app->instance(BannerRepositoryInterface::class, $this->bannerRepository);

        $this->bannerService = app(BannerServiceInterface::class);
    }

    /**
     * Test if can get all banners based on component.
     *
     * @return void
     */
    public function test_if_can_get_all_banners_based_on_component(): void
    {
        $component = Banner::HOME_HEADER_CAROUSEL_BANNERS;

        $collection = Mockery::mock(Collection::class);

        $this->bannerRepository
            ->shouldReceive('allBasedOnComponent')
            ->once()
            ->with($component)
            ->andReturn($collection);

        $result = $this->bannerService->allBasedOnComponent($component);

        $this->assertEquals($collection, $result);
        $this->assertInstanceOf(Collection::class, $result);

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
