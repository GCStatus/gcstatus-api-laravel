<?php

namespace App\Traits;

trait HasRoles
{
    /**
     * Check if user has given role.
     *
     * @param mixed $roleId
     * @return bool
     */
    public function hasRole(mixed $roleId): bool
    {
        return $this->roles()->where('role_id', $roleId)->exists();
    }

    /**
     * Check if user has all given roles.
     *
     * @param list<int> $roles
     * @return bool
     */
    public function hasAllRoles(array $roles): bool
    {
        return $this->roles()->whereIn('role_id', $roles)->count() === count($roles);
    }

    /**
     * Check if user has one of given roles.
     *
     * @param list<int> $roles
     * @return bool
     */
    public function hasOneOfRoles(array $roles): bool
    {
        return $this->roles()->whereIn('role_id', $roles)->exists();
    }
}
