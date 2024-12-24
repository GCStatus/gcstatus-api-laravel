<?php

namespace App\Models;

use App\Support\Database\CacheQueryBuilder;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\{Model, SoftDeletes};
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Rewardable extends Model
{
    /** @use HasFactory<\Database\Factories\RewardableFactory> */
    use HasFactory;
    use SoftDeletes;
    use CacheQueryBuilder;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'sourceable_id',
        'rewardable_id',
        'sourceable_type',
        'rewardable_type',
    ];

    /**
     * Get the sourceable for Rewardable.
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphTo<Model, $this>
     */
    public function sourceable(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Get the rewardable for Rewardable.
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphTo<Model, $this>
     */
    public function rewardable(): MorphTo
    {
        return $this->morphTo();
    }
}
