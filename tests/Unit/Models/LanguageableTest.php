<?php

namespace Tests\Unit\Models;

use App\Models\{Game, Languageable};
use Tests\Contracts\Models\BaseModelTesting;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\{BelongsTo, MorphTo};
use Tests\Contracts\Models\{
    ShouldTestCasts,
    ShouldTestTraits,
    ShouldTestConstants,
    ShouldTestFillables,
    ShouldTestRelations,
};

class LanguageableTest extends BaseModelTesting implements
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
        return Languageable::class;
    }

    /**
     * The fillable tests.
     *
     * @return void
     */
    public function test_fillable_attributes(): void
    {
        $fillable = [
            'menu',
            'dubs',
            'subtitles',
            'language_id',
            'languageable_id',
            'languageable_type',
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
            'menu' => 'bool',
            'dubs' => 'bool',
            'subtitles' => 'bool',
        ];

        $this->assertHasCasts($casts);
    }

    /**
     * The contract constant attributes that should be tested.
     *
     * @return void
     */
    public function test_constant_attributes(): void
    {
        $expectedConstants = [
            'ALLOWED_LANGUAGEABLE_TYPES' => [
                Game::class,
            ],
            'CREATED_AT' => 'created_at',
            'UPDATED_AT' => 'updated_at',
        ];

        $this->assertHasConstants($expectedConstants);
    }

    /**
     * The relations tests.
     *
     * @return void
     */
    public function test_relations_attributes(): void
    {
        $relations = [
            'language' => BelongsTo::class,
            'languageable' => MorphTo::class,
        ];

        $this->assertHasRelations($relations);
    }
}
