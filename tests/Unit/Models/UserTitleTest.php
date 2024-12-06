<?php

namespace Tests\Unit\Models;

use App\Models\UserTitle;
use Tests\Contracts\Models\BaseModelTesting;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Tests\Contracts\Models\{
    ShouldTestFillables,
    ShouldTestRelations,
};

class UserTitleTest extends BaseModelTesting implements
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
        return UserTitle::class;
    }

    /**
     * The fillable tests.
     *
     * @return void
     */
    public function test_fillable_attributes(): void
    {
        $fillable = [
            'enabled',
            'user_id',
            'title_id',
        ];

        $this->assertHasFillables($fillable);
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
            'title' => BelongsTo::class,
        ];

        $this->assertHasRelations($relations);
    }
}
