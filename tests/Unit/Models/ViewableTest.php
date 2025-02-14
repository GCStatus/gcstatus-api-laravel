<?php

namespace Tests\Unit\Models;

use App\Models\Viewable;
use Tests\Contracts\Models\BaseModelTesting;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\{BelongsTo, MorphTo};
use Tests\Contracts\Models\{
    ShouldTestTraits,
    ShouldTestFillables,
    ShouldTestRelations,
};

class ViewableTest extends BaseModelTesting implements
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
        return Viewable::class;
    }

    /**
     * The fillable tests.
     *
     * @return void
     */
    public function test_fillable_attributes(): void
    {
        $fillable = [
            'user_id',
            'viewable_id',
            'viewable_type',
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
            'user' => BelongsTo::class,
            'viewable' => MorphTo::class,
        ];

        $this->assertHasRelations($relations);
    }
}
