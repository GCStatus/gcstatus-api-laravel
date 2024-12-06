<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Factories\MissionStrategyFactory;
use App\Contracts\Factories\MissionStrategyFactoryInterface;
use App\Strategies\{
    TransactionCountStrategy,
};

class MissionStrategyServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register(): void
    {
        $this->app->singleton(MissionStrategyFactoryInterface::class, function () {
            $registry = new MissionStrategyFactory();

            // Register strategies
            $registry->register('make_transactions', new TransactionCountStrategy());

            return $registry;
        });
    }
}
