<?php

namespace Tests\Unit\Models;

use App\Models\Developerable;
use Tests\Contracts\Models\BaseModelTesting;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\{BelongsTo, MorphTo};
use Tests\Contracts\Models\{
    ShouldTestTraits,
    ShouldTestFillables,
    ShouldTestRelations,
};

class DeveloperableTest extends BaseModelTesting implements
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
        return Developerable::class;
    }

    /**
     * The fillable tests.
     *
     * @return void
     */
    public function test_fillable_attributes(): void
    {
        $fillable = [
            'developer_id',
            'developerable_id',
            'developerable_type',
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
            'developer' => BelongsTo::class,
            'developerable' => MorphTo::class,
        ];

        $this->assertHasRelations($relations);
    }
}
