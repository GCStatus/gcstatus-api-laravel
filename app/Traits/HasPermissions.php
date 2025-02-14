<?php

namespace App\Traits;

use Illuminate\Support\Collection;

trait HasPermissions
{
    /**
     * Check if the user has a given permission scope.
     *
     * @param string $scope
     * @return bool
     */
    public function hasPermission(string $scope): bool
    {
        return $this->getAllPermissionScopes()->contains($scope);
    }

    /**
     * Check if the user has all given permission scopes.
     *
     * @param array<string> $scopes
     * @return bool
     */
    public function hasAllPermissions(array $scopes): bool
    {
        $userScopes = $this->getAllPermissionScopes()->toArray();

        return empty(array_diff($scopes, $userScopes));
    }

    /**
     * Check if the user has at least one of the given permission scopes.
     *
     * @param array<string> $scopes
     * @return bool
     */
    public function hasOneOfPermissions(array $scopes): bool
    {
        return $this->getAllPermissionScopes()->intersect($scopes)->isNotEmpty();
    }

    /**
     * Get all permission scopes assigned to the user, including role-based permissions.
     *
     * @return \Illuminate\Support\Collection<(int|string), mixed>
     */
    private function getAllPermissionScopes(): Collection
    {
        $this->loadMissing(['permissions', 'roles.permissions']);

        return $this->permissions
            ->pluck('scope')
            ->merge($this->roles->flatMap->permissions->pluck('scope'))
            ->unique();
    }
}
