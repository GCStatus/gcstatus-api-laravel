<?php

namespace App\Models;

use App\Traits\NormalizeMorphAdmin;
use Illuminate\Database\Eloquent\{Model, SoftDeletes};
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\{BelongsTo, MorphTo};

class Reviewable extends Model
{
    /** @use HasFactory<\Database\Factories\ReviewableFactory> */
    use HasFactory;

    use SoftDeletes;
    use NormalizeMorphAdmin;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'rate',
        'review',
        'user_id',
        'consumed',
        'reviewable_id',
        'reviewable_type',
    ];

    /**
     * The morphable attribute.
     *
     * @var string
     */
    protected $morphableAttribute = 'reviewable_type';

    /**
     * The attributes that should be casts.
     *
     * @return array<string, string>
     */
    public function casts(): array
    {
        return [
            'rate' => 'float',
            'consumed' => 'bool',
        ];
    }

    /**
     * Get the reviewable for the Reviewable.
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphTo<Model, $this>
     */
    public function reviewable(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Get the user that owns the Reviewable
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
