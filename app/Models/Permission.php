<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\{Model, SoftDeletes};
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Permission extends Model
{
    /** @use HasFactory<\Database\Factories\PermissionFactory> */
    use HasFactory;

    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'scope',
    ];

    /**
     * Get all of the permissionables for the Permission
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany<Permissionable, $this>
     */
    public function permissionables(): MorphMany
    {
        return $this->morphMany(Permissionable::class, 'permissionable');
    }
}
