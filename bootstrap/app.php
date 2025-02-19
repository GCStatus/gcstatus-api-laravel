<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Application;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Foundation\Configuration\{Exceptions, Middleware};
use App\Http\Middleware\{
    AdminScope,
    JwtCookieAuth,
    ForceJsonAccept,
    MidJwtCookieAuth,
    BlockableProtection,
    ShouldCompleteRegistration,
};

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        health: '/',
        apiPrefix: '',
        api: __DIR__ . '/../routes/api.php',
        commands: __DIR__ . '/../routes/console.php',
        channels: __DIR__ . '/../routes/channels.php',
        then: function () {
            Route::middleware(['api.auth', 'blockable.protect'])->group(function () {
                Route::prefix('')->group(base_path('routes/auth.php'));
                Route::prefix('admin')->group(base_path('routes/admin.php'));
            });
        },
    )->withMiddleware(function (Middleware $middleware) {
        $middleware->append([
            ForceJsonAccept::class,
            AddQueuedCookiesToResponse::class,
        ])->alias([
            'scopes' => AdminScope::class,
            'api.auth' => JwtCookieAuth::class,
            'api.mid.auth' => MidJwtCookieAuth::class,
            'blockable.protect' => BlockableProtection::class,
            'registration.should.complete' => ShouldCompleteRegistration::class,
        ])->trustProxies(at: '*');
    })->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
