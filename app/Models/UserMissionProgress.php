<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\{Model, SoftDeletes};
use Illuminate\Database\Eloquent\Factories\HasFactory;

class UserMissionProgress extends Model
{
    /** @use HasFactory<\Database\Factories\UserMissionProgressFactory> */
    use HasFactory;
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'user_id',
        'progress',
        'completed',
        'mission_requirement_id',
    ];

    /**
     * Get the user that owns the UserMissionProgress
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the missionRequirement that owns the UserMissionProgress
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo<MissionRequirement, $this>
     */
    public function missionRequirement(): BelongsTo
    {
        return $this->belongsTo(MissionRequirement::class);
    }
}
