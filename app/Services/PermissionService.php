<?php

namespace App\Services;

use App\Models\{User, Role};
use App\Contracts\Services\PermissionServiceInterface;

class PermissionService implements PermissionServiceInterface
{
    /**
     * @inheritDoc
     */
    public function hasAllPermissions(User $user, array $permissions): bool
    {
        if ($user->hasRole(Role::TECHNOLOGY_ROLE_ID)) {
            return true;
        }

        return $user->hasAllPermissions($permissions);
    }
}
