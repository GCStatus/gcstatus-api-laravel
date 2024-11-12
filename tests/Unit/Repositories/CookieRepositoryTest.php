<?php

namespace Tests\Unit\Repositories;

use Mockery;
use Tests\TestCase;
use Illuminate\Support\Facades\Cookie;
use App\Contracts\Repositories\CookieRepositoryInterface;

class CookieRepositoryTest extends TestCase
{
    /**
     * The abstract repository.
     *
     * @var \App\Contracts\Repositories\CookieRepositoryInterface
     */
    private CookieRepositoryInterface $repository;

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

        $this->repository = app(CookieRepositoryInterface::class);
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

        Cookie::shouldReceive('queue')
            ->once()
            ->with($key, $queueable, 60, '/', 'localhost', false, false);

        Cookie::shouldReceive('hasQueued')
            ->once()
            ->with($key)
            ->andReturnTrue();

        $this->repository->queue($key, $queueable, 60, '/', 'localhost', false, false);

        $this->assertTrue(Cookie::hasQueued($key));
    }

    /**
     * Test if can remove (forget) a cookie.
     *
     * @return void
     */
    public function test_if_can_forget_cookie(): void
    {
        $key = self::COOKIE_KEY;

        $forgottenCookie = Cookie::forget($key);

        Cookie::shouldReceive('forget')
            ->once()
            ->with($key)
            ->andReturn($forgottenCookie);

        Cookie::shouldReceive('queue')
            ->once()
            ->with($forgottenCookie);

        $this->repository->forget($key);
    }

    /**
     * Test if can check existence of a cookie.
     *
     * @return void
     */
    public function test_if_can_check_cookie_existence(): void
    {
        $key = self::COOKIE_KEY;

        $mockCookie = Mockery::mock(\Symfony\Component\HttpFoundation\Cookie::class);
        $mockCookie->shouldReceive('getName')->andReturn($key);

        Cookie::shouldReceive('getQueuedCookies')
            ->once()
            ->andReturn([$mockCookie]);

        $this->assertTrue($this->repository->has($key));
    }

    /**
     * Test if can retrieve a cookie's value.
     *
     * @return void
     */
    public function test_if_can_get_cookie_value(): void
    {
        $key = self::COOKIE_KEY;
        $queueable = fake()->word();

        $mockCookie = Mockery::mock(\Symfony\Component\HttpFoundation\Cookie::class);
        $mockCookie->shouldReceive('getName')->andReturn($key);
        $mockCookie->shouldReceive('getValue')->andReturn($queueable);

        Cookie::shouldReceive('getQueuedCookies')
            ->once()
            ->andReturn([$mockCookie]);

        $this->assertEquals($queueable, $this->repository->get($key));
    }
}
