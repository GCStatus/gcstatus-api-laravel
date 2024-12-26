<?php

namespace App\Models;

use Illuminate\Database\Eloquent\{Model, SoftDeletes};
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\{HasMany, MorphMany};

class Level extends Model
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory;
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'level',
        'coins',
        'experience',
    ];

    /**
     * Get all of the users for the Level
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany<User, $this>
     */
    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    /**
     * Get all of the rewards for the Mission
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany<Rewardable, $this>
     */
    public function rewards(): MorphMany
    {
        return $this->morphMany(Rewardable::class, 'sourceable');
    }
}
