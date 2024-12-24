<?php

namespace Tests\Unit\Models;

use App\Models\MissionRequirement;
use App\Support\Database\CacheQueryBuilder;
use Tests\Contracts\Models\BaseModelTesting;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\{
    HasOne,
    HasMany,
    BelongsTo,
};
use Tests\Contracts\Models\{
    ShouldTestCasts,
    ShouldTestTraits,
    ShouldTestFillables,
    ShouldTestRelations,
    ShouldTestConstants
};

class MissionRequirementTest extends BaseModelTesting implements
    ShouldTestCasts,
    ShouldTestTraits,
    ShouldTestFillables,
    ShouldTestRelations,
    ShouldTestConstants
{
    /**
     * The testable model string class.
     *
     * @return class-string
     */
    public function model(): string
    {
        return MissionRequirement::class;
    }

    /**
     * The fillable tests.
     *
     * @return void
     */
    public function test_fillable_attributes(): void
    {
        $fillable = [
            'key',
            'task',
            'goal',
            'mission_id',
            'description',
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
            HasFactory::class,
            SoftDeletes::class,
            CacheQueryBuilder::class,
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
            'mission' => BelongsTo::class,
            'progresses' => HasMany::class,
            'userProgress' => HasOne::class,
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
            'CREATED_AT' => 'created_at',
            'UPDATED_AT' => 'updated_at',
            'TRANSACTIONS_COUNT_STRATEGY_KEY' => 'make_transactions',
        ];

        $this->assertHasConstants($expectedConstants);
    }
}
