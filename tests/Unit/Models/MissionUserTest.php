<?php

namespace Tests\Unit\Models;

use App\Models\MissionUser;
use App\Support\Database\CacheQueryBuilder;
use Tests\Contracts\Models\BaseModelTesting;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Tests\Contracts\Models\{
    ShouldTestFillables,
    ShouldTestRelations,
    ShouldTestTraits,
};

class MissionUserTest extends BaseModelTesting implements
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
        return MissionUser::class;
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
            'mission_id',
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
            'user' => BelongsTo::class,
            'mission' => BelongsTo::class,
        ];

        $this->assertHasRelations($relations);
    }
}
