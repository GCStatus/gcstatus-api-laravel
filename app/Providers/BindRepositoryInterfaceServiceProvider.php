<?php

namespace App\Providers;

use App\Repositories\GameRepository;
use App\Contracts\Repositories\GameRepositoryInterface;
use App\Contracts\Factories\FilterStrategyFactoryInterface;

class BindRepositoryInterfaceServiceProvider extends BaseInterfaceBindServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register(): void
    {
        $this->bindRepositoryInterfacesToImplementations();

        $this->app->singleton(GameRepositoryInterface::class, fn () => new GameRepository(
            app(FilterStrategyFactoryInterface::class),
        ));
    }

    /**
     * Bind the repository interfaces.
     *
     * @return void
     */
    private function bindRepositoryInterfacesToImplementations(): void
    {
        $this->bindInterfacesToImplementations(
            'Contracts/Repositories',
            'App\\Contracts\\Repositories',
            'App\\Repositories',
        );
    }
}
