<?php

namespace Tests\Unit\Models;

use App\Models\TransactionType;
use App\Support\Database\CacheQueryBuilder;
use Tests\Contracts\Models\BaseModelTesting;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Tests\Contracts\Models\{
    ShouldTestCasts,
    ShouldTestConstants,
    ShouldTestTraits,
    ShouldTestFillables,
    ShouldTestRelations,
};

class TransactionTypeTest extends BaseModelTesting implements
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
        return TransactionType::class;
    }

    /**
     * The fillable tests.
     *
     * @return void
     */
    public function test_fillable_attributes(): void
    {
        $fillable = [
            'type',
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
            'transactions' => HasMany::class,
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
            'SUBTRACTION_TYPE_ID' => 1,
            'ADDITION_TYPE_ID' => 2,
            'SUBTRACTION_TYPE' => 'subtraction',
            'ADDITION_TYPE' => 'addition',
            'CREATED_AT' => 'created_at',
            'UPDATED_AT' => 'updated_at',
        ];

        $this->assertHasConstants($expectedConstants);
    }
}
