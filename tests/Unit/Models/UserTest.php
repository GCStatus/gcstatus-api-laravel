<?php

namespace Tests\Unit\Models;

use App\Models\User;
use Illuminate\Notifications\Notifiable;
use Tests\Contracts\Models\BaseModelTesting;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\{BelongsTo, HasOne, MorphMany};
use Tests\Contracts\Models\{
    ShouldTestCasts,
    ShouldTestTraits,
    ShouldTestFillables,
    ShouldTestRelations,
    ShouldTestInterfaces,
};

class UserTest extends BaseModelTesting implements
    ShouldTestCasts,
    ShouldTestTraits,
    ShouldTestFillables,
    ShouldTestRelations,
    ShouldTestInterfaces
{
    /**
     * The testable model string class.
     *
     * @return string-class
     */
    public function model(): string
    {
        return User::class;
    }

    /**
     * The fillable tests.
     *
     * @return void
     */
    public function test_fillable_attributes(): void
    {
        $fillable = [
            'name',
            'email',
            'blocked',
            'password',
            'level_id',
            'nickname',
            'birthdate',
            'experience',
        ];

        $this->assertHasFillables($fillable);
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
            'password' => 'hashed',
            'email_verified_at' => 'datetime',
        ];

        $this->assertHasCasts($casts);
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
            Notifiable::class,
        ];

        $this->assertUsesTraits($traits);
    }

    /**
     * The interfaces tests.
     *
     * @return void
     */
    public function test_interfaces_attributes(): void
    {
        $interfaces = [
            MustVerifyEmail::class,
        ];

        $this->assertUsesInterfaces($interfaces);
    }

    /**
     * The relations tests.
     *
     * @return void
     */
    public function test_relations_attributes(): void
    {
        $relations = [
            'wallet' => HasOne::class,
            'level' => BelongsTo::class,
            'notifications' => MorphMany::class,
            'readNotifications' => MorphMany::class,
            'unreadNotifications' => MorphMany::class,
        ];

        $this->assertHasRelations($relations);
    }
}
