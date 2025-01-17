<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Crack extends Model
{
    /** @use HasFactory<\Database\Factories\CrackFactory> */
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
        'cracker_id',
        'protection_id',
    ];

    /**
     * The relationships that should always be loaded.
     *
     * @var list<string>
     */
    protected $with = [
        'status',
        'cracker',
        'protection',
    ];

    /**
     * The attributes that should be casts.
     *
     * @return array<string, string>
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
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo<Game, $this>
     */
    public function game(): BelongsTo
    {
        return $this->belongsTo(Game::class);
    }

    /**
     * Get the status that owns the Crack
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo<Status, $this>
     */
    public function status(): BelongsTo
    {
        return $this->belongsTo(Status::class);
    }

    /**
     * Get the cracker that owns the Crack
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo<Cracker, $this>
     */
    public function cracker(): BelongsTo
    {
        return $this->belongsTo(Cracker::class);
    }

    /**
     * Get the protection that owns the Crack
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo<Protection, $this>
     */
    public function protection(): BelongsTo
    {
        return $this->belongsTo(Protection::class);
    }
}
