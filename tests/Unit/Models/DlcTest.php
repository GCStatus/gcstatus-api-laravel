<?php

namespace Tests\Unit\Models;

use App\Models\Dlc;
use App\Traits\HasSlug;
use Tests\Contracts\Models\BaseModelTesting;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\{BelongsTo, MorphMany, MorphToMany};
use Tests\Contracts\Models\{
    ShouldTestCasts,
    ShouldTestTraits,
    ShouldTestFillables,
    ShouldTestRelations,
};

class DlcTest extends BaseModelTesting implements
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
        return Dlc::class;
    }

    /**
     * The fillable tests.
     *
     * @return void
     */
    public function test_fillable_attributes(): void
    {
        $fillable = [
            'slug',
            'free',
            'cover',
            'about',
            'legal',
            'title',
            'game_id',
            'description',
            'release_date',
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
            'game' => BelongsTo::class,
            'tags' => MorphToMany::class,
            'stores' => MorphMany::class,
            'genres' => MorphToMany::class,
            'galleries' => MorphMany::class,
            'platforms' => MorphToMany::class,
            'publishers' => MorphToMany::class,
            'developers' => MorphToMany::class,
            'categories' => MorphToMany::class,
        ];

        $this->assertHasRelations($relations);
    }
}
