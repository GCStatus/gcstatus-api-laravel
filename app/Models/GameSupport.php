<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class GameSupport extends Model
{
    /** @use HasFactory<\Database\Factories\GameSupportFactory> */
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'url',
        'email',
        'contact',
        'game_id',
    ];

    /**
     * Get the game that owns the GameSupport
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo<Game, $this>
     */
    public function game(): BelongsTo
    {
        return $this->belongsTo(Game::class);
    }
}
