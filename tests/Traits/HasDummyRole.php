<?php

namespace Tests\Traits;

use App\Models\Role;

trait HasDummyRole
{
    /**
     * Create dummy role.
     *
     * @param array<string, mixed> $data
     * @return \App\Models\Role
     */
    public function createDummyRole(array $data = []): Role
    {
        return Role::factory()->create($data);
    }
}
