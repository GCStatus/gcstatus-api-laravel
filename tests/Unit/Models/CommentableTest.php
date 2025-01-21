<?php

namespace Tests\Unit\Models;

use App\Models\Commentable;
use App\Contracts\HasHeartInterface;
use Tests\Contracts\Models\BaseModelTesting;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\{HasHeart, NormalizeMorphAdmin};
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\{
    HasMany,
    MorphTo,
    BelongsTo,
    MorphMany,
};
use Tests\Contracts\Models\{
    ShouldTestCasts,
    ShouldTestTraits,
    ShouldTestFillables,
    ShouldTestRelations,
    ShouldTestInterfaces,
};

class CommentableTest extends BaseModelTesting implements
    ShouldTestCasts,
    ShouldTestTraits,
    ShouldTestFillables,
    ShouldTestRelations,
    ShouldTestInterfaces
{
    /**
     * The testable model string class.
     *
     * @return class-string
     */
    public function model(): string
    {
        return Commentable::class;
    }

    /**
     * The fillable tests.
     *
     * @return void
     */
    public function test_fillable_attributes(): void
    {
        $fillable = [
            'comment',
            'user_id',
            'parent_id',
            'commentable_id',
            'commentable_type',
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
            HasHeart::class,
            HasFactory::class,
            SoftDeletes::class,
            NormalizeMorphAdmin::class,
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
            'deleted_at' => 'datetime',
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
            'user' => BelongsTo::class,
            'hearts' => MorphMany::class,
            'parent' => BelongsTo::class,
            'children' => HasMany::class,
            'commentable' => MorphTo::class,
        ];

        $this->assertHasRelations($relations);
    }

    /**
     * The contract interfaces attributes that should be tested.
     *
     * @return void
     */
    public function test_interfaces_attributes(): void
    {
        $interfaces = [
            HasHeartInterface::class,
        ];

        $this->assertUsesInterfaces($interfaces);
    }
}
