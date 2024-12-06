<?php

namespace App\Providers;

use App\Models\Title;
use App\Strategies\TitleRewardStrategy;
use Illuminate\Support\ServiceProvider;
use App\Factories\RewardStrategyFactory;
use App\Contracts\Factories\RewardStrategyFactoryInterface;

class RewardStrategyServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register(): void
    {
        $this->app->singleton(RewardStrategyFactoryInterface::class, function () {
            $factory = new RewardStrategyFactory();

            $factory->register(
                Title::class,
                new TitleRewardStrategy(),
            );

            return $factory;
        });
    }
}
