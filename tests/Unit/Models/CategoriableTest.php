<?php

namespace Tests\Unit\Models;

use App\Models\Categoriable;
use Tests\Contracts\Models\BaseModelTesting;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\{BelongsTo, MorphTo};
use Tests\Contracts\Models\{
    ShouldTestTraits,
    ShouldTestFillables,
    ShouldTestRelations,
};

class CategoriableTest extends BaseModelTesting implements
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
        return Categoriable::class;
    }

    /**
     * The fillable tests.
     *
     * @return void
     */
    public function test_fillable_attributes(): void
    {
        $fillable = [
            'category_id',
            'categoriable_id',
            'categoriable_type',
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
            'category' => BelongsTo::class,
            'categoriable' => MorphTo::class,
        ];

        $this->assertHasRelations($relations);
    }
}
