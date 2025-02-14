<?php

namespace Tests\Traits;

use App\Models\Permission;

trait HasDummyPermission
{
    /**
     * Create dummy permission.
     *
     * @param array<string, mixed> $data
     * @return \App\Models\Permission
     */
    public function createDummyPermission(array $data = []): Permission
    {
        return Permission::factory()->create($data);
    }
}
