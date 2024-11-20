<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Application;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Foundation\Configuration\{Exceptions, Middleware};
use App\Http\Middleware\{JwtCookieAuth, ForceJsonAccept, ShouldCompleteRegistration};

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        health: '/',
        apiPrefix: '',
        api: __DIR__ . '/../routes/api.php',
        commands: __DIR__ . '/../routes/console.php',
        then: function () {
            Route::middleware('api.auth')->group(base_path('routes/auth.php'));
        },
    )->withMiddleware(function (Middleware $middleware) {
        $middleware->append([
            ForceJsonAccept::class,
            AddQueuedCookiesToResponse::class,
        ])->alias([
            'api.auth' => JwtCookieAuth::class,
            'registration.should.complete' => ShouldCompleteRegistration::class,
        ]);
    })->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
