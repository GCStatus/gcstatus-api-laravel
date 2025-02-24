<?php

namespace Tests\Unit\Models;

use App\Models\Status;
use App\Support\Database\CacheQueryBuilder;
use Tests\Contracts\Models\BaseModelTesting;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Tests\Contracts\Models\{
    ShouldTestCasts,
    ShouldTestTraits,
    ShouldTestConstants,
    ShouldTestFillables,
};

class StatusTest extends BaseModelTesting implements
    ShouldTestCasts,
    ShouldTestTraits,
    ShouldTestFillables,
    ShouldTestConstants
{
    /**
     * The testable model string class.
     *
     * @return class-string
     */
    public function model(): string
    {
        return Status::class;
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
     * The contract constant attributes that should be tested.
     *
     * @return void
     */
    public function test_constant_attributes(): void
    {
        $expectedConstants = [
            'AVAILABLE_STATUS_ID' => 1,
            'UNAVAILABLE_STATUS_ID' => 2,
            'CRACKED_STATUS_ID' => 3,
            'UNCRACKED_STATUS_ID' => 4,
            'CRACKED_ONEDAY_STATUS_ID' => 5,
            'CREATED_AT' => 'created_at',
            'UPDATED_AT' => 'updated_at',
            'TRANSLATE_TO_ID' => [
                'cracked' => 3,
                'uncracked' => 4,
                'cracked-oneday' => 5,
                null => 4,
            ],
        ];

        $this->assertHasConstants($expectedConstants);
    }
}
