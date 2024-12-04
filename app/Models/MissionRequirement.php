<?php

namespace App\Models;

use Illuminate\Database\Eloquent\{Model, SoftDeletes};
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\{BelongsTo, HasMany};

class MissionRequirement extends Model
{
    /** @use HasFactory<\Database\Factories\MissionRequirementFactory> */
    use HasFactory;
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'key',
        'task',
        'goal',
        'mission_id',
        'description',
    ];

    /**
     * Get the mission that owns the MissionRequirement
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo<Mission, $this>
     */
    public function mission(): BelongsTo
    {
        return $this->belongsTo(Mission::class);
    }

    /**
     * Get all of the progresses for the MissionRequirement
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany<UserMissionProgress, $this>
     */
    public function progresses(): HasMany
    {
        return $this->hasMany(UserMissionProgress::class);
    }
}
