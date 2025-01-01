<?php

namespace App\Models;

use App\Traits\HasSlug;
use Illuminate\Database\Eloquent\{Model, SoftDeletes};
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\{BelongsTo, MorphMany};

class Dlc extends Model
{
    use HasSlug;
    use HasFactory;
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'slug',
        'free',
        'cover',
        'about',
        'legal',
        'game_id',
        'description',
        'release_date',
        'short_description',
    ];

    /**
     * The attributes that should be casts.
     *
     * @return array<string, string>
     */
    public function casts(): array
    {
        return [
            'free' => 'bool',
            'release_date' => 'date',
        ];
    }

    /**
     * Get the game that owns the Dlc
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function game(): BelongsTo
    {
        return $this->belongsTo(Game::class);
    }

    /**
     * Get all of the categories for the DLC
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany<Categoriable, $this>
     */
    public function categories(): MorphMany
    {
        return $this->morphMany(Categoriable::class, 'categoriable');
    }

    /**
     * Get all of the platforms for the DLC
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany<Platformable, $this>
     */
    public function platforms(): MorphMany
    {
        return $this->morphMany(Platformable::class, 'platformable');
    }

    /**
     * Get all of the tags for the DLC
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany<Taggable, $this>
     */
    public function tags(): MorphMany
    {
        return $this->morphMany(Taggable::class, 'taggable');
    }

    /**
     * Get all of the genres for the DLC
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany<Genreable, $this>
     */
    public function genres(): MorphMany
    {
        return $this->morphMany(Genreable::class, 'genreable');
    }

    /**
     * Get all of the galleries for the DLC
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany<Galleriable, $this>
     */
    public function galleries(): MorphMany
    {
        return $this->morphMany(Galleriable::class, 'galleriable');
    }

    /**
     * Get all of the stores for the DLC
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany<Storeable, $this>
     */
    public function stores(): MorphMany
    {
        return $this->morphMany(Storeable::class, 'storeable');
    }

    /**
     * Get all of the developers for the DLC
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany<Developerable, $this>
     */
    public function developers(): MorphMany
    {
        return $this->morphMany(Developerable::class, 'developerable');
    }

    /**
     * Get all of the publishers for the DLC
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany<Publisherable, $this>
     */
    public function publishers(): MorphMany
    {
        return $this->morphMany(Publisherable::class, 'publisherable');
    }
}
