<?php

namespace Tests\Unit\Models;

use App\Models\User;
use Tymon\JWTAuth\Contracts\JWTSubject;
use Illuminate\Notifications\Notifiable;
use App\Traits\{HasRoles, HasPermissions};
use Tests\Contracts\Models\BaseModelTesting;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Contracts\HasRolesAndPermissionsInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\{
    HasOne,
    HasMany,
    MorphMany,
    BelongsTo,
    MorphToMany,
    BelongsToMany,
};
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
     * @return class-string
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
            'deleted_at' => 'datetime',
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
            HasRoles::class,
            HasFactory::class,
            Notifiable::class,
            SoftDeletes::class,
            HasPermissions::class,
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
            JWTSubject::class,
            MustVerifyEmail::class,
            HasRolesAndPermissionsInterface::class,
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
            'title' => HasOne::class,
            'wallet' => HasOne::class,
            'profile' => HasOne::class,
            'level' => BelongsTo::class,
            'roles' => MorphToMany::class,
            'friendships' => HasMany::class,
            'titles' => BelongsToMany::class,
            'transactions' => HasMany::class,
            'sentRequests' => HasMany::class,
            'friends' => BelongsToMany::class,
            'missions' => BelongsToMany::class,
            'notifications' => MorphMany::class,
            'permissions' => MorphToMany::class,
            'receivedRequests' => HasMany::class,
            'readNotifications' => MorphMany::class,
            'unreadNotifications' => MorphMany::class,
        ];

        $this->assertHasRelations($relations);
    }
}
