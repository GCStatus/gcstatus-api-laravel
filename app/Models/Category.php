<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\{Model, SoftDeletes};
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Category extends Model
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
     * Get all of the categoriables for the Category
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany<Categoriable, $this>
     */
    public function categoriables(): HasMany
    {
        return $this->hasMany(Categoriable::class);
    }
}