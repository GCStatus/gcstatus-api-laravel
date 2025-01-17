<?php

namespace App\Models;

use App\Traits\NormalizeMorphAdmin;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\{MorphTo, BelongsTo};

class Viewable extends Model
{
    /** @use HasFactory<\Database\Factories\ViewableFactory> */
    use HasFactory;
    use NormalizeMorphAdmin;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'user_id',
        'viewable_id',
        'viewable_type',
    ];

    /**
     * The morphable attribute.
     *
     * @var string
     */
    protected $morphableAttribute = 'viewable_type';

    /**
     * Get the viewable for the Viewable.
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphTo<Model, $this>
     */
    public function viewable(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Get the user that owns the Viewable
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
