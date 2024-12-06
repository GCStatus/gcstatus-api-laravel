<?php

namespace App\Models;

use Illuminate\Database\Eloquent\{Model, SoftDeletes};
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\{BelongsTo, HasMany};

class SocialAccount extends Model
{
    /** @use HasFactory<\Database\Factories\SocialAccountFactory> */
    use HasFactory;
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'user_id',
        'provider',
        'provider_id',
    ];

    /**
     * Get the user that owns the SocialAccount
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get all of the scopes for the User
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany<SocialScope, $this>
     */
    public function scopes(): HasMany
    {
        return $this->hasMany(SocialScope::class);
    }
}
