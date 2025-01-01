<?php

namespace App\Models;

use App\Traits\HasSlug;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\{Model, SoftDeletes};
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Language extends Model
{
    use HasSlug;
    use HasFactory;
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'iso',
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
     * Get all of the languageables for the Language
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function languageables(): HasMany
    {
        return $this->hasMany(Language::class);
    }
}
