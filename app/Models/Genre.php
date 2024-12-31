<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\{Model, SoftDeletes};
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Genre extends Model
{
    use HasFactory;
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
    ];

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
