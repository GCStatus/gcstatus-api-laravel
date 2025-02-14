<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\{MorphTo, BelongsTo};

class Roleable extends Model
{
    /** @use HasFactory<\Database\Factories\RoleableFactory> */
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'role_id',
        'roleable_id',
        'roleable_type',
    ];

    /**
     * Get the default relations to be loaded.
     *
     * @var list<string>
     */
    protected $with = [
        'role',
    ];

    /**
     * Get the morph roleable for the Role.
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphTo<Model, $this>
     */
    public function roleable(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Get the role that owns the Roleable
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo<Role, $this>
     */
    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class);
    }
}
