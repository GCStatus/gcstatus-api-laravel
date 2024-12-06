<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\{Model, SoftDeletes};
use Illuminate\Database\Eloquent\Factories\HasFactory;

class SocialScope extends Model
{
    /** @use HasFactory<\Database\Factories\SocialScopeFactory> */
    use HasFactory;
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'scope',
        'social_account_id',
    ];

    /**
     * Get the account that owns the Scope
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo<SocialAccount, $this>
     */
    public function account(): BelongsTo
    {
        return $this->belongsTo(SocialAccount::class);
    }
}
