<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\{MorphTo, BelongsTo};

class Taggable extends Model
{
    /** @use HasFactory<\Database\Factories\TaggableFactory> */
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'tag_id',
        'taggable_id',
        'taggable_type',
    ];

    /**
     * Get the taggable for the Taggable.
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphTo<Model, $this>
     */
    public function taggable(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Get the tag that owns the Taggable
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo<Tag, $this>
     */
    public function tag(): BelongsTo
    {
        return $this->belongsTo(Tag::class);
    }
}
