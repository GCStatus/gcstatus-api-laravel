<?php

namespace Tests\Unit\Services;

use Mockery;
use Tests\TestCase;
use Mockery\MockInterface;
use App\Services\CookieService;
use App\Contracts\Services\CookieServiceInterface;
use App\Contracts\Repositories\CookieRepositoryInterface;

class CookieServiceTest extends TestCase
{
    /**
     * The abstract service.
     *
     * @var \App\Contracts\Services\CookieServiceInterface
     */
    private CookieServiceInterface $cookieService;

    /**
     * The cookie repository mock interface.
     *
     * @var \Mockery\MockInterface
     */
    private MockInterface $mockRepository;

    /**
     * The testable cookie key.
     *
     * @var string
     */
    private const COOKIE_KEY = 'fake';

    /**
     * Setup new test environments.
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->mockRepository = Mockery::mock(CookieRepositoryInterface::class);

        /** @var \App\Contracts\Repositories\CookieRepositoryInterface $mockRepository */
        $mockRepository = $this->mockRepository;
        $this->cookieService = new CookieService($mockRepository);
    }

    /**
     * Test if can enqueue a cookie with the correct parameters.
     *
     * @return void
     */
    public function test_if_can_enqueue_cookie_with_correct_parameters(): void
    {
        $key = self::COOKIE_KEY;
        $queueable = fake()->word();

        $this->mockRepository
            ->shouldReceive('queue')
            ->once()
            ->with($key, $queueable, 60, '/', 'localhost', false, false);

        $this->cookieService->queue($key, $queueable, 60, '/', 'localhost', false, false);
    }

    /**
     * Test if can remove (forget) a cookie.
     *
     * @return void
     */
    public function test_if_can_forget_cookie(): void
    {
        $key = self::COOKIE_KEY;

        $this->mockRepository
            ->shouldReceive('forget')
            ->once()
            ->with($key);

        $this->cookieService->forget($key);
    }

    /**
     * Test if can check existence of a cookie.
     *
     * @return void
     */
    public function test_if_can_check_cookie_existence(): void
    {
        $key = self::COOKIE_KEY;

        $this->mockRepository
            ->shouldReceive('has')
            ->once()
            ->with($key)
            ->andReturnTrue();

        $this->assertTrue($this->cookieService->has($key));
    }

    /**
     * Test if can retrieve a cookie's value.
     *
     * @return void
     */
    public function test_if_can_get_cookie_value(): void
    {
        $key = self::COOKIE_KEY;
        $value = 'test_value';

        $this->mockRepository
            ->shouldReceive('get')
            ->once()
            ->with($key)
            ->andReturn($value);

        $this->assertEquals($value, $this->cookieService->get($key));
    }
}
