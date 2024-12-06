<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\{Pivot, BelongsTo};

class MissionUser extends Pivot
{
    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'user_id',
        'mission_id',
    ];

    /**
     * The related pivot table.
     *
     * @var string
     */
    public $table = 'mission_users';

    /**
     * Get the user that owns the Missionuser
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the mission that owns the Missionuser
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo<Mission, $this>
     */
    public function mission(): BelongsTo
    {
        return $this->belongsTo(Mission::class);
    }
}
