<?php

namespace Tests\Unit\Models;

use App\Models\Requirementable;
use App\Traits\NormalizeMorphAdmin;
use Tests\Contracts\Models\BaseModelTesting;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\{BelongsTo, MorphTo};
use Tests\Contracts\Models\{
    ShouldTestTraits,
    ShouldTestFillables,
    ShouldTestRelations,
};

class RequirementableTest extends BaseModelTesting implements
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
        return Requirementable::class;
    }

    /**
     * The fillable tests.
     *
     * @return void
     */
    public function test_fillable_attributes(): void
    {
        $fillable = [
            'os',
            'dx',
            'cpu',
            'gpu',
            'ram',
            'rom',
            'obs',
            'network',
            'requirementable_id',
            'requirement_type_id',
            'requirementable_type',
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
            NormalizeMorphAdmin::class,
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
            'requirementable' => MorphTo::class,
            'requirementType' => BelongsTo::class,
        ];

        $this->assertHasRelations($relations);
    }
}
