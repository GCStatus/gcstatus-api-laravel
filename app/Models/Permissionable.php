<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\{BelongsTo, MorphTo};

class Permissionable extends Model
{
    /** @use HasFactory<\Database\Factories\PermissionableFactory> */
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'permission_id',
        'permissionable_id',
        'permissionable_type',
    ];

    /**
     * Get the default relations to be loaded.
     *
     * @var list<string>
     */
    protected $with = [
        'permission',
    ];

    /**
     * Get the morph permissionable for the permission.
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphTo<Model, $this>
     */
    public function permissionable(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Get the permission that owns the permissionable
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo<Permission, $this>
     */
    public function permission(): BelongsTo
    {
        return $this->belongsTo(Permission::class);
    }
}
