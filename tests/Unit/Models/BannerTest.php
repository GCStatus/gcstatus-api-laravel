<?php

namespace Tests\Unit\Models;

use App\Models\Banner;
use Tests\Contracts\Models\BaseModelTesting;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Tests\Contracts\Models\{
    ShouldTestTraits,
    ShouldTestFillables,
    ShouldTestRelations,
};

class BannerTest extends BaseModelTesting implements
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
        return Banner::class;
    }

    /**
     * The fillable tests.
     *
     * @return void
     */
    public function test_fillable_attributes(): void
    {
        $fillable = [
            'component',
            'bannerable_id',
            'bannerable_type',
        ];

        $this->assertHasFillables($fillable);
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
            'bannerable' => MorphTo::class,
        ];

        $this->assertHasRelations($relations);
    }
}
