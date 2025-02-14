<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Exceptions\NotFoundException;
use Symfony\Component\HttpFoundation\Response;
use App\Contracts\Services\{
    AuthServiceInterface,
    PermissionServiceInterface,
};

class AdminScope
{
    /**
     * The auth service.
     *
     * @var \App\Contracts\Services\AuthServiceInterface
     */
    private AuthServiceInterface $authService;

    /**
     * The permission service.
     *
     * @var \App\Contracts\Services\PermissionServiceInterface
     */
    private PermissionServiceInterface $permissionService;

    /**
     * Create a new class instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->authService = app(AuthServiceInterface::class);
        $this->permissionService = app(PermissionServiceInterface::class);
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string ...$scopes): Response
    {
        $user = $this->authService->getAuthUser();

        /** @var list<string> $scopes */
        /** @var bool $hasPermissions */
        $hasPermissions = $this->permissionService->hasAllPermissions($user, $scopes);

        if ($hasPermissions) {
            return $next($request);
        }

        throw new NotFoundException();
    }
}
