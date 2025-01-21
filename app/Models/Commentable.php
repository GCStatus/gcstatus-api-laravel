<?php

namespace App\Models;

use App\Contracts\HasHeartInterface;
use App\Traits\{HasHeart, NormalizeMorphAdmin};
use Illuminate\Database\Eloquent\{Model, SoftDeletes};
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\{
    MorphTo,
    HasMany,
    BelongsTo,
    MorphMany,
};

/**
 * @implements HasHeartInterface<Commentable>
 */
class Commentable extends Model implements HasHeartInterface
{
    /** @use HasFactory<\Database\Factories\CommentableFactory> */
    use HasFactory;

    use HasHeart;
    use SoftDeletes;
    use NormalizeMorphAdmin;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'comment',
        'user_id',
        'parent_id',
        'commentable_id',
        'commentable_type',
    ];

    /**
     * The attributes that should load with count.
     *
     * @var list<string>
     */
    protected $withCount = [
        'hearts',
    ];

    /**
     * The morphable attribute.
     *
     * @var string
     */
    protected $morphableAttribute = 'commentable_type';

    /**
     * Get the commentable for the Commntable.
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphTo<Model, $this>
     */
    public function commentable(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Get the user that owns the Commntable
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get all of the hearts for the Commentable
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany<Heartable, Commentable>
     */
    public function hearts(): MorphMany
    {
        /** @var \Illuminate\Database\Eloquent\Relations\MorphMany<Heartable, Commentable> */
        return $this->morphMany(Heartable::class, 'heartable');
    }

    /**
     * Get the parent comment of this Comment (if any).
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo<Commentable, $this>
     */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    /**
     * Get the child comments for this Comment.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany<Commentable, $this>
     */
    public function children(): HasMany
    {
        return $this->hasMany(self::class, 'parent_id');
    }
}
