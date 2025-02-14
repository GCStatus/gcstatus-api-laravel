<?php

namespace App\Contracts;

interface HasRolesAndPermissionsInterface
{
    /**
     * Check if the user has a given role.
     *
     * @param mixed $roleId
     * @return bool
     */
    public function hasRole(mixed $roleId): bool;

    /**
     * Check if the user has all given roles.
     *
     * @param list<int> $roles
     * @return bool
     */
    public function hasAllRoles(array $roles): bool;

    /**
     * Check if the user has one of the given roles.
     *
     * @param list<int> $roles
     * @return bool
     */
    public function hasOneOfRoles(array $roles): bool;

    /**
     * Check if the user has a given permission by scope.
     *
     * @param string $scope
     * @return bool
     */
    public function hasPermission(string $scope): bool;

    /**
     * Check if the user has all given permissions by scopes.
     *
     * @param list<string> $scopes
     * @return bool
     */
    public function hasAllPermissions(array $scopes): bool;

    /**
     * Check if the user has one of the given permissions by scopes.
     *
     * @param list<string> $scopes
     * @return bool
     */
    public function hasOneOfPermissions(array $scopes): bool;
}
