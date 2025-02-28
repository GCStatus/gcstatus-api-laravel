<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\{MorphTo, BelongsTo};

class Storeable extends Model
{
    /** @use HasFactory<\Database\Factories\StoreableFactory> */
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'url',
        'price',
        'store_id',
        'storeable_id',
        'store_item_id',
        'storeable_type',
    ];

    /**
     * The attributes that should be casts.
     *
     * @return array<string, string>
     */
    public function casts(): array
    {
        return [
            'price' => 'int',
        ];
    }

    /**
     * Get the storeable for the Storeable.
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphTo<Model, $this>
     */
    public function storeable(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Get the store that owns the Storeable
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo<Store, $this>
     */
    public function store(): BelongsTo
    {
        return $this->belongsTo(Store::class);
    }
}
