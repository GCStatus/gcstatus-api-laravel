<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Crack extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'game_id',
        'status_id',
        'cracked_at',
        'protection_id',
    ];

    /**
     * The attributes that should be casts.
     *
     * @var array<string, string>
     */
    public function casts(): array
    {
        return [
            'cracked_at' => 'date',
        ];
    }

    /**
     * Get the game that owns the Crack
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function game(): BelongsTo
    {
        return $this->belongsTo(Game::class);
    }

    /**
     * Get the status that owns the Crack
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function status(): BelongsTo
    {
        return $this->belongsTo(Status::class);
    }

    /**
     * Get the protection that owns the Crack
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function protection(): BelongsTo
    {
        return $this->belongsTo(Protection::class);
    }
}