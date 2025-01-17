<?php

namespace App\Models;

use App\Traits\HasSlug;
use Illuminate\Database\Eloquent\{Model, SoftDeletes};
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\{BelongsTo, MorphMany, MorphToMany};

class Dlc extends Model
{
    /** @use HasFactory<\Database\Factories\DlcFactory> */
    use HasFactory;

    use HasSlug;
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'slug',
        'free',
        'cover',
        'about',
        'legal',
        'title',
        'game_id',
        'description',
        'release_date',
        'short_description',
    ];

    /**
     * The sluggable attribute for the dlc.
     *
     * @var string
     */
    protected $sluggable = 'title';

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
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo<Game, $this>
     */
    public function game(): BelongsTo
    {
        return $this->belongsTo(Game::class);
    }

    /**
     * Get all of the categories for the DLC
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphToMany<Category, $this>
     */
    public function categories(): MorphToMany
    {
        return $this->morphToMany(Category::class, 'categoriable');
    }

    /**
     * Get all of the platforms for the DLC
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphToMany<Platform, $this>
     */
    public function platforms(): MorphToMany
    {
        return $this->morphToMany(Platform::class, 'platformable');
    }

    /**
     * Get all of the tags for the DLC
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphToMany<Tag, $this>
     */
    public function tags(): MorphToMany
    {
        return $this->morphToMany(Tag::class, 'taggable');
    }

    /**
     * Get all of the genres for the DLC
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphToMany<Genre, $this>
     */
    public function genres(): MorphToMany
    {
        return $this->morphToMany(Genre::class, 'genreable');
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
     * @return \Illuminate\Database\Eloquent\Relations\MorphToMany<Developer, $this>
     */
    public function developers(): MorphToMany
    {
        return $this->morphToMany(Developer::class, 'developerable');
    }

    /**
     * Get all of the publishers for the DLC
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphToMany<Publisher, $this>
     */
    public function publishers(): MorphToMany
    {
        return $this->morphToMany(Publisher::class, 'publisherable');
    }
}
