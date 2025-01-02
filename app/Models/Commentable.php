<?php

namespace App\Models;

use Illuminate\Database\Eloquent\{Model, SoftDeletes};
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\{
    MorphTo,
    HasMany,
    BelongsTo,
    MorphMany,
};

class Commentable extends Model
{
    /** @use HasFactory<\Database\Factories\CommentableFactory> */
    use HasFactory;

    use SoftDeletes;

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
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany<Heartable, $this>
     */
    public function hearts(): MorphMany
    {
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
