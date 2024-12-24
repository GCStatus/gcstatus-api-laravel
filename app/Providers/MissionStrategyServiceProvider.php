<?php

namespace App\Providers;

use App\Models\MissionRequirement;
use Illuminate\Support\ServiceProvider;
use App\Factories\MissionStrategyFactory;
use App\Strategies\TransactionCountStrategy;
use App\Contracts\Factories\MissionStrategyFactoryInterface;

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
            $registry->register(MissionRequirement::TRANSACTIONS_COUNT_STRATEGY_KEY, new TransactionCountStrategy());

            return $registry;
        });
    }
}
