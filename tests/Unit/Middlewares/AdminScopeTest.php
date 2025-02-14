<?php

namespace Tests\Unit\Middlewares;

use Mockery;
use App\Models\User;
use Mockery\MockInterface;
use App\Http\Middleware\AdminScope;
use App\Exceptions\NotFoundException;
use Tests\Contracts\Middlewares\BaseMiddlewareTesting;
use App\Contracts\Services\{
    AuthServiceInterface,
    PermissionServiceInterface,
};

class AdminScopeTest extends BaseMiddlewareTesting
{
    /**
     * The auth service.
     *
     * @var \Mockery\MockInterface
     */
    private MockInterface $authService;

    /**
     * The permission service.
     *
     * @var \Mockery\MockInterface
     */
    private MockInterface $permissionService;

    /**
     * Setup new test environments.
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->authService = Mockery::mock(AuthServiceInterface::class);
        $this->permissionService = Mockery::mock(PermissionServiceInterface::class);

        $this->app->instance(AuthServiceInterface::class, $this->authService);
        $this->app->instance(PermissionServiceInterface::class, $this->permissionService);
    }

    /**
     * @inheritDoc
     */
    public function middleware(): string
    {
        return AdminScope::class;
    }

    /**
     * @inheritDoc
     */
    public function resolveMiddleware(): AdminScope
    {
        return new AdminScope();
    }

    /**
     * Test if can pass if user has given permissions.
     *
     * @return void
     */
    public function test_if_can_pass_if_user_has_given_permissions(): void
    {
        $permissions = 'permission:1,permission:2';

        $exploded = explode(',', $permissions);

        $user = Mockery::mock(User::class);

        $this->authService
            ->shouldReceive('getAuthUser')
            ->once()
            ->withNoArgs()
            ->andReturn($user);

        $this->permissionService
            ->shouldReceive('hasAllPermissions')
            ->once()
            ->with($user, $exploded)
            ->andReturnTrue();

        $response = $this->resolveMiddleware()->handle($this->request, $this->next, ...$exploded);

        $this->assertEquals('Next middleware', $response->getContent());
    }

    /**
     * Test if can't pass if user has not given permissions.
     *
     * @return void
     */
    public function test_if_cant_pass_if_user_has_not_given_permissions(): void
    {
        $permissions = 'permission:1,permission:2';

        $exploded = explode(',', $permissions);

        $user = Mockery::mock(User::class);

        $this->authService
            ->shouldReceive('getAuthUser')
            ->once()
            ->withNoArgs()
            ->andReturn($user);

        $this->permissionService
            ->shouldReceive('hasAllPermissions')
            ->once()
            ->with($user, $exploded)
            ->andReturnFalse();

        $this->expectException(NotFoundException::class);

        $this->resolveMiddleware()->handle($this->request, $this->next, ...$exploded);
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
