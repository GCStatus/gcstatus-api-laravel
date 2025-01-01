<?php

namespace App\Models;

use App\Traits\HasSlug;
use Illuminate\Database\Eloquent\{Model, SoftDeletes};
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\{
    HasOne,
    HasMany,
    MorphMany,
};

class Game extends Model
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
        'age',
        'slug',
        'free',
        'title',
        'cover',
        'about',
        'legal',
        'website',
        'condition',
        'description',
        'release_date',
        'great_release',
        'short_description',
    ];

    /**
     * The attributes that should load with count.
     *
     * @var list<string>
     */
    protected $withCount = [
        'views',
        'hearts',
    ];

    /**
     * The sluggable attribute for the game.
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
            'great_release' => 'bool',
        ];
    }

    /**
     * Get all of the views for the Game
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany<Viewable, $this>
     */
    public function views(): MorphMany
    {
        return $this->morphMany(Viewable::class, 'viewable');
    }

    /**
     * Get all of the hearts for the Game
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany<Heartable, $this>
     */
    public function hearts(): MorphMany
    {
        return $this->morphMany(Heartable::class, 'heartable');
    }

    /**
     * Get all of the categories for the Game
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany<Categoriable, $this>
     */
    public function categories(): MorphMany
    {
        return $this->morphMany(Categoriable::class, 'categoriable');
    }

    /**
     * Get all of the platforms for the Game
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany<Platformable, $this>
     */
    public function platforms(): MorphMany
    {
        return $this->morphMany(Platformable::class, 'platformable');
    }

    /**
     * Get all of the tags for the Game
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany<Taggable, $this>
     */
    public function tags(): MorphMany
    {
        return $this->morphMany(Taggable::class, 'taggable');
    }

    /**
     * Get all of the genres for the Game
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany<Genreable, $this>
     */
    public function genres(): MorphMany
    {
        return $this->morphMany(Genreable::class, 'genreable');
    }

    /**
     * Get all of the galleries for the Game
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany<Galleriable, $this>
     */
    public function galleries(): MorphMany
    {
        return $this->morphMany(Galleriable::class, 'galleriable');
    }

    /**
     * Get all of the stores for the Game
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany<Storeable, $this>
     */
    public function stores(): MorphMany
    {
        return $this->morphMany(Storeable::class, 'storeable');
    }

    /**
     * Get all of the requirements for the Game
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany<Requirementable, $this>
     */
    public function requirements(): MorphMany
    {
        return $this->morphMany(Requirementable::class, 'requirementable');
    }

    /**
     * Get all of the comments for the Game
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany<Commentable, $this>
     */
    public function comments(): MorphMany
    {
        return $this->morphMany(Commentable::class, 'commentable');
    }

    /**
     * Get all of the developers for the Game
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany<Developerable, $this>
     */
    public function developers(): MorphMany
    {
        return $this->morphMany(Developerable::class, 'developerable');
    }

    /**
     * Get all of the publishers for the Game
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany<Commentable, $this>
     */
    public function publishers(): MorphMany
    {
        return $this->morphMany(Publisherable::class, 'publisherable');
    }

    /**
     * Get all of the reviews for the Game
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany<Reviewable, $this>
     */
    public function reviews(): MorphMany
    {
        return $this->morphMany(Reviewable::class, 'reviewable');
    }

    /**
     * Get all of the critics for the Game
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany<Criticable, $this>
     */
    public function critics(): MorphMany
    {
        return $this->morphMany(Criticable::class, 'criticable');
    }

    /**
     * Get all of the languages for the Game
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany<Languageable, $this>
     */
    public function languages(): MorphMany
    {
        return $this->morphMany(Languageable::class, 'languageable');
    }

    /**
     * Get the crack associated with the Game
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function crack(): HasOne
    {
        return $this->hasOne(Crack::class);
    }

    /**
     * Get the support associated with the Game
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function support(): HasOne
    {
        return $this->hasOne(GameSupport::class);
    }

    /**
     * Get all of the torrents for the Game
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function torrents(): HasMany
    {
        return $this->hasMany(Torrent::class);
    }

    /**
     * Get all of the dlcs for the Game
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function dlcs(): HasMany
    {
        return $this->hasMany(Dlc::class);
    }
}
