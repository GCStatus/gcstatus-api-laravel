<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\{MorphTo, BelongsTo};

class Genreable extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'genre_id',
        'genreable_id',
        'genreable_type',
    ];

    /**
     * Get the genreable for the Genreable.
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphTo<Model, $this>
     */
    public function genreable(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Get the genre that owns the Genreable
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo<Genre, $this>
     */
    public function genre(): BelongsTo
    {
        return $this->belongsTo(Genre::class);
    }
}
