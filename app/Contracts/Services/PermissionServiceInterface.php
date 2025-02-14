<?php

namespace App\Contracts\Services;

use App\Models\User;

interface PermissionServiceInterface
{
    /**
     * Check if user has all given permissions.
     *
     * @param \App\Models\User $user
     * @param list<string> $permissions
     * @return bool
     */
    public function hasAllPermissions(User $user, array $permissions): bool;
}
