<?php

namespace Tests\Unit\Middlewares;

use Mockery;
use App\Models\User;
use Mockery\MockInterface;
use App\Http\Middleware\BlockableProtection;
use App\Exceptions\User\BlockedUserException;
use App\Contracts\Services\AuthServiceInterface;
use Tests\Contracts\Middlewares\BaseMiddlewareTesting;

class BlockableProtectionTest extends BaseMiddlewareTesting
{
    /**
     * The auth service.
     *
     * @var \Mockery\MockInterface
     */
    private MockInterface $authService;

    /**
     * Setup new test environments.
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->authService = Mockery::mock(AuthServiceInterface::class);

        $this->app->instance(AuthServiceInterface::class, $this->authService);
    }

    /**
     * @inheritDoc
     */
    public function middleware(): string
    {
        return BlockableProtection::class;
    }

    /**
     * @inheritDoc
     */
    public function resolveMiddleware(): BlockableProtection
    {
        return new BlockableProtection();
    }

    /**
     * Test if can throw an exception if user is blocked.
     *
     * @return void
     */
    public function test_if_can_throw_an_exception_if_user_is_blocked(): void
    {
        $user = Mockery::mock(User::class);
        $user->shouldReceive('getAttribute')->with('blocked')->andReturnTrue();

        $this->authService
            ->shouldReceive('getAuthUser')
            ->once()
            ->withNoArgs()
            ->andReturn($user);

        $this->authService
            ->shouldReceive('clearAuthenticationCookies')
            ->once()
            ->withNoArgs()
            ->andReturnNull();

        $this->expectException(BlockedUserException::class);
        $this->expectExceptionMessage('You are blocked from GCStatus. If you do not agree with this, please, contact the support.');

        $this->resolveMiddleware()->handle($this->request, $this->next);
    }

    /**
     * Test if can pass if user is not blocked.
     *
     * @return void
     */
    public function test_if_can_pass_if_user_is_not_blocked(): void
    {
        $user = Mockery::mock(User::class);
        $user->shouldReceive('getAttribute')->with('blocked')->andReturnFalse();

        $this->authService
            ->shouldReceive('getAuthUser')
            ->once()
            ->withNoArgs()
            ->andReturn($user);

        $this->authService->shouldNotReceive('clearAuthenticationCookies');

        $response = $this->resolveMiddleware()->handle($this->request, $this->next);

        $this->assertEquals('Next middleware', $response->getContent());
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
