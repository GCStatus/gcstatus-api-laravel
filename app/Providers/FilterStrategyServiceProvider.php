<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Factories\FilterStrategyFactory;
use App\Contracts\Factories\FilterStrategyFactoryInterface;
use App\Strategies\Filters\{
    TagsFilterStrategy,
    CracksFilterStrategy,
    GenresFilterStrategy,
    CrackersFilterStrategy,
    PlatformsFilterStrategy,
    CategoriesFilterStrategy,
    DevelopersFilterStrategy,
    PublishersFilterStrategy,
    ProtectionsFilterStrategy,
};

class FilterStrategyServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register(): void
    {
        $this->app->singleton(FilterStrategyFactoryInterface::class, function () {
            $registry = new FilterStrategyFactory();

            // Register strategies
            $registry->register('tags', new TagsFilterStrategy());
            $registry->register('cracks', new CracksFilterStrategy());
            $registry->register('genres', new GenresFilterStrategy());
            $registry->register('crackers', new CrackersFilterStrategy());
            $registry->register('platforms', new PlatformsFilterStrategy());
            $registry->register('categories', new CategoriesFilterStrategy());
            $registry->register('developers', new DevelopersFilterStrategy());
            $registry->register('publishers', new PublishersFilterStrategy());
            $registry->register('protections', new ProtectionsFilterStrategy());

            return $registry;
        });
    }
}
