<?php

namespace App\Models;

use Illuminate\Database\Eloquent\{Model, SoftDeletes};
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\{
    HasOne,
    HasMany,
    BelongsTo,
    MorphMany,
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
        'reset_time',
        'description',
    ];

    /**
     * The relations that should be loaded by default.
     *
     * @var list<string>
     */
    protected $with = [
        'status',
    ];

    /**
     * The attributes that should be cast.
     *
     * @return array<string, string>
     */
    public function casts(): array
    {
        return [
            'for_all' => 'bool',
            'status_id' => 'int',
            'reset_time' => 'datetime',
        ];
    }

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
        return $this->belongsToMany(User::class, 'mission_users')->using(MissionUser::class);
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

    /**
     * Get the status for the authenticated user.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne<UserMission, $this>
     */
    public function userMission(): HasOne
    {
        return $this->hasOne(UserMission::class);
    }
}
