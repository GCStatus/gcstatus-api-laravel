<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\{Model, SoftDeletes};
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Tag extends Model
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
     * Get all of the taggables for the Tag
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany<Taggable, $this>
     */
    public function taggables(): HasMany
    {
        return $this->hasMany(Taggable::class);
    }
}
