<?php

namespace App\Providers;

use App\Models\{User, Role};
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;
use App\Contracts\Services\AuthServiceInterface;

class PulseServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        Gate::before(function (?User $user, string $ability) {
            return true;
        });

        Gate::define('viewPulse', function (?User $user) {
            $user = app(AuthServiceInterface::class)->getAuthUser();

            return $user->hasRole(Role::TECHNOLOGY_ROLE_ID);
        });
    }
}
