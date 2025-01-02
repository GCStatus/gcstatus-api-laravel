<?php

namespace App\Models;

use App\Support\Database\CacheQueryBuilder;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\{Model, SoftDeletes};
use Illuminate\Database\Eloquent\Factories\HasFactory;

class MediaType extends Model
{
    /** @use HasFactory<\Database\Factories\MediaTypeFactory> */
    use HasFactory;

    use SoftDeletes;
    use CacheQueryBuilder;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
    ];

    /**
     * The photo const id.
     *
     * @var int
     */
    public const PHOTO_CONST_ID = 1;

    /**
     * The video const id.
     *
     * @var int
     */
    public const VIDEO_CONST_ID = 2;

    /**
     * Get all of the galleries for the MediaType
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany<Galleriable, $this>
     */
    public function galleries(): HasMany
    {
        return $this->hasMany(Galleriable::class);
    }
}
