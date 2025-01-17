<?php

namespace App\Models;

use App\Traits\{HasSlug, HasHeart};
use App\Contracts\HasHeartInterface;
use Illuminate\Database\Eloquent\{Model, SoftDeletes};
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\{
    HasOne,
    HasMany,
    MorphMany,
    MorphToMany,
};

/**
 * @implements HasHeartInterface<Game>
 */
class Game extends Model implements HasHeartInterface
{
    /** @use HasFactory<\Database\Factories\GameFactory> */
    use HasFactory;

    use HasSlug;
    use HasHeart;
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
     * The relationships that should always be loaded.
     *
     * @var list<string>
     */
    protected $with = [
        'tags',
        'crack',
        'genres',
        'platforms',
        'categories',
    ];

    /**
     * The sluggable attribute for the game.
     *
     * @var string
     */
    protected $sluggable = 'title';

    /**
     * The game hot condition.
     *
     * @var string
     */
    public const HOT_CONDITION = 'hot';

    /**
     * The game sale condition.
     *
     * @var string
     */
    public const SALE_CONDITION = 'sale';

    /**
     * The game popular condition.
     *
     * @var string
     */
    public const POPULAR_CONDITION = 'popular';

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
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany<Heartable, Game>
     */
    public function hearts(): MorphMany
    {
        /** @var \Illuminate\Database\Eloquent\Relations\MorphMany<Heartable, Game> */
        return $this->morphMany(Heartable::class, 'heartable');
    }

    /**
     * Get all categories for the Game.
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphToMany<Category, $this>
     */
    public function categories(): MorphToMany
    {
        return $this->morphToMany(Category::class, 'categoriable');
    }

    /**
     * Get all platforms for the Game.
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphToMany<Platform, $this>
     */
    public function platforms(): MorphToMany
    {
        return $this->morphToMany(Platform::class, 'platformable');
    }

    /**
     * Get all of the tags for the Game
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphToMany<Tag, $this>
     */
    public function tags(): MorphToMany
    {
        return $this->morphToMany(Tag::class, 'taggable');
    }

    /**
     * Get all of the genres for the Game
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphToMany<Genre, $this>
     */
    public function genres(): MorphToMany
    {
        return $this->morphToMany(Genre::class, 'genreable');
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
     * Get all of the top-level comments for the Game
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany<Commentable, $this>
     */
    public function comments(): MorphMany
    {
        return $this->morphMany(Commentable::class, 'commentable')->whereNull('parent_id');
    }

    /**
     * Get all of the developers for the Game
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphToMany<Developer, $this>
     */
    public function developers(): MorphToMany
    {
        return $this->morphToMany(Developer::class, 'developerable');
    }

    /**
     * Get all of the publishers for the Game
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphToMany<Publisher, $this>
     */
    public function publishers(): MorphToMany
    {
        return $this->morphToMany(Publisher::class, 'publisherable');
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
     * @return \Illuminate\Database\Eloquent\Relations\HasOne<Crack, $this>
     */
    public function crack(): HasOne
    {
        return $this->hasOne(Crack::class);
    }

    /**
     * Get the support associated with the Game
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne<GameSupport, $this>
     */
    public function support(): HasOne
    {
        return $this->hasOne(GameSupport::class);
    }

    /**
     * Get all of the torrents for the Game
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany<Torrent, $this>
     */
    public function torrents(): HasMany
    {
        return $this->hasMany(Torrent::class);
    }

    /**
     * Get all of the dlcs for the Game
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany<Dlc, $this>
     */
    public function dlcs(): HasMany
    {
        return $this->hasMany(Dlc::class);
    }
}
