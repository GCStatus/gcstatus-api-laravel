<?php

namespace App\Models;

use Illuminate\Database\Eloquent\{Model, SoftDeletes};
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\{MorphMany, MorphToMany};

class Role extends Model
{
    /** @use HasFactory<\Database\Factories\RoleFactory> */
    use HasFactory;

    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
    ];

    /**
     * The technology role id.
     *
     * @var int
     */
    public const TECHNOLOGY_ROLE_ID = 1;

    /**
     * Get all of the permissions for the Role
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphToMany<Permission, $this>
     */
    public function permissions(): MorphToMany
    {
        return $this->morphToMany(Permission::class, 'permissionable');
    }

    /**
     * Get all of the roleables for the Permission
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany<Roleable, $this>
     */
    public function roleables(): MorphMany
    {
        return $this->morphMany(Roleable::class, 'roleable');
    }
}
