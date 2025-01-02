<?php

namespace App\Models;

use App\Traits\HasSlug;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\{Model, SoftDeletes};
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Category extends Model
{
    /** @use HasFactory<\Database\Factories\CategoryFactory> */
    use HasFactory;

    use HasSlug;
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'slug',
    ];

    /**
     * The sluggable attribute for category.
     *
     * @var string
     */
    protected $sluggable = 'name';

    /**
     * Get all of the categoriables for the Category
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany<Categoriable, $this>
     */
    public function categoriables(): HasMany
    {
        return $this->hasMany(Categoriable::class);
    }
}
