<?php

namespace App\Models;

use Illuminate\Database\Eloquent\{Model, SoftDeletes};
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\{
    HasMany,
    BelongsTo,
    BelongsToMany,
    HasManyThrough,
};

class Mission extends Model
{
    /** @use HasFactory<\Database\Factories\MissionFactory> */
    use HasFactory;
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'coins',
        'mission',
        'for_all',
        'frequency',
        'status_id',
        'experience',
        'description',
    ];

    /**
     * Get the status that owns the Mission
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo<Status, $this>
     */
    public function status(): BelongsTo
    {
        return $this->belongsTo(Status::class);
    }

    /**
     * Get all of the requirements for the Mission
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany<MissionRequirement, $this>
     */
    public function requirements(): HasMany
    {
        return $this->hasMany(MissionRequirement::class);
    }

    /**
     * Get all of the progresses for the Mission
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasManyThrough<UserMissionProgress, MissionRequirement, $this>
     */
    public function progresses(): HasManyThrough
    {
        return $this->hasManyThrough(UserMissionProgress::class, MissionRequirement::class);
    }

    /**
     * The users that belong to the User
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany<User, $this>
     */
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class)->using(MissionUser::class);
    }
}
