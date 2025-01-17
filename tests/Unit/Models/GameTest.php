<?php

namespace Tests\Unit\Models;

use App\Models\Game;
use App\Traits\{HasSlug, HasHeart};
use App\Contracts\HasHeartInterface;
use Tests\Contracts\Models\BaseModelTesting;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\{
    HasOne,
    HasMany,
    MorphMany,
    MorphToMany,
};
use Tests\Contracts\Models\{
    ShouldTestCasts,
    ShouldTestConstants,
    ShouldTestTraits,
    ShouldTestFillables,
    ShouldTestRelations,
    ShouldTestInterfaces,
};

class GameTest extends BaseModelTesting implements
    ShouldTestCasts,
    ShouldTestTraits,
    ShouldTestFillables,
    ShouldTestRelations,
    ShouldTestConstants,
    ShouldTestInterfaces
{
    /**
     * The testable model string class.
     *
     * @return class-string
     */
    public function model(): string
    {
        return Game::class;
    }

    /**
     * The fillable tests.
     *
     * @return void
     */
    public function test_fillable_attributes(): void
    {
        $fillable = [
            'age',
            'slug',
            'free',
            'title',
            'cover',
            'about',
            'legal',
            'website',
            'condition',
            'description',
            'release_date',
            'great_release',
            'short_description',
        ];

        $this->assertHasFillables($fillable);
    }

    /**
     * The casts tests.
     *
     * @return void
     */
    public function test_casts_attributes(): void
    {
        $casts = [
            'id' => 'int',
            'free' => 'bool',
            'release_date' => 'date',
            'great_release' => 'bool',
            'deleted_at' => 'datetime',
        ];

        $this->assertHasCasts($casts);
    }

    /**
     * The traits tests.
     *
     * @return void
     */
    public function test_traits_attributes(): void
    {
        $traits = [
            HasSlug::class,
            HasHeart::class,
            HasFactory::class,
            SoftDeletes::class,
        ];

        $this->assertUsesTraits($traits);
    }

    /**
     * The relations tests.
     *
     * @return void
     */
    public function test_relations_attributes(): void
    {
        $relations = [
            'dlcs' => HasMany::class,
            'crack' => HasOne::class,
            'support' => HasOne::class,
            'views' => MorphMany::class,
            'tags' => MorphToMany::class,
            'hearts' => MorphMany::class,
            'torrents' => HasMany::class,
            'stores' => MorphMany::class,
            'reviews' => MorphMany::class,
            'critics' => MorphMany::class,
            'genres' => MorphToMany::class,
            'comments' => MorphMany::class,
            'galleries' => MorphMany::class,
            'languages' => MorphMany::class,
            'platforms' => MorphToMany::class,
            'publishers' => MorphToMany::class,
            'developers' => MorphToMany::class,
            'categories' => MorphToMany::class,
            'requirements' => MorphMany::class,
        ];

        $this->assertHasRelations($relations);
    }

    /**
     * The contract constant attributes that should be tested.
     *
     * @return void
     */
    public function test_constant_attributes(): void
    {
        $expectedConstants = [
            'HOT_CONDITION' => 'hot',
            'SALE_CONDITION' => 'sale',
            'POPULAR_CONDITION' => 'popular',
            'CREATED_AT' => 'created_at',
            'UPDATED_AT' => 'updated_at',
        ];

        $this->assertHasConstants($expectedConstants);
    }

    /**
     * The contract interfaces attributes that should be tested.
     *
     * @return void
     */
    public function test_interfaces_attributes(): void
    {
        $interfaces = [
            HasHeartInterface::class,
        ];

        $this->assertUsesInterfaces($interfaces);
    }
}
