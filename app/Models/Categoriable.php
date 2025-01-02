<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\{MorphTo, BelongsTo};

class Categoriable extends Model
{
    /** @use HasFactory<\Database\Factories\CategoriableFactory> */
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'category_id',
        'categoriable_id',
        'categoriable_type',
    ];

    /**
     * Get the categoriable for the Categoriable.
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphTo<Model, $this>
     */
    public function categoriable(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Get the category that owns the Categoriable
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo<Category, $this>
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }
}
