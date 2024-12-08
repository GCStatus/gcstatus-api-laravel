<?php

namespace App\Models;

use Illuminate\Database\Eloquent\{Model, SoftDeletes};
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\{
    MorphOne,
    BelongsTo,
    BelongsToMany,
};

class Title extends Model
{
    /** @use HasFactory<\Database\Factories\TitleFactory> */
    use HasFactory;
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'cost',
        'title',
        'status_id',
        'description',
        'purchasable',
    ];

    /**
     * The relations that should be loaded by default.
     *
     * @var list<string>
     */
    protected $with = [
        'status',
    ];

    /**
     * The attributes that should be cast.
     *
     * @return array<string, string>
     */
    public function casts(): array
    {
        return [
            'purchasable' => 'bool',
        ];
    }

    /**
     * Get the status that owns the Title
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo<Status, $this>
     */
    public function status(): BelongsTo
    {
        return $this->belongsTo(Status::class);
    }

    /**
     * Get all of the rewardable for the Mission
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphOne<Rewardable, $this>
     */
    public function rewardable(): MorphOne
    {
        return $this->morphOne(Rewardable::class, 'rewardable');
    }

    /**
     * The users that belong to the Title
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany<User, $this>
     */
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'user_titles')
            ->using(UserTitle::class)
            ->withPivot('enabled')
            ->withTimestamps();
    }
}
