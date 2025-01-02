<?php

namespace App\Models;

use App\Traits\HasSlug;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\{Model, SoftDeletes};
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Genre extends Model
{
    /** @use HasFactory<\Database\Factories\GenreFactory> */
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
     * The sluggable attribute for store.
     *
     * @var string
     */
    protected $sluggable = 'name';

    /**
     * Get all of the genreables for the Genre
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany<Genreable, $this>
     */
    public function genreables(): HasMany
    {
        return $this->hasMany(Genreable::class);
    }
}
