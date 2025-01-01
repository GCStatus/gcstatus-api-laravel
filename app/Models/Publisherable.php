<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\{MorphTo, BelongsTo};

class Publisherable extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'publisher_id',
        'publisherable_id',
        'publisherable_type',
    ];

    /**
     * Get the publisherable for the Publisherable.
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphTo<Model, $this>
     */
    public function publisherable(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Get the publisher that owns the Publisherable
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo<Publisher, $this>
     */
    public function publisher(): BelongsTo
    {
        return $this->belongsTo(Publisher::class);
    }
}
