<?php

namespace Tests\Unit\Models;

use App\Models\{Dlc, Game, Galleriable};
use Tests\Contracts\Models\BaseModelTesting;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\{
    MorphTo,
    BelongsTo,
};
use Tests\Contracts\Models\{
    ShouldTestCasts,
    ShouldTestTraits,
    ShouldTestFillables,
    ShouldTestRelations,
    ShouldTestConstants,
};

class GalleriableTest extends BaseModelTesting implements
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
        return Galleriable::class;
    }

    /**
     * The fillable tests.
     *
     * @return void
     */
    public function test_fillable_attributes(): void
    {
        $fillable = [
            's3',
            'path',
            'media_type_id',
            'galleriable_id',
            'galleriable_type',
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
     * The casts tests.
     *
     * @return void
     */
    public function test_casts_attributes(): void
    {
        $casts = [
            'id' => 'int',
            's3' => 'bool',
        ];

        $this->assertHasCasts($casts);
    }

    /**
     * The relations tests.
     *
     * @return void
     */
    public function test_relations_attributes(): void
    {
        $relations = [
            'mediaType' => BelongsTo::class,
            'galleriable' => MorphTo::class,
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
            'ALLOWED_GALLERIABLES_TYPE' => [
                Dlc::class,
                Game::class,
            ],
            'CREATED_AT' => 'created_at',
            'UPDATED_AT' => 'updated_at',
        ];

        $this->assertHasConstants($expectedConstants);
    }
}
