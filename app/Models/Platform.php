<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\{Model, SoftDeletes};
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Platform extends Model
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
     * Get all of the platformables for the Platform
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany<Platformable, $this>
     */
    public function platformables(): HasMany
    {
        return $this->hasMany(Platformable::class);
    }
}
