<?php

namespace Tests\Unit\Middlewares;

use Mockery;
use App\Models\User;
use Mockery\MockInterface;
use App\Contracts\Services\AuthServiceInterface;
use App\Http\Middleware\ShouldCompleteRegistration;
use Tests\Contracts\Middlewares\BaseMiddlewareTesting;

class ShouldCompleteRegistrationTest extends BaseMiddlewareTesting
{
    /**
     * The auth service.
     *
     * @var \Mockery\MockInterface
     */
    private MockInterface $authService;

    /**
     * The jwt cookie auth middleware.
     *
     * @return string
     */
    public function middleware(): string
    {
        return ShouldCompleteRegistration::class;
    }

    /**
     * Resolve the middleware instance with dependencies.
     *
     * @return \App\Http\Middleware\ShouldCompleteRegistration
     */
    public function resolveMiddleware(): ShouldCompleteRegistration
    {
        $authService = Mockery::mock(AuthServiceInterface::class);
        $this->authService = $authService;

        /** @var \App\Contracts\Services\AuthServiceInterface $authService */
        return new ShouldCompleteRegistration(
            $authService,
        );
    }

    /**
     * Test if can handles valid users.
     *
     * @return void
     */
    public function test_if_it_can_handles_valid_users(): void
    {
        $user = Mockery::mock(User::class);
        $user->shouldReceive('getAttribute')->with('password')->andReturn(fake()->password());

        $this->authService
            ->shouldReceive('getAuthUser')
            ->once()
            ->andReturn($user);

        /** @var \App\Http\Middleware\ShouldCompleteRegistration $middleware */
        $middleware = $this->middleware;

        $response = $middleware->handle($this->request, $this->next);

        $this->assertEquals('Next middleware', $response->getContent());
        $this->assertFalse($response->isRedirect());
    }

    /**
     * Test if can redirect away if user has no password filled up.
     *
     * @return void
     */
    public function test_if_can_redirect_away_if_user_has_no_password_filled_up(): void
    {
        $user = Mockery::mock(User::class);
        $user->shouldReceive('getAttribute')->with('password')->andReturnNull();

        $this->authService
            ->shouldReceive('getAuthUser')
            ->once()
            ->andReturn($user);

        /** @var \App\Http\Middleware\ShouldCompleteRegistration $middleware */
        $middleware = $this->middleware;

        $response = $middleware->handle($this->request, $this->next);

        $this->assertTrue($response->isRedirect());
    }

    /**
     * Tear down the mocks.
     *
     * @return void
     */
    public function tearDown(): void
    {
        Mockery::close();

        parent::tearDown();
    }
}
