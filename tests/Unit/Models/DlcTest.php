<?php

namespace Tests\Unit\Models;

use App\Models\Dlc;
use App\Traits\HasSlug;
use Tests\Contracts\Models\BaseModelTesting;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\{BelongsTo, MorphMany};
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
            'name',
            'slug',
            'free',
            'cover',
            'about',
            'legal',
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
            'tags' => MorphMany::class,
            'genres' => MorphMany::class,
            'stores' => MorphMany::class,
            'platforms' => MorphMany::class,
            'galleries' => MorphMany::class,
            'categories' => MorphMany::class,
            'publishers' => MorphMany::class,
            'developers' => MorphMany::class,
        ];

        $this->assertHasRelations($relations);
    }
}
