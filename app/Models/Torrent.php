<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Torrent extends Model
{
    /** @use HasFactory<\Database\Factories\TorrentFactory> */
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'url',
        'game_id',
        'posted_at',
        'torrent_provider_id',
    ];

    /**
     * The attributes that should be casts.
     *
     * @return array<string, string>
     */
    public function casts(): array
    {
        return [
            'posted_at' => 'date',
        ];
    }

    /**
     * Get the game that owns the Torrent
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo<Game, $this>
     */
    public function game(): BelongsTo
    {
        return $this->belongsTo(Game::class);
    }

    /**
     * Get the provider that owns the Torrent
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo<TorrentProvider, $this>
     */
    public function provider(): BelongsTo
    {
        return $this->belongsTo(TorrentProvider::class, 'torrent_provider_id');
    }
}
