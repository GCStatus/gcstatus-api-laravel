<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\{MorphTo, BelongsTo};

class Galleriable extends Model
{
    /** @use HasFactory<\Database\Factories\GalleriableFactory> */
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        's3',
        'path',
        'media_type_id',
        'galleriable_id',
        'galleriable_type',
    ];

    /**
     * The attributes that should be casts.
     *
     * @return array<string, string>
     */
    public function casts(): array
    {
        return [
            's3' => 'bool',
        ];
    }

    /**
     * Get the galleriable for the Galleriable.
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphTo<Model, $this>
     */
    public function galleriable(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Get the mediaType that owns the Galleriable
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo<MediaType, $this>
     */
    public function mediaType(): BelongsTo
    {
        return $this->belongsTo(MediaType::class);
    }
}
