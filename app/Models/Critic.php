<?php

namespace App\Models;

use App\Traits\HasSlug;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\{Model, SoftDeletes};
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Critic extends Model
{
    /** @use HasFactory<\Database\Factories\CriticFactory> */
    use HasFactory;

    use HasSlug;
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'url',
        'name',
        'slug',
        'acting',
    ];

    /**
     * The sluggable attribute for the cracker.
     *
     * @var string
     */
    protected $sluggable = 'name';

    /**
     * The attributes that should be casts.
     *
     * @return array<string, string>
     */
    public function casts(): array
    {
        return [
            'acting' => 'bool',
        ];
    }

    /**
     * Get all of the criticables for the Critic
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany<Criticable, $this>
     */
    public function criticables(): HasMany
    {
        return $this->hasMany(Criticable::class);
    }
}
