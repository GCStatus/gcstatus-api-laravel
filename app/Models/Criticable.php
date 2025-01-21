<?php

namespace App\Models;

use App\Traits\NormalizeMorphAdmin;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\{MorphTo, BelongsTo};

class Criticable extends Model
{
    /** @use HasFactory<\Database\Factories\CriticableFactory> */
    use HasFactory;

    use NormalizeMorphAdmin;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'url',
        'rate',
        'posted_at',
        'critic_id',
        'criticable_id',
        'criticable_type',
    ];

    /**
     * The morphable attribute.
     *
     * @var string
     */
    protected $morphableAttribute = 'criticable_type';

    /**
     * The attributes that should be casts.
     *
     * @return array<string, string>
     */
    public function casts(): array
    {
        return [
            'rate' => 'float',
            'posted_at' => 'datetime',
        ];
    }

    /**
     * Get the criticable for the Criticable.
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphTo<Model, $this>
     */
    public function criticable(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Get the critic that owns the Criticable
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo<Critic, $this>
     */
    public function critic(): BelongsTo
    {
        return $this->belongsTo(Critic::class);
    }
}
