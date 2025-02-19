<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Exceptions\User\BlockedUserException;
use Symfony\Component\HttpFoundation\Response;
use App\Contracts\Services\AuthServiceInterface;

class BlockableProtection
{
    /**
     * The auth service.
     *
     * @var \App\Contracts\Services\AuthServiceInterface
     */
    private AuthServiceInterface $authService;

    /**
     * Create a new middleware instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->authService = app(AuthServiceInterface::class);
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $this->authService->getAuthUser();

        if ($user->blocked) {
            $this->authService->clearAuthenticationCookies();

            throw new BlockedUserException();
        }

        return $next($request);
    }
}
