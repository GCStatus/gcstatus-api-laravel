<?php

namespace Tests\Unit\Models;

use App\Models\Game;
use App\Traits\HasSlug;
use Tests\Contracts\Models\BaseModelTesting;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\{
    HasOne,
    HasMany,
    MorphMany,
};
use Tests\Contracts\Models\{
    ShouldTestCasts,
    ShouldTestTraits,
    ShouldTestFillables,
    ShouldTestRelations,
};

class GameTest extends BaseModelTesting implements
    ShouldTestCasts,
    ShouldTestTraits,
    ShouldTestFillables,
    ShouldTestRelations
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
            'tags' => MorphMany::class,
            'views' => MorphMany::class,
            'hearts' => MorphMany::class,
            'genres' => MorphMany::class,
            'torrents' => HasMany::class,
            'stores' => MorphMany::class,
            'reviews' => MorphMany::class,
            'critics' => MorphMany::class,
            'comments' => MorphMany::class,
            'platforms' => MorphMany::class,
            'galleries' => MorphMany::class,
            'languages' => MorphMany::class,
            'categories' => MorphMany::class,
            'publishers' => MorphMany::class,
            'developers' => MorphMany::class,
            'requirements' => MorphMany::class,
        ];

        $this->assertHasRelations($relations);
    }
}
