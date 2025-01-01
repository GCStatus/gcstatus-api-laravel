<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\{MorphTo, BelongsTo};

class Developerable extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'developer_id',
        'developerable_id',
        'developerable_type',
    ];

    /**
     * Get the developerable for the Developerable.
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphTo<Model, $this>
     */
    public function developerable(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Get the developer that owns the Developerable
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo<Developer, $this>
     */
    public function developer(): BelongsTo
    {
        return $this->belongsTo(Developer::class);
    }
}
